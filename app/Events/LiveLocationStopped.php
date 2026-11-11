<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveLocationStopped implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $sessionId;
    public $endedAt;

    public function __construct(
        int $userId,
        int $sessionId,
        string $endedAt
    ) {
        $this->userId = $userId;
        $this->sessionId = $sessionId;
        $this->endedAt = $endedAt;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('crm.live-location'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'location.stopped';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'session_id' => $this->sessionId,
            'status' => 'offline',
            'ended_at' => $this->endedAt,
        ];
    }
}
