<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLockScreen
{
    public function handle(Request $request, Closure $next)
    {
        // Skip the middleware for the lockscreen, unlock, and login routes
        if ($request->routeIs('lockscreen') || $request->routeIs('unlock') || $request->routeIs('login')) {
            return $next($request);
        }

        // If the session is locked, redirect to the lockscreen
        // if (session('locked')) {
        //     return redirect()->route('lockscreen');
        // }

        return $next($request);
    }
}