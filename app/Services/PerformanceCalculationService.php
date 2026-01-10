<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPerformance;
use App\Models\GlobalSetting;
use App\Models\UserTask;
use Carbon\Carbon;

class PerformanceCalculationService
{
    /**
     * Calculate and update performance for a single user
     */
    public function calculateUserPerformance(User $user): UserPerformance
    {
        $settings = GlobalSetting::first();
        $starRequirements = $settings->star_requirements ?? $this->getDefaultStarRequirements();

        // Get user stats
        $stats = $this->getUserStats($user, $settings);

        // Calculate star rating based on requirements
        $starRating = $this->calculateStarRating($stats, $starRequirements);

        // Apply decay if user is inactive
        $starRating = $this->applyDecay($user, $starRating, $starRequirements);

        // Priority level matches star rating
        $priorityLevel = $starRating;

        // Get old star rating before update
        $oldPerformance = UserPerformance::where('user_id', $user->id)->first();
        $oldStarRating = $oldPerformance?->star_rating ?? 0;

        // Update or create performance record
        $performance = UserPerformance::updateOrCreate(
            ['user_id' => $user->id],
            [
                'tasks_completed_this_week' => $stats['tasks_this_week'],
                'referrals_this_week' => $stats['referrals_this_week'],
                'total_referrals' => $stats['total_referrals'],
                'direct_referrals' => $stats['direct_referrals'],
                'team_size' => $stats['team_size'],
                'referral_depth' => $stats['referral_depth'],
                'star_rating' => $starRating,
                'priority_level' => $priorityLevel,
                'last_calculated_at' => now(),
                'last_active_at' => $user->last_task_completed_at ?? $user->updated_at,
            ]
        );

        // Check if user qualifies for plan upgrade
        if ($starRating > $oldStarRating || $oldStarRating == 0) {
            $this->checkPlanUpgradeEligibility($user, $starRating);
        }

        return $performance;
    }

    /**
     * Get user statistics for performance calculation
     */
    protected function getUserStats(User $user, GlobalSetting $settings): array
    {
        // Tasks completed this week
        $tasksThisWeek = UserTask::where('user_id', $user->id)
            ->where('completed_at', '>=', now()->startOfWeek())
            ->where('status', 'COMPLETED')
            ->count();

        // Referrals this week
        $referralsThisWeek = User::where('referred_by_id', $user->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->count();

        // Total and direct referrals
        $totalReferrals = $user->total_team_size ?? 0;
        $directReferrals = $user->direct_referrals_count ?? 0;

        // Team size
        $teamSize = $user->total_team_size ?? 0;

        // Referral depth from global settings
        $referralDepth = $settings->referral_levels_depth ?? 10;

        return [
            'tasks_this_week' => $tasksThisWeek,
            'referrals_this_week' => $referralsThisWeek,
            'total_referrals' => $totalReferrals,
            'direct_referrals' => $directReferrals,
            'team_size' => $teamSize,
            'referral_depth' => $referralDepth,
        ];
    }

    /**
     * Calculate star rating based on user stats and requirements
     */
    protected function calculateStarRating(array $stats, array $requirements): int
    {
        // Check from 5 stars down to 1 star
        for ($stars = 5; $stars >= 1; $stars--) {
            $key = $stars . '_star';
            if (!isset($requirements[$key])) continue;

            $req = $requirements[$key];

            // Check if user meets all requirements for this star level
            if (
                $stats['tasks_this_week'] >= ($req['tasks_per_week'] ?? 0) &&
                $stats['referrals_this_week'] >= ($req['referrals_per_week'] ?? 0)
            ) {
                return $stars;
            }
        }

        // Default to 1 star
        return 1;
    }

    /**
     * Apply decay logic for inactive users
     */
    protected function applyDecay(User $user, int $currentStarRating, array $requirements): int
    {
        $decayDays = $requirements['decay_days'] ?? 4;

        // Get last activity date
        $lastActive = $user->last_task_completed_at ?? $user->updated_at;

        if (!$lastActive) {
            return 1; // No activity, minimum rating
        }

        $daysSinceActive = $lastActive->diffInDays(now());

        // Drop 1 star every decay_days of inactivity
        $starsToDeduct = (int)($daysSinceActive / $decayDays);

        if ($starsToDeduct > 0) {
            $newRating = max(1, $currentStarRating - $starsToDeduct);
            return $newRating;
        }

        return $currentStarRating;
    }

    /**
     * Calculate performance for all active users
     */
    public function calculateAllUsers(): int
    {
        $count = 0;

        User::where('status', 'ACTIVE')
            ->chunk(100, function ($users) use (&$count) {
                foreach ($users as $user) {
                    $this->calculateUserPerformance($user);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * Default star requirements if not set in global settings
     */
    protected function getDefaultStarRequirements(): array
    {
        return [
            '5_star' => [
                'tasks_per_week' => 8,
                'referrals_per_week' => 9,
            ],
            '4_star' => [
                'tasks_per_week' => 6,
                'referrals_per_week' => 5,
            ],
            '3_star' => [
                'tasks_per_week' => 4,
                'referrals_per_week' => 3,
            ],
            '2_star' => [
                'tasks_per_week' => 2,
                'referrals_per_week' => 1,
            ],
            '1_star' => [
                'tasks_per_week' => 0,
                'referrals_per_week' => 0,
            ],
            'decay_days' => 4,
        ];
    }

    /**
     * Get star rating change notification data
     */
    public function getStarChangeNotificationData(UserPerformance $oldPerformance, UserPerformance $newPerformance): ?array
    {
        if ($oldPerformance->star_rating === $newPerformance->star_rating) {
            return null; // No change
        }

        $change = $newPerformance->star_rating - $oldPerformance->star_rating;
        $isPromotion = $change > 0;

        return [
            'old_stars' => $oldPerformance->star_rating,
            'new_stars' => $newPerformance->star_rating,
            'change' => abs($change),
            'is_promotion' => $isPromotion,
            'message' => $isPromotion
                ? "Congratulations! You've been promoted to {$newPerformance->star_rating} stars! " . str_repeat('⭐', $newPerformance->star_rating)
                : "Your rating has dropped to {$newPerformance->star_rating} stars. Stay active to regain your rating!",
        ];
    }

    /**
     * Check if user qualifies for plan upgrade based on star rating
     */
    protected function checkPlanUpgradeEligibility(User $user, int $starRating): void
    {
        // Get qualified plan based on star rating using order field
        // This way renaming plans won't break the system
        // 1 star = Order 1, 2 stars = Order 2, etc.
        $qualifiedPlan = \App\Models\Plan::where('order', $starRating)->first();

        if (!$qualifiedPlan) {
            return; // Plan not found
        }

        $currentPlan = $user->plan;

        // Check if qualified plan is higher than current plan
        if ($qualifiedPlan->order > $currentPlan->order) {
            // User qualifies for an upgrade! Send notification
            $notificationService = app(NotificationService::class);
            $settings = GlobalSetting::first();
            $discount = $settings->plan_upgrade_discount_percentage ?? 20;

            $originalPrice = $qualifiedPlan->price;
            $discountedPrice = $originalPrice * (1 - $discount / 100);
            $savings = $originalPrice - $discountedPrice;

            $notificationService->send($user, 'plan_upgrade_available', [
                'qualified_plan' => $qualifiedPlan->display_name,
                'star_rating' => $starRating,
                'original_price' => $originalPrice,
                'discounted_price' => $discountedPrice,
                'discount_percentage' => $discount,
                'savings' => $savings,
            ]);
        }
    }
}
