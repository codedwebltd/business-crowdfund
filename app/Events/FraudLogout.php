<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FraudLogout implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $fraudData;

    public function __construct($userId, array $fraudData)
    {
        $this->userId = $userId;
        $this->fraudData = $fraudData;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'fraud-logout';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => $this->fraudData['title'],
            'message' => $this->fraudData['message'],
            'offense' => $this->fraudData['offense'],
            'banned_until' => $this->fraudData['banned_until'] ?? null,
        ];
    }
}
