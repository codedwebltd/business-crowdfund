<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ReferralTree;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $settings = GlobalSetting::first();

        // Get user's referral tree node
        $userNode = ReferralTree::where('user_id', $user->id)->first();

        // Stats
        $stats = [
            'referralCode' => $user->referral_code,
            'referralLink' => url("/register?ref={$user->referral_code}"),
            'directReferrals' => $user->direct_referrals_count,
            'totalTeam' => $user->total_team_size,
            'activationEarnings' => $this->getActivationEarnings($user->id),
            'taskEarnings' => $this->getTaskEarnings($user->id),
            'totalEarnings' => $this->getTotalReferralEarnings($user->id),
            'teamByLevel' => $this->getTeamByLevel($user->id),
        ];

        // Get tree data for visualization (first 3 levels for performance)
        $treeData = $this->buildTreeData($user->id, 3);

        // Commission rates
        $commissionRates = [
            'activation' => $settings->commission_rates['activation'] ?? [],
            'taskEarnings' => $settings->commission_rates['task_earnings'] ?? [],
            'maxLevels' => $settings->referral_levels_depth,
        ];

        // Network-wide statistics (only for agents)
        $networkStats = null;
        if ($user->isAgent()) {
            $networkStats = $this->getNetworkStats($user->id);
        }

        return Inertia::render('User/Referrals', [
            'user' => $user->load('rank'),
            'stats' => $stats,
            'treeData' => $treeData,
            'commissionRates' => $commissionRates,
            'networkStats' => $networkStats,
            'currencyCode' => $settings->platform_currency ?? 'NGN',
            'currencySymbol' => \App\Helpers\CountryHelper::getCurrencySymbol($settings->country_of_operation ?? 'NGA'),
        ]);
    }

    private function getActivationEarnings($userId)
    {
        return \DB::table('transactions')
            ->where('user_id', $userId)
            ->where('transaction_type', 'REFERRAL_ACTIVATION')
            ->where('status', 'COMPLETED')
            ->sum('amount');
    }

    private function getTaskEarnings($userId)
    {
        return \DB::table('transactions')
            ->where('user_id', $userId)
            ->where('transaction_type', 'REFERRAL_TASK')
            ->where('status', 'COMPLETED')
            ->sum('amount');
    }

    private function getTotalReferralEarnings($userId)
    {
        return \DB::table('transactions')
            ->where('user_id', $userId)
            ->whereIn('transaction_type', ['REFERRAL_ACTIVATION', 'REFERRAL_TASK'])
            ->where('status', 'COMPLETED')
            ->sum('amount');
    }

    private function getTeamByLevel($userId)
    {
        if (!ReferralTree::where('user_id', $userId)->exists()) {
            return [];
        }

        return ReferralTree::descendantsOf($userId)
            ->selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->pluck('count', 'level')
            ->toArray();
    }

    private function buildTreeData($userId, $maxDepth = 3)
    {
        $user = \App\Models\User::find($userId);

        if (!$user) return null;

        // Calculate Total Earned for this user
        $totalEarned = \DB::table('transactions')
            ->where('user_id', $user->id)
            ->whereIn('transaction_type', ['TASK_EARNING', 'REFERRAL_BONUS', 'TEAM_COMMISSION'])
            ->where('status', 'COMPLETED')
            ->where('is_credit', true)
            ->sum('amount');

        // Calculate Total Deposited for this user (including upgrades)
        $totalDeposited = \DB::table('transactions')
            ->where('user_id', $user->id)
            ->whereIn('transaction_type', ['ACTIVATION_PAYMENT', 'PLAN_UPGRADE'])
            ->where('status', 'COMPLETED')
            ->where('is_credit', true)
            ->sum('amount');

        $node = [
            'id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email ?? $user->phone_number,
            'status' => $user->status,
            'rank' => $user->rank?->name ?? 'Bronze',
            'directReferrals' => $user->direct_referrals_count,
            'totalTeam' => $user->total_team_size,
            'activationDate' => $user->activation_date?->format('M d, Y'),
            'totalEarned' => (float) $totalEarned,
            'totalDeposited' => (float) $totalDeposited,
            'children' => []
        ];

        if ($maxDepth > 0) {
            $directReferrals = $user->directReferrals()->get();
            foreach ($directReferrals as $referral) {
                $node['children'][] = $this->buildTreeData($referral->id, $maxDepth - 1);
            }
        }

        return $node;
    }

    public function getSubTree(Request $request)
    {
        $userId = $request->input('user_id');
        $depth = $request->input('depth', 2);

        $treeData = $this->buildTreeData($userId, $depth);

        return response()->json(['tree' => $treeData]);
    }

    /**
     * Get network-wide statistics for the entire referral tree
     * Used for agents to see their network's financial performance
     */
    private function getNetworkStats($userId)
    {
        // Get all user IDs in the referral network (user + all descendants)
        $networkUserIds = $this->getAllNetworkUserIds($userId);

        // Total Deposited by entire network (including upgrades)
        $totalNetworkDeposits = \DB::table('transactions')
            ->whereIn('user_id', $networkUserIds)
            ->whereIn('transaction_type', ['ACTIVATION_PAYMENT', 'PLAN_UPGRADE'])
            ->where('status', 'COMPLETED')
            ->where('is_credit', true)
            ->sum('amount');

        // Total Withdrawals by entire network
        $totalNetworkWithdrawals = \DB::table('transactions')
            ->whereIn('user_id', $networkUserIds)
            ->where('transaction_type', 'WITHDRAWAL')
            ->where('status', 'COMPLETED')
            ->where('is_credit', false)
            ->sum('amount');

        // Net Gain for the parent
        $netGain = $totalNetworkDeposits - $totalNetworkWithdrawals;

        // Additional breakdown stats
        $totalActivations = \DB::table('transactions')
            ->whereIn('user_id', $networkUserIds)
            ->where('transaction_type', 'ACTIVATION_PAYMENT')
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $totalUpgrades = \DB::table('transactions')
            ->whereIn('user_id', $networkUserIds)
            ->where('transaction_type', 'PLAN_UPGRADE')
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $totalTaskEarnings = \DB::table('transactions')
            ->whereIn('user_id', $networkUserIds)
            ->where('transaction_type', 'TASK_EARNING')
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $totalReferralBonuses = \DB::table('transactions')
            ->whereIn('user_id', $networkUserIds)
            ->where('transaction_type', 'REFERRAL_BONUS')
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $totalTeamCommissions = \DB::table('transactions')
            ->whereIn('user_id', $networkUserIds)
            ->where('transaction_type', 'TEAM_COMMISSION')
            ->where('status', 'COMPLETED')
            ->sum('amount');

        return [
            'totalNetworkDeposits' => (float) $totalNetworkDeposits,
            'totalNetworkWithdrawals' => (float) $totalNetworkWithdrawals,
            'netGain' => (float) $netGain,
            'totalActivations' => (float) $totalActivations,
            'totalUpgrades' => (float) $totalUpgrades,
            'totalTaskEarnings' => (float) $totalTaskEarnings,
            'totalReferralBonuses' => (float) $totalReferralBonuses,
            'totalTeamCommissions' => (float) $totalTeamCommissions,
            'networkSize' => count($networkUserIds),
        ];
    }

    /**
     * Get all user IDs in the referral network (including the parent)
     */
    private function getAllNetworkUserIds($userId)
    {
        $userIds = [$userId]; // Start with the parent

        // Get all descendants recursively
        $descendants = \App\Models\User::where('id', $userId)
            ->first()
            ?->directReferrals()
            ->pluck('id')
            ->toArray() ?? [];

        foreach ($descendants as $descendantId) {
            $userIds = array_merge($userIds, $this->getAllNetworkUserIds($descendantId));
        }

        return array_unique($userIds);
    }

    /**
     * Hardwork Stats Page (Agents Only)
     * Comprehensive analytics dashboard for agent's referral network
     */
    public function hardworkStats()
    {
        $user = auth()->user();
        $settings = GlobalSetting::first();

        // Only agents can access this page
        if (!$user->isAgent()) {
            return redirect()->route('user.referrals')
                ->with('error', 'This page is only accessible to agents.');
        }

        // Get comprehensive network stats
        $networkStats = $this->getNetworkStats($user->id);

        // Calculate additional performance metrics
        $totalEarnings = $networkStats['totalTaskEarnings'] +
                        $networkStats['totalReferralBonuses'] +
                        $networkStats['totalTeamCommissions'];

        $avgDepositPerMember = $networkStats['networkSize'] > 0
            ? $networkStats['totalNetworkDeposits'] / $networkStats['networkSize']
            : 0;

        $avgEarningsPerMember = $networkStats['networkSize'] > 0
            ? $totalEarnings / $networkStats['networkSize']
            : 0;

        $retentionRate = $networkStats['totalNetworkDeposits'] > 0
            ? (($networkStats['totalNetworkDeposits'] - $networkStats['totalNetworkWithdrawals']) / $networkStats['totalNetworkDeposits']) * 100
            : 0;

        $stats = array_merge($networkStats, [
            'totalEarnings' => $totalEarnings,
            'avgDepositPerMember' => round($avgDepositPerMember, 2),
            'avgEarningsPerMember' => round($avgEarningsPerMember, 2),
            'retentionRate' => round($retentionRate, 1),
        ]);

        return Inertia::render('User/HardworkStats', [
            'stats' => $stats,
            'currencySymbol' => \App\Helpers\CountryHelper::getCurrencySymbol($settings->country_of_operation ?? 'NGA'),
        ]);
    }
}
