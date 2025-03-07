<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and has a language preference
        if (auth()->check() && auth()->user()->user_lang) {
            app()->setLocale(auth()->user()->user_lang); // Set the language
        } else {
            app()->setLocale('kh');  // Default language if not set
        }

        return $next($request);
    }
}
