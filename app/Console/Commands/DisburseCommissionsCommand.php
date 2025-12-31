<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCommissionDisbursementJob;
use App\Models\CommissionLedger;
use App\Models\GlobalSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DisburseCommissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:disburse
                            {--batch-size=100 : Number of jobs per batch}
                            {--dry-run : Preview what would be processed without executing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disburse pending commissions to users using Bus::batch for massive parallel processing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("🏦 Commission Disbursement System");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n");

        $isDryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');

        if ($isDryRun) {
            $this->warn("🔍 DRY RUN MODE - No commissions will be disbursed\n");
        }

        // Get pending commissions grouped by user
        $this->info("📊 Querying pending commissions...");

        $pendingCommissions = CommissionLedger::where('status', 'PENDING')
            ->select('user_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('user_id')
            ->get();

        if ($pendingCommissions->isEmpty()) {
            $this->warn("✓ No pending commissions to disburse.");
            return 0;
        }

        $totalUsers = $pendingCommissions->count();
        $totalCommissions = $pendingCommissions->sum('count');
        $totalAmount = $pendingCommissions->sum('total');

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("📈 Statistics:");
        $this->info("   • Users with pending commissions: " . number_format($totalUsers));
        $this->info("   • Total commission entries: " . number_format($totalCommissions));
        $this->info("   • Total amount: ₦" . number_format($totalAmount, 2));
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n");

        if ($isDryRun) {
            $this->showDryRunDetails($pendingCommissions);
            return 0;
        }

        if (!$this->confirm('Do you want to proceed with disbursement?', true)) {
            $this->warn('Disbursement cancelled.');
            return 1;
        }

        // Build jobs array for Bus::batch
        $this->info("🔨 Building job batches...");
        $jobs = [];
        $progressBar = $this->output->createProgressBar($totalUsers);
        $progressBar->start();

        foreach ($pendingCommissions as $commission) {
            // Get all pending commission ledger IDs for this user
            $ledgerIds = CommissionLedger::where('user_id', $commission->user_id)
                ->where('status', 'PENDING')
                ->pluck('id')
                ->toArray();

            if (!empty($ledgerIds)) {
                $jobs[] = new ProcessCommissionDisbursementJob($commission->user_id, $ledgerIds);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✓ Created " . count($jobs) . " jobs");
        $this->info("🚀 Dispatching jobs in batches of {$batchSize}...\n");

        // Dispatch jobs using Bus::batch for massive fanout
        $batch = Bus::batch($jobs)
            ->name('Commission Disbursement - ' . now()->toDateTimeString())
            ->allowFailures()
            ->onQueue('commissions')
            ->then(function (\Illuminate\Bus\Batch $batch) {
                Log::info('Commission disbursement batch completed successfully', [
                    'batch_id' => $batch->id,
                    'processed_jobs' => $batch->processedJobs(),
                    'failed_jobs' => $batch->failedJobs,
                ]);
            })
            ->catch(function (\Illuminate\Bus\Batch $batch, \Throwable $e) {
                Log::error('Commission disbursement batch failed', [
                    'batch_id' => $batch->id,
                    'error' => $e->getMessage(),
                ]);
            })
            ->finally(function (\Illuminate\Bus\Batch $batch) {
                Log::info('Commission disbursement batch finished', [
                    'batch_id' => $batch->id,
                    'total_jobs' => $batch->totalJobs,
                    'pending_jobs' => $batch->pendingJobs,
                    'failed_jobs' => $batch->failedJobs,
                ]);
            })
            ->dispatch();

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ Batch Dispatched Successfully!");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("   • Batch ID: {$batch->id}");
        $this->info("   • Total Jobs: " . number_format($batch->totalJobs));
        $this->info("   • Queue: commissions");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n");

        $this->comment("💡 Monitor batch progress:");
        $this->comment("   php artisan queue:batches");
        $this->comment("   php artisan queue:work --queue=commissions");

        return 0;
    }

    /**
     * Show dry-run details
     */
    protected function showDryRunDetails($pendingCommissions): void
    {
        $this->info("🔍 Top 10 Users Who Would Receive Commissions:\n");

        $top = $pendingCommissions->sortByDesc('total')->take(10);

        $headers = ['User ID', 'Commission Count', 'Total Amount'];
        $rows = [];

        foreach ($top as $index => $commission) {
            $user = \App\Models\User::find($commission->user_id);
            $rows[] = [
                $user ? $user->full_name . " ({$user->email})" : $commission->user_id,
                number_format($commission->count),
                '₦' . number_format($commission->total, 2),
            ];
        }

        $this->table($headers, $rows);
    }
}
