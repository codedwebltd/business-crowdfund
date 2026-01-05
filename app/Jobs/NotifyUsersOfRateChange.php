<?php

namespace App\Jobs;

use App\Models\TokenRateHistory;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class NotifyUsersOfRateChange implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public TokenRateHistory $rateHistory;

    /**
     * Create a new job instance.
     */
    public function __construct(TokenRateHistory $rateHistory)
    {
        $this->rateHistory = $rateHistory;
    }

    /**
     * Execute the job - Batch fanout to all users.
     */
    public function handle(): void
    {
        // Chunk users into batches of 500 for efficient processing
        $jobs = [];

        User::where('status', 'ACTIVE')
            ->chunk(500, function ($users) use (&$jobs) {
                foreach ($users as $user) {
                    $jobs[] = new SendRateChangeNotification($user, $this->rateHistory);
                }
            });

        // Dispatch batch (Laravel handles queueing automatically)
        if (count($jobs) > 0) {
            Bus::batch($jobs)
                ->name('Token Rate Change Notifications - ' . now()->toDateTimeString())
                ->allowFailures() // Don't stop batch if one fails
                ->dispatch();
        }
    }
}

/**
 * Individual notification job (runs per user)
 */
class SendRateChangeNotification implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public TokenRateHistory $rateHistory;

    public function __construct(User $user, TokenRateHistory $rateHistory)
    {
        $this->user = $user;
        $this->rateHistory = $rateHistory;
    }

    public function handle(NotificationService $notificationService): void
    {
        // Skip if batch has been cancelled
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Prepare notification data
        $trendEmoji = match ($this->rateHistory->trend) {
            'up' => '📈',
            'down' => '📉',
            default => '➡️',
        };

        $message = sprintf(
            '%s Token rate updated! New withdrawal rate: %s%% (%s %s)',
            $trendEmoji,
            number_format($this->rateHistory->withdrawal_rate * 100, 2),
            $this->rateHistory->trend === 'up' ? '+' : '',
            $this->rateHistory->getTrendPercentage()
        );

        // Send via NotificationService (uses channels from README.md)
        $notificationService->send($this->user, 'token_rate_change', [
            'token_price' => $this->rateHistory->token_price,
            'withdrawal_rate' => $this->rateHistory->withdrawal_rate,
            'trend' => $this->rateHistory->trend,
            'trend_percentage' => $this->rateHistory->getTrendPercentage(),
            'message' => $message,
            'change_reason' => $this->rateHistory->change_reason,
        ]);
    }
}
