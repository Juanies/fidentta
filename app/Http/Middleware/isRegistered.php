<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isRegistered
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->subscribed('fidentta')) {
            $team = $request->user()->currentTeam;

            return $team
                ? redirect()->route('dashboard', ['current_team' => $team->getRouteKey()])
                : redirect()->route('registera');
        }


        return $next($request);
    }
}
