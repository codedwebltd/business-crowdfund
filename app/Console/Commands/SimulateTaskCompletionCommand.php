<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserTask;
use App\Models\TaskTemplate;
use App\Models\Transaction;
use App\Models\CommissionLedger;
use App\Models\Wallet;
use App\Models\ReferralTree;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimulateTaskCompletionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:simulate-tasks {count=10 : Number of tasks to simulate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate task completions for commission testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $taskCount = (int) $this->argument('count');

        $this->info("🎯 Simulating {$taskCount} task completions...\n");

        // Get test users (Level 5 users - deepest in tree)
        $testUsers = User::where('email', 'LIKE', 'level5.%')
            ->orWhere('email', 'LIKE', 'level4.%')
            ->orWhere('email', 'LIKE', 'level3.%')
            ->limit($taskCount)
            ->get();

        if ($testUsers->isEmpty()) {
            $this->error('No test users found! Run: php artisan db:seed --class=CommissionTestSeeder first');
            return 1;
        }

        // Get random task templates
        $templates = TaskTemplate::where('is_active', true)->get();
        if ($templates->isEmpty()) {
            $this->error('No active task templates found!');
            return 1;
        }

        $this->info("✓ Found {$testUsers->count()} test users");
        $this->info("✓ Found {$templates->count()} task templates\n");

        $completedCount = 0;
        $totalEarned = 0;
        $totalCommissions = 0;

        DB::beginTransaction();
        try {
            foreach ($testUsers as $user) {
                // Pick random template
                $template = $templates->random();

                // Create and complete task
                $task = UserTask::create([
                    'user_id' => $user->id,
                    'task_template_id' => $template->id,
                    'reward_amount' => $template->reward_amount,
                    'status' => 'COMPLETED',
                    'assigned_at' => now()->subHours(2),
                    'started_at' => now()->subHour(),
                    'completed_at' => now(),
                    'expires_at' => now()->addHours(22),
                    'completion_duration_seconds' => rand(60, 300),
                    'response_data' => ['simulated' => true],
                    'credited' => true,
                ]);

                // Credit user wallet (PENDING)
                $wallet = $user->wallet;
                $wallet->increment('pending_balance', $task->reward_amount);
                $wallet->increment('total_earned', $task->reward_amount);

                // Create transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'transaction_type' => 'TASK_EARNING',
                    'balance_type' => 'PENDING',
                    'amount' => $task->reward_amount,
                    'status' => 'PENDING',
                    'is_credit' => true,
                    'description' => "Simulated task: {$template->title}",
                    'metadata' => ['task_id' => $task->id, 'simulated' => true],
                ]);

                // Update user stats
                $user->increment('total_tasks_completed');
                $user->increment('tasks_completed_this_week');
                $user->increment('tasks_completed_this_month');
                $user->update(['last_task_completed_at' => now()]);

                // Update template completion count
                $template->increment('current_completions');

                // Distribute commissions
                $commissionsEarned = $this->distributeCommissions($user, $task);
                $totalCommissions += $commissionsEarned;

                $totalEarned += $task->reward_amount;
                $completedCount++;

                $this->info("✓ {$user->full_name} completed task (₦{$task->reward_amount}) → ₦{$commissionsEarned} in commissions distributed");
            }

            DB::commit();

            // Show summary
            $this->info("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("✅ Simulation Complete!");
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("📊 Tasks Completed: {$completedCount}");
            $this->info("💰 Total Earned: ₦" . number_format($totalEarned, 2));
            $this->info("🎁 Total Commissions: ₦" . number_format($totalCommissions, 2));

            // Show commission breakdown
            $this->showCommissionBreakdown();

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Simulation failed: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    private function distributeCommissions($user, $task): float
    {
        $settings = \App\Models\GlobalSetting::first();
        $commissionRates = $settings->commission_rates['task_earnings'] ?? [];
        $maxDepth = $settings->referral_levels_depth ?? 5;
        $totalCommissions = 0;

        if (empty($commissionRates)) {
            return 0;
        }

        $referralTree = ReferralTree::where('user_id', $user->id)->first();
        if (!$referralTree) {
            return 0;
        }

        $upline = $referralTree->getUplineForCommissions();
        if (empty($upline)) {
            return 0;
        }

        foreach ($upline as $ancestor) {
            $level = $ancestor['level'];
            $uplineUserId = $ancestor['user_id'];

            if ($level > $maxDepth || !isset($commissionRates[$level])) {
                continue;
            }

            $commissionRate = $commissionRates[$level];
            $commissionAmount = round(($task->reward_amount * $commissionRate) / 100, 2);

            if ($commissionAmount <= 0) {
                continue;
            }

            // Record in commission ledger (PENDING - will be disbursed later via batch job)
            CommissionLedger::create([
                'user_id' => $uplineUserId,
                'source_user_id' => $user->id,
                'source_task_id' => $task->id,
                'amount' => $commissionAmount,
                'level' => $level,
                'commission_rate' => $commissionRate,
                'status' => 'PENDING', // Changed from PROCESSED
                'processed_at' => null, // Will be set when batch job processes
            ]);

            $totalCommissions += $commissionAmount;
        }

        return $totalCommissions;
    }

    private function showCommissionBreakdown()
    {
        $this->info("\n🏆 Top Earners (Commission):");

        $topEarners = User::select('users.id', 'users.full_name', 'users.email', 'wallets.withdrawable_balance')
            ->join('wallets', 'users.id', '=', 'wallets.user_id')
            ->where('users.email', 'LIKE', 'level%')
            ->orWhere('users.email', 'Angab704@gmail.com')
            ->orderBy('wallets.withdrawable_balance', 'desc')
            ->limit(10)
            ->get();

        foreach ($topEarners as $index => $earner) {
            $commissions = Transaction::where('user_id', $earner->id)
                ->where('transaction_type', 'REFERRAL_BONUS')
                ->sum('amount');

            if ($commissions > 0) {
                $this->info(sprintf(
                    "   %d. %-20s ₦%-10s (₦%s in commissions)",
                    $index + 1,
                    substr($earner->full_name, 0, 20),
                    number_format($earner->withdrawable_balance, 2),
                    number_format($commissions, 2)
                ));
            }
        }

        $this->info("\n📈 Commission Stats:");
        $totalCommissionsPaid = CommissionLedger::where('status', 'PROCESSED')->sum('amount');
        $totalCommissionRecords = CommissionLedger::where('status', 'PROCESSED')->count();

        $this->info("   • Total Paid: ₦" . number_format($totalCommissionsPaid, 2));
        $this->info("   • Total Records: {$totalCommissionRecords}");

        foreach ([1, 2, 3, 4, 5] as $level) {
            $levelTotal = CommissionLedger::where('level', $level)->sum('amount');
            if ($levelTotal > 0) {
                $this->info("   • Level {$level}: ₦" . number_format($levelTotal, 2));
            }
        }
    }
}
