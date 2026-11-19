<?php
namespace App\Http\Controllers;

use App\Models\HabitLog;
use App\Support\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StatsController extends Controller
{
    public function __invoke(Request $r)
    {
        $u = $r->user();
        $today = Game::today();
        $start = Carbon::parse($today)->subDays(83);

        $sums = HabitLog::where('user_id', $u->id)
            ->whereBetween('date', [$start->toDateString(), $today])
            ->selectRaw('date, SUM(count) as c')->groupBy('date')->pluck('c', 'date');

        $cells = [];
        for ($i = 0; $i < 84; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $cells[] = ['d' => $d, 'c' => (int) ($sums[$d] ?? 0)];
        }

        return view('stats', [
            'u' => $u,
            'cells' => $cells,
            'habits' => $u->habits()->orderByDesc('best_streak')->get(),
            'ach' => Game::achievements($u),
        ]);
    }
}
