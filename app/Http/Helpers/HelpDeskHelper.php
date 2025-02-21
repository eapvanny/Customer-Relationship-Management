<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;


if (! function_exists('getAuthUser')) {
    function getAuthUser()
    {
        $auth = auth()->user();
        $session_unique = Session::getId();
        if (Cache::has($session_unique.'_lang') && Auth::check()) {
            $auth->user_lang = Cache::get($session_unique.'_lang');
        }
       
        return $auth;
    }
}