<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AjaxBackendController extends Controller
{
    public function getUserArea(Request $request)
    {
        $areaText = trim($request->area);

        $users = User::whereRaw('LOWER(TRIM(area)) = ?', [strtolower($areaText)])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->username . ' (' . $user->full_name . ')'
                ];
            });

        return response()->json([
            'status' => true,
            'area'   => $areaText,
            'total'  => $users->count(),
            'users'  => $users
        ]);
    }

}
