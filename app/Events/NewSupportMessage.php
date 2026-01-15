<?php

namespace App\Events;

use App\Models\SupportMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSupportMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(SupportMessage $message)
    {
        // Load sender relationship if it's an admin message
        if ($message->sender_type === 'admin' && !$message->relationLoaded('sender')) {
            $message->load('sender');
        }

        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // Specific ticket channel (user/guest listens here)
            new Channel('support.ticket.' . $this->message->ticket_id),
            // Admin channel (admin dashboard listens here)
            new Channel('support.admin'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-message';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->message->id,
            'ticket_id' => $this->message->ticket_id,
            'sender_type' => $this->message->sender_type,
            'message' => $this->message->message,
            'message_type' => $this->message->message_type,
            'file_path' => $this->message->file_path,
            'file_name' => $this->message->file_name,
            'file_type' => $this->message->file_type,
            'created_at' => $this->message->created_at->toISOString(),
        ];

        // Add sender name
        if ($this->message->sender_type === 'admin' && $this->message->sender) {
            $data['sender_name'] = $this->message->sender->full_name ?? 'Support Agent';
        } elseif ($this->message->sender_type === 'system') {
            $data['sender_name'] = 'System';
        } else {
            $data['sender_name'] = $this->message->ticket?->owner_name ?? 'User';
        }

        return $data;
    }
}
