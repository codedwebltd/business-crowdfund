<?php

namespace App\Events;

use App\Models\SupportTicket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSupportTicket implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('support.admin'), // Admin listens for new tickets
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-ticket';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'first_message' => $this->ticket->first_message,
            'owner_name' => $this->ticket->owner_name,
            'owner_email' => $this->ticket->owner_email,
            'status' => $this->ticket->status,
            'priority' => $this->ticket->priority,
            'category' => $this->ticket->category,
            'user_id' => $this->ticket->user_id,
            'created_at' => $this->ticket->created_at->toISOString(),
        ];
    }
}
