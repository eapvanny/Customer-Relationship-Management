<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Get allowed roles dynamically from the database
            $allowedRoles = Role::pluck('name')->toArray();

            // Check if the user has any of the allowed roles
            if ($user->hasAnyRole($allowedRoles)) {
                return $next($request);
            }

            abort(403, "User doesn't have a correct role");
        }   
        abort(401);
    }
}
