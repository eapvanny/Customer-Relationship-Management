<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLocationHistory;
use App\Models\UserWorkSession;
use Illuminate\Http\Request;

class LiveTrackingController extends Controller
{
    public function index()
    {
        return view('backend.location-tacking.live_tracking');
    }

    public function activeUsers(Request $request)
    {
        $query = UserWorkSession::query()
            ->with('user')
            ->whereNull('ended_at')
            ->whereHas('locations');

        // Add your CRM role/hierarchy authorization here
        // before allowing the query to return other users.

        $sessions = $query->get();

        $data = $sessions->map(function ($session) {

            $location = $session->locations()
                ->latest('recorded_at')
                ->first();

            if (!$location) {
                return null;
            }

            return [
                'user_id' => $session->user_id,
                'session_id' => $session->id,

                'name' => $session->user?->full_name
                    ?? $session->user?->name
                    ?? 'Unknown',

                'position' => $session->user?->position ?? null,

                'area' => $session->user?->area ?? null,

                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,

                'accuracy' => $location->accuracy,

                'status' => 'working',

                'recorded_at' => $location->recorded_at,
                'last_seen_at' => $location->recorded_at,
            ];
        })->filter()->values();

        return response()->json($data);
    }

    public function history(Request $request, int $userId)
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        // Add your CRM hierarchy authorization here.
        $date = $filters['date'] ?? now()->toDateString();

        $history = UserLocationHistory::query()
            ->where('user_id', $userId)
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at')
            ->get([
                'id',
                'user_work_session_id',
                'latitude',
                'longitude',
                'accuracy',
                'recorded_at',
            ]);

        return response()->json($history);
    }
}
