<?php
namespace App\Http\Controllers;

use App\Models\Reward;
use App\Support\Game;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $r)
    {
        return view('shop', [
            'u' => $r->user(),
            'items' => config('game.shop'),
            'rewards' => $r->user()->rewards()->orderBy('cost')->get(),
        ]);
    }

    public function buyItem(Request $r)
    {
        $code = $r->input('code');
        $item = config("game.shop.$code");
        abort_unless($item, 404);

        $u = $r->user();
        if ($u->gold < $item['cost']) return back()->with('status', 'Не хватает золота');

        $u->gold -= $item['cost'];
        if ($code === 'potion') $u->hp = min(Game::hpMax($u->level), $u->hp + $item['heal']);
        if ($code === 'shield') $u->shields++;
        $u->save();

        return back()->with('status', $item['icon'] . ' ' . $item['name'] . ' куплено');
    }

    public function storeReward(Request $r)
    {
        $r->user()->rewards()->create($r->validate([
            'title' => 'required|string|max:60',
            'cost' => 'required|integer|min:1|max:100000',
        ]));
        return back()->with('status', 'Награда добавлена');
    }

    public function buyReward(Request $r, Reward $reward)
    {
        abort_unless($reward->user_id === $r->user()->id, 403);
        $u = $r->user();
        if ($u->gold < $reward->cost) return back()->with('status', 'Не хватает золота');

        $u->gold -= $reward->cost;
        $u->save();
        return back()->with('status', '🎉 Заслужено: ' . $reward->title);
    }

    public function destroyReward(Request $r, Reward $reward)
    {
        abort_unless($reward->user_id === $r->user()->id, 403);
        $reward->delete();
        return back();
    }
}
