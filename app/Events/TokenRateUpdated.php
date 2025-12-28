<?php

namespace App\Events;

use App\Models\TokenRateHistory;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokenRateUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TokenRateHistory $rateHistory;

    public function __construct(TokenRateHistory $rateHistory)
    {
        $this->rateHistory = $rateHistory;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('token-rates'), // Public channel - all users can listen
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'rate.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'token_price' => (float) $this->rateHistory->token_price,
            'withdrawal_rate' => (float) $this->rateHistory->withdrawal_rate,
            'trend' => $this->rateHistory->trend,
            'trend_percentage' => $this->rateHistory->getTrendPercentage(),
            'is_good_time' => $this->rateHistory->isGoodWithdrawalTime(),
            'change_reason' => $this->rateHistory->change_reason,
            'updated_at' => $this->rateHistory->created_at->toIso8601String(),
        ];
    }
}
