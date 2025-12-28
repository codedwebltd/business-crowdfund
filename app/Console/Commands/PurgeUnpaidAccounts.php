<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PurgeUnpaidAccounts extends Command
{
    protected $signature = 'purge:unpaid-accounts';
    protected $description = 'Soft delete user accounts with unpaid activation payments older than 48 hours';

    public function handle()
    {
        $this->info('Starting purge of unpaid accounts...');

        // Find transactions that are:
        // 1. AWAITING_APPROVAL status
        // 2. ACTIVATION_PAYMENT type
        // 3. Older than 48 hours
        $cutoffTime = Carbon::now()->subHours(48);

        $staleTransactions = Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
            ->where('status', 'AWAITING_APPROVAL')
            ->where('created_at', '<', $cutoffTime)
            ->with('user')
            ->get();

        if ($staleTransactions->isEmpty()) {
            $this->info('No unpaid accounts found for purging.');
            return 0;
        }

        $purgedCount = 0;
        $notificationService = app(NotificationService::class);

        foreach ($staleTransactions as $transaction) {
            $user = $transaction->user;

            if (!$user || $user->trashed()) {
                continue;
            }

            // Check if user still has no plan (hasn't been activated)
            if (!$user->plan_id) {
                try {
                    // Send notification email before soft delete
                    $notificationService->send($user, 'account_purged', [
                        'transaction_id' => $transaction->transaction_hash,
                        'plan_name' => $transaction->metadata['plan_name'] ?? 'N/A',
                        'created_at' => $transaction->created_at->format('M d, Y H:i'),
                        'reason' => 'Unpaid activation payment after 48 hours',
                    ]);

                    // Soft delete the user account
                    $user->delete();

                    // Update transaction status
                    $transaction->update([
                        'status' => 'CANCELLED',
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'purge_reason' => 'Unpaid activation after 48 hours',
                            'purged_at' => now()->toDateTimeString(),
                        ]),
                    ]);

                    $this->info("Soft deleted user: {$user->email} (Transaction: {$transaction->transaction_hash})");
                    $purgedCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to purge user {$user->email}: " . $e->getMessage());
                }
            }
        }

        $this->info("Purge completed. {$purgedCount} accounts soft deleted.");

        return 0;
    }
}
