<?php

namespace App\Http\Controllers\API;

use App\Events\LiveLocationUpdated;
use App\Events\LiveLocationStopped;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLocationHistory;
use App\Models\UserWorkSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LiveLocationController extends Controller
{
    /**
     * Check In and start live tracking.
     */
    public function start(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy'  => ['nullable', 'numeric', 'min:0'],
        ]);

        if (!$user || (int) $user->status !== 1) {
            return response()->json([
                'message' => 'Your account is inactive.',
            ], 403);
        }

        $result = DB::transaction(function () use ($user, $data) {

            // Lock user row to prevent duplicate sessions.
            User::whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $session = UserWorkSession::where('user_id', $user->id)
                ->whereNull('ended_at')
                ->latest('id')
                ->first();

            // Reuse active session or create new one.
            if (!$session) {
                $session = UserWorkSession::create([
                    'user_id' => $user->id,
                    'started_at' => now(),
                    'start_latitude' => $data['latitude'],
                    'start_longitude' => $data['longitude'],
                ]);
            }

            $location = $this->saveLocation(
                $session,
                $user->id,
                $data
            );

            return [
                'session' => $session,
                'location' => $location,
            ];
        });

        // Broadcast after database transaction.
        $this->broadcastLocation(
            $result['session'],
            $result['location']
        );

        return response()->json([
            'success' => true,
            'message' => 'Check In successful. Live tracking started.',
            'user_id' => $user->id,
            'session_id' => $result['session']->id,
            'tracking' => true,
            'location' => $result['location'],
        ], 201);
    }

    /**
     * Receive GPS updates every 15 seconds.
     */
    public function updateLocation(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'speed' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'between:0,360'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $result = DB::transaction(function () use ($user, $data) {

            $session = UserWorkSession::where('user_id', $user->id)
                ->whereNull('ended_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$session) {
                return null;
            }

            $location = $this->saveLocation(
                $session,
                $user->id,
                $data
            );

            return [
                'session' => $session,
                'location' => $location,
            ];
        });

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'No active work session. Please Check In first.',
            ], 409);
        }

        $this->broadcastLocation(
            $result['session'],
            $result['location']
        );

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully.',
            'user_id' => $user->id,
            'session_id' => $result['session']->id,
            'location' => $result['location'],
        ]);
    }

    /**
     * Check Out and stop tracking.
     */
    public function stop(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $session = DB::transaction(function () use ($user, $data) {

            $session = UserWorkSession::where('user_id', $user->id)
                ->whereNull('ended_at')
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$session) {
                return null;
            }

            $session->update([
                'ended_at' => now(),
                'end_latitude' => $data['latitude'] ?? null,
                'end_longitude' => $data['longitude'] ?? null,
            ]);

            return $session;
        });

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'No active work session.',
            ], 409);
        }

        // Broadcast Check Out status to Web Dashboard.
        event(new LiveLocationStopped(
            (int) $session->user_id,
            (int) $session->id,
            $session->ended_at->toIso8601String()
        ));

        return response()->json([
            'success' => true,
            'message' => 'Check Out successful. Tracking stopped.',
            'user_id' => $user->id,
            'session_id' => $session->id,
            'tracking' => false,
            'status' => 'offline',
            'ended_at' => $session->ended_at,
        ]);
    }

    /**
     * Get current tracking status.
     */
    public function status(Request $request)
    {
        $user = $request->user();

        $session = UserWorkSession::where('user_id', $user->id)
            ->whereNull('ended_at')
            ->latest('id')
            ->first();

        $location = $session
            ? $session->locations()->latest('id')->first()
            : null;

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'tracking' => (bool) $session,
            'session_id' => $session?->id,
            'started_at' => $session?->started_at,
            'last_location' => $location,
        ]);
    }

    /**
     * Save GPS history.
     */
    private function saveLocation(
        UserWorkSession $session,
        int $userId,
        array $data
    ): UserLocationHistory {

        return $session->locations()->create([
            'user_id' => $userId,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'accuracy' => $data['accuracy'] ?? null,
            'speed' => $data['speed'] ?? null,
            'heading' => $data['heading'] ?? null,
            'recorded_at' => !empty($data['recorded_at'])
                ? Carbon::parse($data['recorded_at'])
                : now(),
        ]);
    }

    /**
     * Broadcast location to CRM dashboard.
     */
    private function broadcastLocation(
        UserWorkSession $session,
        UserLocationHistory $location
    ): void {

        $user = User::find($session->user_id);

        event(new LiveLocationUpdated(
            (int) $session->user_id,
            (int) $session->id,
            (int) $location->id,
            (float) $location->latitude,
            (float) $location->longitude,
            $location->accuracy !== null
                ? (float) $location->accuracy
                : null,
            $location->recorded_at->toIso8601String(),
            $user?->full_name
                ?? $user?->name
                ?? 'User'
        ));
    }
}


