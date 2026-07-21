<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function updateUserProfile(Request $request)
    {
        try {

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'staff_id_card' => 'required|string|max:100',
                'phone_no' => 'required|string|max:30',
                'family_name' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'family_name_latin' => 'nullable|string|max:255',
                'name_latin' => 'nullable|string|max:255',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,heic|max:10240',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user->staff_id_card = $request->staff_id_card;
            $user->phone_no = $request->phone_no;
            $user->family_name = $request->family_name;
            $user->name = $request->name;
            $user->family_name_latin = $request->family_name_latin;
            $user->name_latin = $request->name_latin;

            if ($request->hasFile('photo')) {

                // Delete old photo
                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }

                $file = $request->file('photo');

                $fileName = 'Uploads/profile_' . time() . '_' . Str::random(10) . '.' . $file->extension();

                Storage::disk('public')->put(
                    $fileName,
                    file_get_contents($file)
                );

                $user->photo = $fileName;
            }

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $user->id,
                    'staff_id_card' => $user->staff_id_card,
                    'phone_no' => $user->phone_no,
                    'family_name' => $user->family_name,
                    'name' => $user->name,
                    'family_name_latin' => $user->family_name_latin,
                    'name_latin' => $user->name_latin,
                    'photo' => $user->photo
                        ? asset('storage/' . $user->photo)
                        : asset('images/avatar.png'),
                ]
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}