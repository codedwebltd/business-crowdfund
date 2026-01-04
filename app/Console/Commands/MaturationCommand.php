<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBalanceMaturationJob;
use App\Models\GlobalSetting;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

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
    protected $description = 'Transfer matured earnings from PENDING to WITHDRAWABLE balance using batch jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting earnings maturation process...');

        // Get maturation hours from settings (default 72)
        $settings = GlobalSetting::first();
        $maturationHours = $settings->pending_balance_maturation_hours ?? 72;

        $this->info("⏰ Maturation threshold: {$maturationHours} hours");

        // Find all PENDING transactions that have matured
        $maturedTransactionIds = Transaction::where('status', 'PENDING')
            ->where('balance_type', 'PENDING')
            ->where('created_at', '<=', now()->subHours($maturationHours))
            ->pluck('id')
            ->toArray();

        if (empty($maturedTransactionIds)) {
            $this->info('✅ No matured earnings to process.');
            return 0;
        }

        $totalCount = count($maturedTransactionIds);
        $this->info("📊 Found {$totalCount} matured transactions to process.");

        // Chunk transactions into batches (500 per job for optimal performance)
        $chunkSize = 500;
        $chunks = array_chunk($maturedTransactionIds, $chunkSize);
        $jobCount = count($chunks);

        $this->info("📦 Creating {$jobCount} batch jobs ({$chunkSize} transactions per job)...");

        // Create jobs array
        $jobs = [];
        foreach ($chunks as $chunk) {
            $jobs[] = new ProcessBalanceMaturationJob($chunk);
        }

        // Dispatch batch jobs
        $batch = Bus::batch($jobs)
            ->name('Balance Maturation - ' . now()->format('Y-m-d H:i:s'))
            ->allowFailures() // Don't cancel entire batch if one job fails
            ->onQueue('snail') // Use snail queue
            ->finally(function () {
                logger()->info('Balance maturation batch completed');
            })
            ->dispatch();

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ Batch dispatched successfully!");
        $this->info("🆔 Batch ID: {$batch->id}");
        $this->info("📦 Jobs queued: {$jobCount}");
        $this->info("💼 Total transactions: {$totalCount}");
        $this->info("🔄 Queue: snail");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("📝 Monitor progress: php artisan queue:batches");
        $this->info("🔍 View batch: php artisan tinker → Bus::findBatch('{$batch->id}')");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return 0;
    }
}
