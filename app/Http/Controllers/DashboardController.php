<?php
namespace App\Http\Controllers;

use App\Models\HabitLog;
use App\Support\Game;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $r)
    {
        $u = $r->user();
        $today = Game::today();
        $all = $u->habits()->orderBy('id')->get();

        $dailies = $all->where('type', 'daily')->filter(fn ($h) => $h->isScheduled($today))->values();
        $flex = $all->where('type', 'habit')->values();
        $logs = HabitLog::where('user_id', $u->id)->where('date', $today)->get()->keyBy('habit_id');
        $done = $dailies->filter(fn ($h) => ($logs->get($h->id)?->count ?? 0) > 0)->count();

        return view('dashboard', compact('u', 'today', 'dailies', 'flex', 'logs', 'done'));
    }
}
