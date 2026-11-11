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
        return view('backend.location-tracking.live_tracking');
    }

    public function activeUsers(Request $request)
    {
        $timeout = (int) config(
            'tracking.heartbeat_timeout_seconds',
            60
        );

        $now = now();

        // Get the latest session for each user.
        $sessions = UserWorkSession::query()
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('user_work_sessions')
                    ->groupBy('user_id');
            })
            ->with([
                'user',
                'latestLocation',
            ])
            ->get();

        $data = $sessions->map(function ($session) use ($timeout, $now) {

            $location = $session->latestLocation;

            // created_at is the actual server-received timestamp.
            $lastHeartbeat = $location?->created_at;

            $heartbeatAge = $lastHeartbeat
                ? $now->diffInSeconds($lastHeartbeat)
                : null;

            // Check whether the GPS heartbeat is still fresh.
            $isHeartbeatAlive = $lastHeartbeat &&
                $lastHeartbeat->greaterThanOrEqualTo(
                    $now->copy()->subSeconds($timeout)
                );

            $isWorking = is_null($session->ended_at)
                && $isHeartbeatAlive;

            $status = $isWorking ? 'working' : 'offline';

            return [
                'user_id' => $session->user_id,
                'session_id' => $session->id,

                'name' => $session->user?->full_name
                    ?? $session->user?->name
                    ?? 'Unknown',

                'position' => $session->user?->position,

                'area' => $session->user?->area,

                'latitude' => $location
                    ? (float) $location->latitude
                    : null,

                'longitude' => $location
                    ? (float) $location->longitude
                    : null,

                'accuracy' => $location?->accuracy,

                'status' => $status,

                // Original GPS capture time.
                'recorded_at' => $location?->recorded_at,

                // Actual server received time.
                'last_seen_at' => $lastHeartbeat,

                'heartbeat_age_seconds' => $heartbeatAge,

                'heartbeat_timeout_seconds' => $timeout,

                'started_at' => $session->started_at,

                'ended_at' => $session->ended_at,
            ];
        })->values();

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
