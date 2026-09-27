<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Subscribed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->subscribed('fidentta')) {
            return redirect()->route('subscription-checkout');
        }

        return $next($request);  // ✅ Continúa si está pagado
    }
    }
