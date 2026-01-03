<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BurnRateHistory;
use App\Models\GlobalSetting;
use App\Models\TokenRateHistory;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class LiquidityController extends Controller
{
    public function index()
    {
        $settings = GlobalSetting::first();

        // Get burn rate history (last 30 days)
        $burnRateHistory = BurnRateHistory::orderBy('report_date', 'desc')
            ->take(30)
            ->get();

        // Calculate comprehensive earnings
        $totalActivations = Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $totalTaskEarnings = Transaction::where('transaction_type', 'TASK_EARNING')
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $totalReferralBonuses = Transaction::where('transaction_type', 'REFERRAL_BONUS')
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $totalWithdrawals = Withdrawal::where('status', 'COMPLETED')
            ->sum('amount_requested');

        $pendingWithdrawals = Withdrawal::whereIn('status', ['PENDING', 'PROCESSING', 'APPROVED'])
            ->sum('amount_requested');

        $platformBalance = Wallet::sum('withdrawable_balance');
        $totalPendingBalance = Wallet::sum('pending_balance');

        // User counts
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'ACTIVE')->count();

        // Today's stats
        $todayActivations = Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
            ->where('status', 'COMPLETED')
            ->whereDate('created_at', today())
            ->sum('amount');

        $todayWithdrawals = Withdrawal::where('status', 'COMPLETED')
            ->whereDate('processed_at', today())
            ->sum('amount_requested');

        $todayBurnRate = $todayActivations > 0
            ? round($todayWithdrawals / $todayActivations, 4)
            : 0;

        // Overall earnings summary
        $totalIncome = $totalActivations;
        $totalExpenses = $totalWithdrawals + $totalTaskEarnings + $totalReferralBonuses;
        $netProfit = $totalIncome - $totalExpenses;

        // Current liquidity status
        $latestBurnRate = $burnRateHistory->first();
        $liquidityStatus = $latestBurnRate
            ? $latestBurnRate->liquidity_status
            : 'healthy';

        // Calculate earnings by token rate periods
        $tokenRatePeriods = $this->calculateTokenRatePeriods();

        return Inertia::render('Admin/Liquidity', [
            'settings' => $settings,
            'burnRateHistory' => $burnRateHistory,
            'tokenRatePeriods' => $tokenRatePeriods,
            'stats' => [
                'total_activations' => $totalActivations,
                'total_task_earnings' => $totalTaskEarnings,
                'total_referral_bonuses' => $totalReferralBonuses,
                'total_withdrawals' => $totalWithdrawals,
                'pending_withdrawals' => $pendingWithdrawals,
                'platform_balance' => $platformBalance,
                'total_pending_balance' => $totalPendingBalance,
                'today_activations' => $todayActivations,
                'today_withdrawals' => $todayWithdrawals,
                'today_burn_rate' => $todayBurnRate,
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net_profit' => $netProfit,
                'liquidity_status' => $liquidityStatus,
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
            ],
            'currencySymbol' => $settings->platform_currency === 'NGN' ? '₦' : '$',
        ]);
    }

    /**
     * Calculate earnings and withdrawals by token rate periods
     */
    protected function calculateTokenRatePeriods()
    {
        $tokenRates = TokenRateHistory::orderBy('created_at', 'asc')->get();
        $periods = [];

        foreach ($tokenRates as $index => $rate) {
            $periodStart = $rate->created_at;
            $periodEnd = isset($tokenRates[$index + 1])
                ? $tokenRates[$index + 1]->created_at
                : now();

            // Activations during this period (Revenue in NGN)
            $activations = Transaction::where('transaction_type', 'ACTIVATION_PAYMENT')
                ->where('status', 'COMPLETED')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum('amount');

            // Withdrawals during this period
            $withdrawals = Withdrawal::where('status', 'COMPLETED')
                ->whereBetween('processed_at', [$periodStart, $periodEnd])
                ->get();

            $withdrawalCount = $withdrawals->count();
            $totalWithdrawalNGN = $withdrawals->sum('amount_requested');

            // Calculate token value paid out
            // Users receive tokens based on withdrawal_rate
            // withdrawal_rate = 0.68 means user gets 68% in tokens, platform keeps 32%
            $tokensPaidOut = $totalWithdrawalNGN / $rate->token_price; // Total tokens
            $tokensToUsers = $tokensPaidOut * $rate->withdrawal_rate; // Tokens actually given
            $tokensRetained = $tokensPaidOut * (1 - $rate->withdrawal_rate); // Platform keeps

            // Platform profit from this period
            // Profit = Activations - (Tokens retained value) - Task earnings - Referrals
            $taskEarnings = Transaction::where('transaction_type', 'TASK_EARNING')
                ->where('status', 'COMPLETED')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum('amount');

            $referralBonuses = Transaction::where('transaction_type', 'REFERRAL_BONUS')
                ->where('status', 'COMPLETED')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->sum('amount');

            $periodExpenses = $totalWithdrawalNGN + $taskEarnings + $referralBonuses;
            $periodProfit = $activations - $periodExpenses;

            $periods[] = [
                'rate_id' => $rate->id,
                'period_start' => $periodStart->format('Y-m-d H:i'),
                'period_end' => $index === count($tokenRates) - 1 ? 'Current' : $periodEnd->format('Y-m-d H:i'),
                'token_price' => $rate->token_price,
                'withdrawal_rate' => $rate->withdrawal_rate,
                'duration_days' => $periodStart->diffInDays($periodEnd),
                'activations_ngn' => $activations,
                'withdrawals_count' => $withdrawalCount,
                'withdrawals_ngn' => $totalWithdrawalNGN,
                'tokens_paid_out' => round($tokensPaidOut, 2),
                'tokens_to_users' => round($tokensToUsers, 2),
                'tokens_retained' => round($tokensRetained, 2),
                'task_earnings' => $taskEarnings,
                'referral_bonuses' => $referralBonuses,
                'total_expenses' => $periodExpenses,
                'period_profit' => $periodProfit,
                'profit_margin' => $activations > 0 ? round(($periodProfit / $activations) * 100, 2) : 0,
            ];
        }

        return $periods;
    }

    public function destroy($id)
    {
        $record = BurnRateHistory::findOrFail($id);
        $record->delete();

        return redirect()->route('admin.liquidity.index')
            ->with('success', 'Burn rate record deleted successfully.');
    }
}
