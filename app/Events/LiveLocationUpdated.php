<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $sessionId;
    public $locationId;
    public $latitude;
    public $longitude;
    public $accuracy;
    public $recordedAt;
    public $userName;

    public function __construct(
        int $userId,
        int $sessionId,
        int $locationId,
        float $latitude,
        float $longitude,
        ?float $accuracy,
        string $recordedAt,
        string $userName
    ) {
        $this->userId = $userId;
        $this->sessionId = $sessionId;
        $this->locationId = $locationId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->accuracy = $accuracy;
        $this->recordedAt = $recordedAt;
        $this->userName = $userName;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('crm.live-location'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'session_id' => $this->sessionId,
            'location_id' => $this->locationId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'accuracy' => $this->accuracy,
            'recorded_at' => $this->recordedAt,
            'user_name' => $this->userName,
        ];
    }
}
