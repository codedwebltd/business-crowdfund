<?php

namespace App\Observers;

use App\Helpers\CountryHelper;
use App\Models\User;
use App\Models\Wallet;
use App\Models\ReferralTree;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Auto-create wallet and referral tree node when user registers
     */
    public function created(User $user): void
    {
        // 1. Auto-create wallet with default currency based on country
        $currency = $this->getCurrencyFromCountry($user->country);

        Wallet::create([
            'user_id' => $user->id,
            'pending_balance' => 0,
            'withdrawable_balance' => 0,
            'total_earned' => 0,
            'total_withdrawn' => 0,
            'referral_balance' => 0,
            'bonus_balance' => 0,
            'locked_balance' => 0,
            'currency' => $currency,
        ]);

        // 2. Build referral tree node
        $referrer = $user->referred_by_id ? User::find($user->referred_by_id) : null;

        try {
            ReferralTree::buildForUser($user, $referrer);
        } catch (\Exception $e) {
            // Log error but don't fail user creation
            logger()->error("Failed to build referral tree for user {$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the User "updated" event.
     * Update referral counts when needed
     */
    public function updated(User $user): void
    {
        // Update direct referrals count if referrer exists
        if ($user->isDirty('status') && $user->status === 'ACTIVE' && $user->referred_by_id) {
            $referrer = User::find($user->referred_by_id);
            if ($referrer) {
                $referrer->increment('direct_referrals_count');

                // Update total team size for all upline
                $this->updateUplineTeamSizes($user);
            }
        }
    }

    /**
     * Update total_team_size for all upline users
     */
    private function updateUplineTeamSizes(User $user): void
    {
        $userNode = ReferralTree::where('user_id', $user->id)->first();

        if (!$userNode) return;

        $ancestors = ReferralTree::ancestorsOf($user->id)->get();

        foreach ($ancestors as $ancestorNode) {
            $ancestor = User::find($ancestorNode->user_id);
            if ($ancestor) {
                $ancestor->increment('total_team_size');
            }
        }
    }

    /**
     * Get currency code from country code using dynamic world-countries.json
     */
    private function getCurrencyFromCountry(?string $countryCode): string
    {
        if (!$countryCode) {
            return 'NGN'; // Fallback to Nigerian Naira
        }

        // Use CountryHelper to get currency dynamically from JSON
        return CountryHelper::getCurrencyCode($countryCode);
    }
}
