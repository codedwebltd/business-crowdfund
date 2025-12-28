<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'order',
        'criteria',
        'benefits',
        'badge_color',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'criteria' => 'array',
        'benefits' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Users with this rank
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // ==================== SCOPES ====================

    /**
     * Active ranks only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Order by rank hierarchy
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if user meets rank criteria
     */
    public function userMeetsCriteria(User $user): bool
    {
        $criteria = $this->criteria;

        // Check minimum direct referrals
        if (isset($criteria['min_direct_referrals']) && $user->direct_referrals_count < $criteria['min_direct_referrals']) {
            return false;
        }

        // Check minimum team size
        if (isset($criteria['min_team_size']) && $user->total_team_size < $criteria['min_team_size']) {
            return false;
        }

        // Check minimum monthly volume (to be calculated from transactions)
        if (isset($criteria['min_monthly_volume'])) {
            // TODO: Calculate from transactions table
        }

        // Check minimum active direct referrals
        if (isset($criteria['min_active_directs'])) {
            $activeDirects = $user->directReferrals()
                ->where('last_task_completed_at', '>=', now()->subDays(7))
                ->count();

            if ($activeDirects < $criteria['min_active_directs']) {
                return false;
            }
        }

        // Check stability requirement (months at previous rank)
        if (isset($criteria['stability_months'])) {
            // TODO: Check rank_history table
        }

        return true;
    }

    /**
     * Get withdrawal minimum for this rank
     */
    public function getWithdrawalMin(): float
    {
        return $this->benefits['withdrawal_min'] ?? 5000;
    }

    /**
     * Get withdrawal maximum for this rank
     */
    public function getWithdrawalMax(): float
    {
        return $this->benefits['withdrawal_max'] ?? 50000;
    }

    /**
     * Get daily withdrawal limit
     */
    public function getDailyWithdrawalLimit(): int
    {
        return $this->benefits['daily_withdrawal_limit'] ?? 1;
    }

    /**
     * Get commission multiplier
     */
    public function getCommissionMultiplier(): float
    {
        return $this->benefits['commission_multiplier'] ?? 1.0;
    }

    /**
     * Get withdrawal processing hours
     */
    public function getProcessingHours(): int
    {
        return $this->benefits['processing_hours'] ?? 72;
    }
}
