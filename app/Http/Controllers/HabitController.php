<?php
namespace App\Http\Controllers;

use App\Models\Habit;
use App\Support\Game;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index(Request $r)
    {
        return view('habits', ['habits' => $r->user()->habits()->orderBy('id')->get()]);
    }

    public function store(Request $r)
    {
        $r->user()->habits()->create($this->data($r));
        return back()->with('status', 'Квест добавлен');
    }

    public function update(Request $r, Habit $habit)
    {
        abort_unless($habit->user_id === $r->user()->id, 403);
        $habit->update($this->data($r));
        return back()->with('status', 'Сохранено');
    }

    public function destroy(Request $r, Habit $habit)
    {
        abort_unless($habit->user_id === $r->user()->id, 403);
        $habit->delete();
        return back()->with('status', 'Удалено');
    }

    public function tap(Request $r, Habit $habit)
    {
        abort_unless($habit->user_id === $r->user()->id, 403);
        return back()->with('status', Game::tap($habit, $r->input('dir', 'plus')));
    }

    private function data(Request $r): array
    {
        $d = $r->validate([
            'title' => 'required|string|max:80',
            'type' => 'required|in:daily,habit',
            'difficulty' => 'required|integer|in:1,2,3',
            'days' => 'nullable|array',
            'days.*' => 'integer|between:1,7',
        ]);
        $days = $d['days'] ?? [];
        $d['days'] = ($d['type'] === 'daily' && $days && count($days) < 7) ? array_map('intval', $days) : null;
        return $d;
    }
}
