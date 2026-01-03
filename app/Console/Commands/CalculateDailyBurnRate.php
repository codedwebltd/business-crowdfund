<?php

namespace App\Console\Commands;

use App\Models\BurnRateHistory;
use App\Models\GlobalSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CalculateDailyBurnRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liquidity:calculate-burn-rate
                            {--date= : Calculate for specific date (Y-m-d format, default: yesterday)}
                            {--force : Force recalculation even if record exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate daily burn rate for platform liquidity monitoring (map.MD line 889-916)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔥 Starting Burn Rate Calculation...');

        // Get date to calculate (default: yesterday)
        $targetDate = $this->option('date')
            ? \Carbon\Carbon::parse($this->option('date'))
            : now()->subDay();

        $dateStr = $targetDate->format('Y-m-d');

        $this->info("📅 Calculating burn rate for: {$dateStr}");

        // Check if record already exists
        $existingRecord = BurnRateHistory::where('report_date', $dateStr)->first();

        if ($existingRecord && !$this->option('force')) {
            $this->warn("⚠️  Record already exists for {$dateStr}. Use --force to recalculate.");
            return 0;
        }

        // Get global settings
        $settings = GlobalSetting::get();

        // 1. Calculate Total Deposits (ACTIVATION_PAYMENT transactions)
        $totalDeposits = Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
            ->where('status', 'COMPLETED')
            ->whereDate('created_at', $dateStr)
            ->sum('amount');

        $this->info("💰 Total Deposits: ₦" . number_format($totalDeposits, 2));

        // 2. Calculate Total Withdrawals (COMPLETED withdrawals)
        $totalWithdrawals = Withdrawal::where('status', 'COMPLETED')
            ->whereDate('processed_at', $dateStr)
            ->sum('amount_requested');

        $this->info("💸 Total Withdrawals: ₦" . number_format($totalWithdrawals, 2));

        // 3. Calculate Pending Withdrawals (snapshot at end of day)
        $pendingWithdrawals = Withdrawal::whereIn('status', ['PENDING', 'PROCESSING', 'APPROVED'])
            ->whereDate('requested_at', '<=', $dateStr)
            ->sum('amount_requested');

        $this->info("⏳ Pending Withdrawals: ₦" . number_format($pendingWithdrawals, 2));

        // 4. Calculate Platform Balance (total withdrawable balance across all wallets)
        $platformBalance = Wallet::sum('withdrawable_balance');

        $this->info("🏦 Platform Balance: ₦" . number_format($platformBalance, 2));

        // 5. Calculate Burn Rate (Withdrawals / Deposits)
        // Handle division by zero
        $burnRate = $totalDeposits > 0
            ? round($totalWithdrawals / $totalDeposits, 4)
            : ($totalWithdrawals > 0 ? 9.9999 : 0);

        $this->info("🔥 Burn Rate: " . $burnRate . " (" . ($burnRate * 100) . "%)");

        // 6. Determine Liquidity Status (using GlobalSetting helper)
        $liquidityStatus = $settings->getLiquidityStatus($burnRate);

        $statusEmoji = match($liquidityStatus) {
            'healthy' => '✅',
            'caution' => '⚠️',
            'critical' => '🚨',
            'collapse_imminent' => '☠️',
            default => '❓',
        };

        $this->info("{$statusEmoji} Liquidity Status: " . strtoupper($liquidityStatus));

        // 7. Calculate Consecutive Critical Days
        $consecutiveCriticalDays = 0;

        if ($liquidityStatus === 'collapse_imminent') {
            // Count consecutive days with burn_rate > critical_burn_rate
            $criticalThreshold = $settings->liquidity_settings['critical_burn_rate'] ?? 1.2;

            $recentDays = BurnRateHistory::where('report_date', '<', $dateStr)
                ->orderBy('report_date', 'desc')
                ->take(10)
                ->get();

            foreach ($recentDays as $day) {
                if ($day->burn_rate >= $criticalThreshold) {
                    $consecutiveCriticalDays++;
                } else {
                    break;
                }
            }

            $consecutiveCriticalDays++; // Include today
        }

        $this->info("📊 Consecutive Critical Days: {$consecutiveCriticalDays}");

        // 8. Calculate User Activity Metrics
        $activeUsersCount = Transaction::where('transaction_type', 'TASK_EARNING')
            ->whereDate('created_at', $dateStr)
            ->distinct('user_id')
            ->count('user_id');

        $newActivationsCount = Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
            ->where('status', 'COMPLETED')
            ->whereDate('created_at', $dateStr)
            ->count();

        $withdrawalRequestsCount = Withdrawal::whereDate('requested_at', $dateStr)->count();

        // 9. Store or Update Record
        $data = [
            'report_date' => $dateStr,
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'pending_withdrawals' => $pendingWithdrawals,
            'platform_balance' => $platformBalance,
            'burn_rate' => $burnRate,
            'liquidity_status' => $liquidityStatus,
            'consecutive_critical_days' => $consecutiveCriticalDays,
            'active_users_count' => $activeUsersCount,
            'new_activations_count' => $newActivationsCount,
            'withdrawal_requests_count' => $withdrawalRequestsCount,
            'thresholds_snapshot' => $settings->liquidity_settings,
            'metadata' => [
                'calculated_at' => now()->toDateTimeString(),
                'total_users' => \App\Models\User::count(),
                'total_wallets' => Wallet::count(),
            ],
        ];

        if ($existingRecord) {
            $existingRecord->update($data);
            $record = $existingRecord;
            $this->info('♻️  Updated existing record');
        } else {
            $record = BurnRateHistory::create($data);
            $this->info('✅ Created new record');
        }

        // 10. Send Admin Alerts (if thresholds breached)
        $this->sendAlertsIfNeeded($record, $settings);

        // 11. Auto-cleanup old records (keep last 180 days)
        $this->cleanupOldRecords();

        $this->info('');
        $this->info('✅ Burn Rate Calculation Complete!');

        return 0;
    }

    /**
     * Send admin alerts if liquidity thresholds are breached
     * Sends to all users with role = 1 (admins) using NotificationService
     */
    protected function sendAlertsIfNeeded(BurnRateHistory $record, GlobalSetting $settings)
    {
        // Don't send alert if already sent
        if ($record->admin_alerted) {
            $this->info('ℹ️  Admin already alerted for this date');
            return;
        }

        // Only alert for caution, critical, or collapse_imminent
        if (!in_array($record->liquidity_status, ['caution', 'critical', 'collapse_imminent'])) {
            return;
        }

        // Get all admin users (role = 1)
        $admins = User::where('role', 1)->get();

        if ($admins->isEmpty()) {
            $this->warn('⚠️  No admin users found (role = 1)');
            return;
        }

        $this->info("📧 Sending alerts to {$admins->count()} admin user(s)...");

        $subject = match($record->liquidity_status) {
            'caution' => '⚠️ Platform Liquidity: CAUTION Status',
            'critical' => '🚨 Platform Liquidity: CRITICAL Status',
            'collapse_imminent' => '☠️ URGENT: Platform Liquidity COLLAPSE IMMINENT',
            default => 'Platform Liquidity Alert',
        };

        $message = $this->buildAlertMessage($record, $settings);

        try {
            // Log alert
            Log::channel('daily')->warning("Burn Rate Alert: {$record->liquidity_status}", [
                'burn_rate' => $record->burn_rate,
                'consecutive_days' => $record->consecutive_critical_days,
                'date' => $record->report_date,
                'admin_count' => $admins->count(),
            ]);

            // Send notification to each admin using NotificationService
            $notificationService = app(NotificationService::class);
            $sentCount = 0;

            foreach ($admins as $admin) {
                try {
                    $results = $notificationService->send($admin, 'burn_rate_alert', [
                        'subject' => $subject,
                        'message' => $message,
                        'burn_rate' => $record->burn_rate,
                        'liquidity_status' => $record->liquidity_status,
                        'total_deposits' => $record->total_deposits,
                        'total_withdrawals' => $record->total_withdrawals,
                        'platform_balance' => $record->platform_balance,
                        'consecutive_critical_days' => $record->consecutive_critical_days,
                        'report_date' => $record->report_date,
                    ]);

                    $this->info("  ✅ Sent to: {$admin->full_name} ({$admin->email})");
                    $sentCount++;

                } catch (\Exception $e) {
                    $this->error("  ❌ Failed to send to {$admin->email}: " . $e->getMessage());
                }
            }

            $this->info("📨 Successfully sent {$sentCount}/{$admins->count()} notifications");

            // Mark as alerted
            $record->update([
                'admin_alerted' => true,
                'alerted_at' => now(),
            ]);

        } catch (\Exception $e) {
            $this->error("Failed to send alerts: " . $e->getMessage());
        }
    }

    /**
     * Build alert message for admin
     */
    protected function buildAlertMessage(BurnRateHistory $record, GlobalSetting $settings)
    {
        $currency = $settings->platform_currency ?? 'NGN';

        return "
Platform Liquidity Alert
Date: {$record->report_date}

📊 BURN RATE: {$record->burn_rate} (" . ($record->burn_rate * 100) . "%)
Status: " . strtoupper($record->liquidity_status) . "

💰 Financial Summary:
- Total Deposits: {$currency} " . number_format($record->total_deposits, 2) . "
- Total Withdrawals: {$currency} " . number_format($record->total_withdrawals, 2) . "
- Pending Withdrawals: {$currency} " . number_format($record->pending_withdrawals, 2) . "
- Platform Balance: {$currency} " . number_format($record->platform_balance, 2) . "

⚠️ Thresholds:
- Healthy: < {$settings->liquidity_settings['healthy_burn_rate']}
- Caution: < {$settings->liquidity_settings['caution_burn_rate']}
- Critical: < {$settings->liquidity_settings['critical_burn_rate']}

" . ($record->consecutive_critical_days > 0
    ? "🚨 CONSECUTIVE CRITICAL DAYS: {$record->consecutive_critical_days}\n\n"
    : "") . "

Recommended Actions:
" . match($record->liquidity_status) {
    'caution' => "- Monitor withdrawal approval rate\n- Consider slowing processing time to 48-72 hours",
    'critical' => "- URGENT: Add funds from bank forex to platform\n- Extend withdrawal processing time\n- Prioritize Diamond rank users only",
    'collapse_imminent' => "☠️ CRITICAL: Platform cannot sustain current withdrawal rate\n- Contact bank relationship manager IMMEDIATELY\n- Pause Bronze/Silver withdrawals\n- Announce 'high volume' processing delays",
    default => "- Continue monitoring",
} . "

Login to admin dashboard for details.
        ";
    }

    /**
     * Cleanup old records (keep last 180 days)
     */
    protected function cleanupOldRecords()
    {
        $cutoffDate = now()->subDays(180);

        $deleted = BurnRateHistory::where('report_date', '<', $cutoffDate)->delete();

        if ($deleted > 0) {
            $this->info("🧹 Cleaned up {$deleted} old records (older than 180 days)");
        }
    }
}
