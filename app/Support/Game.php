<?php
namespace App\Support;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Game
{
    public static function today(): string
    {
        return now()->subHours(config('game.day_cutoff'))->toDateString();
    }

    public static function xpNeed(int $level): int { return (int) round(100 * $level ** 1.25); }
    public static function hpMax(int $level): int  { return 50 + 5 * ($level - 1); }

    public static function streakMult(int $streak): float
    {
        return 1 + config('game.streak_step') * min($streak, config('game.streak_cap'));
    }

    public static function award(User $u, int $xp, int $gold): int
    {
        $u->xp += $xp;
        $u->gold += $gold;
        $levels = 0;
        while ($u->xp >= self::xpNeed($u->level)) {
            $u->xp -= self::xpNeed($u->level);
            $u->level++;
            $levels++;
        }
        if ($levels) $u->hp = self::hpMax($u->level);   // левелап лечит
        return $levels;
    }

    public static function revoke(User $u, int $xp, int $gold): void
    {
        $u->xp -= $xp;
        $u->gold = max(0, $u->gold - $gold);
        while ($u->xp < 0 && $u->level > 1) {
            $u->level--;
            $u->xp += self::xpNeed($u->level);
        }
        $u->xp = max(0, $u->xp);
        $u->hp = min($u->hp, self::hpMax($u->level));
    }

    public static function damage(User $u, int $dmg): void
    {
        $u->hp = max(1, $u->hp - $dmg);   // мягкий режим: не убиваем
    }

    /** Чек-ин. Возвращает текст для флеша. */
    public static function tap(Habit $h, string $dir): string
    {
        return DB::transaction(function () use ($h, $dir) {
            $u = User::whereKey($h->user_id)->lockForUpdate()->first();
            $today = self::today();
            $log = HabitLog::firstOrNew(['habit_id' => $h->id, 'date' => $today]);
            $log->user_id = $u->id;
            $base = config('game.xp')[$h->difficulty];

            if ($h->type === 'daily') {
                if ($log->count) {                       // отмена
                    self::revoke($u, $log->xp, $log->gold);
                    $u->total_done = max(0, $u->total_done - 1);
                    $h->streak = max(0, $h->streak - 1);
                    $log->fill(['count' => 0, 'xp' => 0, 'gold' => 0])->save();
                    $u->save(); $h->save();
                    return 'Отменено';
                }
                $yesterday = Carbon::parse($today)->subDay()->toDateString();
                $h->streak = in_array($h->last_date, [$today, $yesterday], true) ? $h->streak + 1 : 1;
                $h->best_streak = max($h->best_streak, $h->streak);
                $h->last_date = $today;

                $xp = (int) round($base * self::streakMult($h->streak));
                $gold = (int) round($xp * config('game.gold_ratio'));
                $log->fill(['count' => 1, 'xp' => $xp, 'gold' => $gold])->save();
                $u->total_done++;
                $lv = self::award($u, $xp, $gold);
                $u->save(); $h->save();

                return $lv ? "⬆️ Уровень {$u->level}! +{$xp} XP" : "+{$xp} XP · +{$gold} 🪙";
            }

            if ($dir === 'minus') {
                $dmg = config('game.damage')[$h->difficulty];
                self::damage($u, $dmg);
                $log->neg = ($log->neg ?? 0) + 1;
                $log->save(); $u->save();
                return "−{$dmg} HP";
            }

            if (($log->count ?? 0) >= config('game.tap_limit')) return 'Лимит на сегодня 🙂';

            $xp = $base;
            $gold = (int) round($xp * config('game.gold_ratio'));
            $log->count = ($log->count ?? 0) + 1;
            $log->xp = ($log->xp ?? 0) + $xp;
            $log->gold = ($log->gold ?? 0) + $gold;
            $log->save();
            $u->total_done++;
            $lv = self::award($u, $xp, $gold);
            $u->save();

            return $lv ? "⬆️ Уровень {$u->level}! +{$xp} XP" : "+{$xp} XP · +{$gold} 🪙";
        });
    }

    /** Закрытие прошедших суток. Идемпотентно, вызывается из middleware. */
    public static function rollover(User $u): void
    {
        $today = self::today();
        if ($u->last_day === $today) return;

        $damage = DB::transaction(function () use ($u, $today) {
            $fresh = User::whereKey($u->id)->lockForUpdate()->first();
            if ($fresh->last_day === $today) return 0;

            $from = $fresh->last_day ?: Carbon::parse($today)->subDay()->toDateString();
            $gap = (int) Carbon::parse($from)->diffInDays(Carbon::parse($today));
            $dmg = 0;

            if ($gap > 7) {   // долго не заходил — не наказываем, просто гасим стрики
                $fresh->habits()->where('type', 'daily')->update(['streak' => 0]);
            } else {
                $dailies = $fresh->habits()->where('type', 'daily')->get();
                $cursor = Carbon::parse($from)->addDay();
                while ($cursor->toDateString() < $today) {
                    $date = $cursor->toDateString();
                    foreach ($dailies as $h) {
                        if (! $h->isScheduled($date)) continue;
                        if ($h->created_at->toDateString() > $date) continue;
                        if (HabitLog::where('habit_id', $h->id)->where('date', $date)->where('count', '>', 0)->exists()) continue;

                        if ($fresh->shields > 0) { $fresh->shields--; continue; }
                        $dmg += config('game.damage')[$h->difficulty];
                        $h->streak = 0;
                        $h->save();
                    }
                    $cursor->addDay();
                }
                $dmg = min($dmg, (int) round(self::hpMax($fresh->level) * 0.4));
            }

            if ($dmg) self::damage($fresh, $dmg);
            $fresh->last_day = $today;
            $fresh->save();

            return $dmg;
        });

        $u->refresh();
        if ($damage) session()->flash('status', "🌙 Новый день. Пропуски: −{$damage} HP");
    }

    public static function achievements(User $u): array
    {
        $max = (int) $u->habits()->max('best_streak');
        return [
            ['🎯', 'Первый шаг',  '1 выполнение',    $u->total_done >= 1],
            ['💯', 'Сотка',       '100 выполнений',  $u->total_done >= 100],
            ['🔥', 'Неделя огня', 'стрик 7',         $max >= 7],
            ['🌋', 'Месяц стали', 'стрик 30',        $max >= 30],
            ['⭐', 'Уровень 5',   '',                $u->level >= 5],
            ['👑', 'Уровень 10',  '',                $u->level >= 10],
            ['💰', 'Казна',       '500 золота',      $u->gold >= 500],
            ['🛡️', 'Страж',      '3 щита',          $u->shields >= 3],
        ];
    }
}
