<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get()->map(function ($user) {
            return [
                'id'            => $user->id,
                'staff_id_card' => $user->staff_id_card,
                'username'      => $user->username,
                'area'          => $user->area,
                'role'          => $user->role?->name, // or $user->role?->title
                'phone_no'      => $user->phone_no,
                'name'          => $user->full_name,
                'gender'        => AppHelper::GENDER[$user->gender] ?? 'N/A',
                'email'         => $user->email,
                'photo'         => $user->photo
                    ? asset('storage/' . $user->photo)
                    : asset('images/avatar.png'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $users
        ]);
    }

    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id'            => $user->id,
                'staff_id_card' => $user->staff_id_card,
                'username'      => $user->username,
                'area'          => $user->area,
                'role'          => $user->role?->name,
                'phone_no'      => $user->phone_no,
                'name'          => $user->full_name,
                'gender'        => AppHelper::GENDER[$user->gender] ?? 'N/A',
                'email'         => $user->email,
                'photo'         => $user->photo
                    ? asset('storage/' . $user->photo)
                    : asset('images/avatar.png'),
            ]
        ]);
    }
}