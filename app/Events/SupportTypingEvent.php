<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupportTypingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket_id;
    public $is_typing;
    public $sender_type; // 'user', 'guest', 'admin'
    public $sender_id;
    public $sender_name;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $ticketId,
        bool $isTyping,
        string $senderType = 'user',
        ?string $senderId = null,
        ?string $senderName = null
    ) {
        $this->ticket_id = $ticketId;
        $this->is_typing = $isTyping;
        $this->sender_type = $senderType;
        $this->sender_id = $senderId;
        $this->sender_name = $senderName;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('support.ticket.' . $this->ticket_id),
        ];

        // If user/guest is typing, also notify admin channel
        if (in_array($this->sender_type, ['user', 'guest'])) {
            $channels[] = new Channel('support.admin');
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'typing';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'ticket_id' => $this->ticket_id,
            'is_typing' => $this->is_typing,
            'sender_type' => $this->sender_type,
            'sender_id' => $this->sender_id,
            'sender_name' => $this->sender_name,
        ];
    }
}
