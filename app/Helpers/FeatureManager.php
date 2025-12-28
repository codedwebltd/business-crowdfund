<?php

namespace App\Helpers;

use App\Models\User;

/**
 * Smart Feature Manager for AI Training Platform
 * Dynamically tracks Plan features + Rank benefits
 * Admin can add ANY feature without code changes!
 */
class FeatureManager
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Check if user has a specific feature (from plan OR rank)
     * Usage: $features->has('priority_support')
     */
    public function has(string $feature): bool
    {
        // Check plan features
        if ($this->user->plan && isset($this->user->plan->features[$feature])) {
            return (bool) $this->user->plan->features[$feature];
        }

        // Check rank benefits
        if ($this->user->rank && isset($this->user->rank->benefits[$feature])) {
            return (bool) $this->user->rank->benefits[$feature];
        }

        return false;
    }

    /**
     * Get feature value (numeric limits, multipliers, etc.)
     * Usage: $features->get('max_daily_tasks', 8)
     */
    public function get(string $feature, $default = null)
    {
        // Priority: Plan features first, then Rank benefits
        if ($this->user->plan && isset($this->user->plan->features[$feature])) {
            return $this->user->plan->features[$feature];
        }

        if ($this->user->rank && isset($this->user->rank->benefits[$feature])) {
            return $this->user->rank->benefits[$feature];
        }

        return $default;
    }

    /**
     * Get feature from rank benefits only
     */
    public function getRankBenefit(string $feature, $default = null)
    {
        return $this->user->rank->benefits[$feature] ?? $default;
    }

    /**
     * Get feature from plan only
     */
    public function getPlanFeature(string $feature, $default = null)
    {
        return $this->user->plan->features[$feature] ?? $default;
    }

    // ==================== TASK FEATURES ====================

    /**
     * Get max daily tasks allowed
     */
    public function getMaxDailyTasks(): int
    {
        return $this->get('max_daily_tasks', 8);
    }

    /**
     * Get task reward multiplier
     */
    public function getTaskRewardMultiplier(): float
    {
        return $this->get('task_reward_multiplier', 1.0);
    }

    /**
     * Check if user can complete more tasks today
     */
    public function canCompleteTask(): bool
    {
        $maxTasks = $this->getMaxDailyTasks();
        $completedToday = $this->user->tasks()
            ->where('status', 'COMPLETED')
            ->whereDate('completed_at', today())
            ->count();

        return $completedToday < $maxTasks;
    }

    /**
     * Get remaining tasks for today
     */
    public function remainingTasks(): int
    {
        $maxTasks = $this->getMaxDailyTasks();
        $completedToday = $this->user->tasks()
            ->where('status', 'COMPLETED')
            ->whereDate('completed_at', today())
            ->count();

        return max(0, $maxTasks - $completedToday);
    }

    // ==================== WITHDRAWAL FEATURES ====================

    /**
     * Get minimum withdrawal amount (from rank)
     */
    public function getWithdrawalMin(): float
    {
        return $this->getRankBenefit('withdrawal_min', 5000);
    }

    /**
     * Get maximum withdrawal amount (from rank)
     */
    public function getWithdrawalMax(): float
    {
        return $this->getRankBenefit('withdrawal_max', 50000);
    }

    /**
     * Get daily withdrawal limit (how many times per day)
     */
    public function getDailyWithdrawalLimit(): int
    {
        return $this->getRankBenefit('withdrawals_per_day', 1);
    }

    /**
     * Get withdrawal processing hours
     */
    public function getProcessingHours(): int
    {
        return $this->getRankBenefit('processing_hours', 72);
    }

    /**
     * Check if user can withdraw
     */
    public function canWithdraw(float $amount): array
    {
        $errors = [];

        // Check minimum
        if ($amount < $this->getWithdrawalMin()) {
            $errors[] = "Minimum withdrawal is ₦" . number_format($this->getWithdrawalMin());
        }

        // Check maximum
        if ($amount > $this->getWithdrawalMax()) {
            $errors[] = "Maximum withdrawal is ₦" . number_format($this->getWithdrawalMax());
        }

        // Check balance
        if ($amount > $this->user->wallet->withdrawable_balance) {
            $errors[] = "Insufficient withdrawable balance";
        }

        // Check daily limit
        $todayWithdrawals = $this->user->withdrawals()
            ->whereDate('created_at', today())
            ->count();

        if ($todayWithdrawals >= $this->getDailyWithdrawalLimit()) {
            $errors[] = "Daily withdrawal limit reached";
        }

        return [
            'can_withdraw' => empty($errors),
            'errors' => $errors,
        ];
    }

    // ==================== COMMISSION FEATURES ====================

    /**
     * Get commission multiplier (from rank)
     */
    public function getCommissionMultiplier(): float
    {
        return $this->getRankBenefit('commission_multiplier', 1.0);
    }

    /**
     * Calculate commission with rank multiplier
     */
    public function calculateCommission(float $baseAmount): float
    {
        return $baseAmount * $this->getCommissionMultiplier();
    }

    // ==================== PREMIUM FEATURES ====================

    /**
     * Check if user has priority support
     */
    public function hasPrioritySupport(): bool
    {
        return $this->has('priority_support');
    }

    /**
     * Check if user has custom badge
     */
    public function hasCustomBadge(): bool
    {
        return $this->has('custom_badge');
    }

    /**
     * Check if user has WhatsApp VIP access
     */
    public function hasWhatsAppVIP(): bool
    {
        return $this->has('whatsapp_vip_group');
    }

    /**
     * Check if user has instant withdrawals
     */
    public function hasInstantWithdrawal(): bool
    {
        return $this->has('instant_withdrawal');
    }

    // ==================== GENERIC HELPERS ====================

    /**
     * Get all features (plan + rank combined)
     */
    public function all(): array
    {
        $planFeatures = $this->user->plan->features ?? [];
        $rankBenefits = $this->user->rank->benefits ?? [];

        return array_merge($rankBenefits, $planFeatures); // Plan overrides rank
    }

    /**
     * Check if user's plan/rank is active
     */
    public function isActive(): bool
    {
        return $this->user->isActive() && !is_null($this->user->plan_id);
    }

    /**
     * Get plan name
     */
    public function planName(): string
    {
        return $this->user->plan?->display_name ?? 'No Plan';
    }

    /**
     * Get rank name
     */
    public function rankName(): string
    {
        return $this->user->rank?->display_name ?? 'Bronze';
    }

    /**
     * Get formatted plan + rank display
     */
    public function displayTier(): string
    {
        return "{$this->planName()} - {$this->rankName()}";
    }

    /**
     * Magic method for dynamic feature checking
     * Usage: $features->hasExpressWithdrawal()
     */
    public function __call($method, $args)
    {
        // Handle has* methods
        if (str_starts_with($method, 'has')) {
            $feature = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($method, 3)));
            return $this->has($feature);
        }

        // Handle get* methods
        if (str_starts_with($method, 'get')) {
            $feature = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($method, 3)));
            return $this->get($feature, $args[0] ?? null);
        }

        throw new \BadMethodCallException("Method {$method} does not exist");
    }
}
