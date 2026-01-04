<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\NotificationService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBalanceMaturationJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes timeout per job
    public $tries = 3; // Retry 3 times on failure

    /**
     * Collection of transaction IDs to process
     */
    protected array $transactionIds;

    /**
     * Create a new job instance.
     */
    public function __construct(array $transactionIds)
    {
        $this->transactionIds = $transactionIds;
        $this->onQueue('snail'); // Use snail queue to avoid blocking main queue
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        Log::info("Processing balance maturation batch", [
            'transaction_count' => count($this->transactionIds),
            'batch_id' => $this->batch()?->id,
        ]);

        $processedCount = 0;
        $failedCount = 0;
        $totalAmount = 0;

        // Group transactions by user for efficient notification batching
        $userTransactions = [];

        foreach ($this->transactionIds as $transactionId) {
            try {
                DB::beginTransaction();

                $transaction = Transaction::find($transactionId);

                if (!$transaction) {
                    Log::warning("Transaction not found: {$transactionId}");
                    $failedCount++;
                    DB::rollBack();
                    continue;
                }

                // Double-check transaction is still eligible
                if ($transaction->status !== 'PENDING' || $transaction->balance_type !== 'PENDING') {
                    Log::info("Transaction {$transactionId} already processed or invalid status");
                    DB::rollBack();
                    continue;
                }

                $wallet = Wallet::where('user_id', $transaction->user_id)->lockForUpdate()->first();

                if (!$wallet) {
                    Log::error("Wallet not found for user {$transaction->user_id}");
                    $failedCount++;
                    DB::rollBack();
                    continue;
                }

                // Transfer from pending to withdrawable
                $wallet->decrement('pending_balance', $transaction->amount);
                $wallet->increment('withdrawable_balance', $transaction->amount);
                $wallet->update(['last_transaction_at' => now()]);

                // Update transaction status
                $transaction->update([
                    'status' => 'COMPLETED',
                    'balance_type' => 'WITHDRAWABLE',
                    'processed_at' => now(),
                ]);

                DB::commit();

                $processedCount++;
                $totalAmount += $transaction->amount;

                // Group by user for batch notification
                if (!isset($userTransactions[$transaction->user_id])) {
                    $userTransactions[$transaction->user_id] = [
                        'count' => 0,
                        'total_amount' => 0,
                        'withdrawable_balance' => $wallet->withdrawable_balance,
                    ];
                }
                $userTransactions[$transaction->user_id]['count']++;
                $userTransactions[$transaction->user_id]['total_amount'] += $transaction->amount;

                Log::info("Matured transaction {$transactionId}: ₦{$transaction->amount} for user {$transaction->user_id}");

            } catch (\Exception $e) {
                DB::rollBack();
                $failedCount++;
                Log::error("Failed to mature transaction {$transactionId}: {$e->getMessage()}", [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Send notifications to users (grouped by user)
        foreach ($userTransactions as $userId => $data) {
            try {
                $user = User::find($userId);
                if (!$user) {
                    Log::warning("User not found for notification: {$userId}");
                    continue;
                }

                $notificationService->send($user, 'balance_matured', [
                    'count' => $data['count'],
                    'amount' => $data['total_amount'],
                    'withdrawable_balance' => $data['withdrawable_balance'],
                ]);

                Log::info("Notification sent to user {$userId} for ₦{$data['total_amount']} maturation");

            } catch (\Exception $e) {
                Log::error("Failed to send maturation notification to user {$userId}: {$e->getMessage()}");
                // Don't fail the job if notification fails
            }
        }

        Log::info("Batch maturation completed", [
            'processed' => $processedCount,
            'failed' => $failedCount,
            'total_amount' => $totalAmount,
            'unique_users' => count($userTransactions),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessBalanceMaturationJob failed", [
            'transaction_ids' => $this->transactionIds,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
