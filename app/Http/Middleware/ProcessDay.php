<?php
namespace App\Http\Middleware;

use App\Support\Game;
use Closure;
use Illuminate\Http\Request;

class ProcessDay
{
    public function handle(Request $request, Closure $next)
    {
        if ($u = $request->user()) Game::rollover($u);
        return $next($request);
    }
}
