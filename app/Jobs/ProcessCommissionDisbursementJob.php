<?php

namespace App\Jobs;

use App\Models\CommissionLedger;
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

class ProcessCommissionDisbursementJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;
    public $backoff = [30, 60, 120];

    protected $userId;
    protected $commissionLedgerIds;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, array $commissionLedgerIds)
    {
        $this->userId = $userId;
        $this->commissionLedgerIds = $commissionLedgerIds;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        DB::beginTransaction();
        try {
            // Get user and wallet
            $user = User::lockForUpdate()->findOrFail($this->userId);
            $wallet = Wallet::lockForUpdate()->where('user_id', $this->userId)->first();

            if (!$wallet) {
                Log::error("Wallet not found for user {$this->userId}");
                DB::rollBack();
                return;
            }

            // Get all pending commission ledger entries for this user
            $commissions = CommissionLedger::whereIn('id', $this->commissionLedgerIds)
                ->where('user_id', $this->userId)
                ->where('status', 'PENDING')
                ->lockForUpdate()
                ->get();

            if ($commissions->isEmpty()) {
                DB::rollBack();
                return;
            }

            // Calculate total commission amount
            $totalAmount = $commissions->sum('amount');

            // Credit to withdrawable balance
            $wallet->increment('withdrawable_balance', $totalAmount);
            $wallet->increment('total_earned', $totalAmount);

            // Create transaction record
            Transaction::create([
                'user_id' => $this->userId,
                'transaction_type' => 'TEAM_COMMISSION',
                'balance_type' => 'WITHDRAWABLE',
                'amount' => $totalAmount,
                'status' => 'COMPLETED',
                'is_credit' => true,
                'description' => "Team commission payout ({$commissions->count()} earnings)",
                'metadata' => [
                    'commission_count' => $commissions->count(),
                    'commission_ledger_ids' => $this->commissionLedgerIds,
                    'disbursement_date' => now()->toDateString(),
                ],
            ]);

            // Mark all commissions as processed
            CommissionLedger::whereIn('id', $this->commissionLedgerIds)
                ->update([
                    'status' => 'PROCESSED',
                    'processed_at' => now(),
                ]);

            DB::commit();

            // Send notification (outside transaction)
            try {
                $notificationService->send($user, 'team_commission', [
                    'amount' => $totalAmount,
                    'message' => "Great news! You've earned ₦" . number_format($totalAmount, 2) . " from your team's activity. Keep growing your network to maximize your earnings.",
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send commission notification: " . $e->getMessage());
            }

            Log::info("Commission disbursed", [
                'user_id' => $this->userId,
                'amount' => $totalAmount,
                'commission_count' => $commissions->count(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Commission disbursement failed for user {$this->userId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Commission disbursement job failed permanently", [
            'user_id' => $this->userId,
            'commission_ledger_ids' => $this->commissionLedgerIds,
            'error' => $exception->getMessage(),
        ]);
    }
}
