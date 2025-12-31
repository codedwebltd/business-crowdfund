<?php

namespace App\Console\Commands;

use App\Models\GlobalSetting;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MaturationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:mature-earnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer matured earnings from PENDING to WITHDRAWABLE balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting earnings maturation process...');

        // Get maturation hours from settings (default 72)
        $settings = GlobalSetting::first();
        $maturationHours = $settings->maturation_hours ?? 72;

        // Find all PENDING transactions that have matured (any type)
        $maturedTransactions = Transaction::where('status', 'PENDING')
            ->where('balance_type', 'PENDING')
            ->where('created_at', '<=', now()->subHours($maturationHours))
            ->get();

        if ($maturedTransactions->isEmpty()) {
            $this->info('No matured earnings to process.');
            return 0;
        }

        $this->info("Found {$maturedTransactions->count()} matured transactions to process.");

        $processedCount = 0;
        $totalAmount = 0;

        DB::beginTransaction();
        try {
            foreach ($maturedTransactions as $transaction) {
                $wallet = Wallet::where('user_id', $transaction->user_id)->first();

                if (!$wallet) {
                    $this->error("Wallet not found for user {$transaction->user_id}");
                    continue;
                }

                // Transfer from pending to withdrawable
                $wallet->decrement('pending_balance', $transaction->amount);
                $wallet->increment('withdrawable_balance', $transaction->amount);

                // Update transaction status
                $transaction->update([
                    'status' => 'COMPLETED',
                    'balance_type' => 'WITHDRAWABLE',
                ]);

                $processedCount++;
                $totalAmount += $transaction->amount;

                $this->info("✓ Matured ₦{$transaction->amount} for user {$transaction->user_id}");
            }

            DB::commit();

            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("✅ Successfully processed {$processedCount} transactions");
            $this->info("💰 Total matured: ₦{$totalAmount}");
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Maturation failed: {$e->getMessage()}");
            logger()->error("Maturation failed: {$e->getMessage()}", [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
