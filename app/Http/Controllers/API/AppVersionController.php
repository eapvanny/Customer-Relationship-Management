<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Get latest app version
     */
    public function check()
    {
        $version = AppVersion::latest('id')->first();

        if (!$version) {
            return response()->json([
                'message' => 'App version configuration not found.',
            ], 404);
        }

        return response()->json([
            'latest_version' => $version->latest_version,
            'can_update' => (bool) $version->can_update,
            'force_update' => (bool) $version->force_update,

            // Android
            'update_version_android' => (bool) $version->update_version_android,
            'update_url_android' => $version->update_url_android,

            // iOS
            'update_version_ios' => (bool) $version->update_version_ios,
            'update_url_ios' => $version->update_url_ios,

            'release_notes' => $version->release_notes,
        ]);
    }

    /**
     * Update app version
     */
    public function update(Request $request, $id)
    {
        $version = AppVersion::findOrFail($id);

        $validated = $request->validate([
            'latest_version' => 'required|string|max:20',

            'can_update' => 'required|boolean',
            'force_update' => 'required|boolean',

            'update_version_android' => 'required|boolean',
            'update_url_android' => 'nullable|url',

            'update_version_ios' => 'required|boolean',
            'update_url_ios' => 'nullable|url',

            'release_notes' => 'nullable|string',
        ]);

        $version->update($validated);

        return response()->json([
            'message' => 'App version updated successfully.',
            'data' => $version->fresh(),
        ]);
    }
}