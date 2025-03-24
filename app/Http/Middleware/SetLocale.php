<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // dd(Auth::user());

        if(!empty(Auth::user())){
            $check_role = Role::find(@Auth::user()->id)->get()->first();
            // dd($check_role->name);
            if($check_role->name == 'Employee'){
                app()->setLocale('kh'); 
            }else{
                if (auth()->check() && auth()->user()->user_lang) {
                    app()->setLocale(auth()->user()->user_lang); // Set the language
                } else {
                    app()->setLocale('kh');  // Default language if not set
                }
            }
        }else{
            if (auth()->check() && auth()->user()->user_lang) {
                app()->setLocale(auth()->user()->user_lang); // Set the language
            } else {
                app()->setLocale('kh');  // Default language if not set
            }
        }

        // Check if the user is authenticated and has a language preference
        

        return $next($request);
    }
}
