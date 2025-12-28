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

        return Inertia::render('User/Referrals', [
            'user' => $user->load('rank'),
            'stats' => $stats,
            'treeData' => $treeData,
            'commissionRates' => $commissionRates,
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

        $node = [
            'id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email ?? $user->phone_number,
            'status' => $user->status,
            'rank' => $user->rank?->name ?? 'Bronze',
            'directReferrals' => $user->direct_referrals_count,
            'totalTeam' => $user->total_team_size,
            'activationDate' => $user->activation_date?->format('M d, Y'),
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
}
