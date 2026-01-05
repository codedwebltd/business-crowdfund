<?php

namespace App\Observers;

use App\Models\Withdrawal;
use App\Models\Transaction;

class WithdrawalObserver
{
    /**
     * Handle the Withdrawal "updated" event.
     * When withdrawal status changes, update the linked transaction status
     */
    public function updated(Withdrawal $withdrawal): void
    {
        // Only update if status changed
        if ($withdrawal->isDirty('status')) {
            // Find linked transaction
            $transaction = Transaction::where('reference_id', $withdrawal->id)
                ->where('reference_type', 'App\Models\Withdrawal')
                ->first();

            if ($transaction) {
                $newStatus = match ($withdrawal->status) {
                    'PENDING' => 'PENDING',
                    'PROCESSING' => 'PROCESSING',  // Show processing to user
                    'APPROVED' => 'APPROVED',
                    'COMPLETED' => 'COMPLETED',
                    'REJECTED' => 'REJECTED',
                    default => $transaction->status,
                };

                $transaction->update([
                    'status' => $newStatus,
                    'processed_at' => $withdrawal->status === 'COMPLETED' ? now() : null,
                    'processed_by' => $withdrawal->approved_by_id,
                ]);
            }
        }
    }

    /**
     * Handle the Withdrawal "deleted" event.
     * If withdrawal is deleted, mark transaction as cancelled
     */
    public function deleted(Withdrawal $withdrawal): void
    {
        $transaction = Transaction::where('reference_id', $withdrawal->id)
            ->where('reference_type', 'App\Models\Withdrawal')
            ->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'CANCELLED',
                'processed_at' => now(),
            ]);
        }
    }
}
