<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportRequest implements ShouldBroadcastNow // Use ShouldBroadcastNow for synchronous execution
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('my-channel'),
        ];
    }

    public function broadcastAs()
    {
        return 'my-event';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
