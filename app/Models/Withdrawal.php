<?php

namespace App\Models;

use App\Helpers\CountryHelper;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Withdrawal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'amount_requested', 'meta_data', 'payment_method', 'bank_name', 'account_number',
        'account_name', 'wallet_details', 'status', 'requested_at', 'approved_at',
        'processed_at', 'rejected_at', 'approved_by_id', 'admin_notes', 'rejection_reason',
        'transaction_reference', 'priority_score', 'is_first_withdrawal',
        'testimonial_submitted', 'processing_hours',
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'meta_data' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'wallet_details' => 'array',
        'priority_score' => 'integer',
        'is_first_withdrawal' => 'boolean',
        'testimonial_submitted' => 'boolean',
        'processing_hours' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'reference');
    }

    // ==================== SCOPES ====================

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'PROCESSING');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'COMPLETED');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'REJECTED');
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority_score', 'desc')->orderBy('requested_at', 'asc');
    }

    // ==================== VALIDATION METHODS ====================

    /**
     * Check if user meets withdrawal requirements
     * Based on map.md validation rules
     */
    public static function canUserWithdraw(User $user, float $amount): array
    {
        $settings = GlobalSetting::first();
        $errors = [];

        // Get platform currency dynamically from global_settings
        $platformCountry = $settings->country_of_operation ?? 'NGA';
        $currencySymbol = CountryHelper::getCurrencySymbol($platformCountry);

        // Get global withdrawal limits (baseline/override)
        $globalMin = $settings->minimum_withdrawal ?? 5000;
        $globalMax = $settings->maximum_withdrawal ?? 50000;
        $globalPerDay = $settings->withdrawals_per_day ?? 1;

        // Get rank-specific limits (if user has rank)
        $rankLimits = $user->rank ? ($settings->withdrawal_limits_by_rank[$user->rank_id] ?? null) : null;

        // Apply stricter-wins logic: Global overrides when stricter
        $minAmount = $rankLimits ? max($globalMin, $rankLimits['min']) : $globalMin;
        $maxAmount = $rankLimits ? min($globalMax, $rankLimits['max']) : $globalMax;
        $dailyLimit = $rankLimits ? min($globalPerDay, $rankLimits['per_day'] ?? $globalPerDay) : $globalPerDay;

        // 1. Check minimum withdrawal
        if ($amount < $minAmount) {
            $errors[] = "Minimum withdrawal for your rank is {$currencySymbol}" . number_format($minAmount, 2);
        }

        // 2. Check maximum withdrawal
        if ($amount > $maxAmount) {
            $errors[] = "Maximum withdrawal for your rank is {$currencySymbol}" . number_format($maxAmount, 2);
        }

        // 3. Check sufficient balance
        if ($amount > $user->wallet->withdrawable_balance) {
            $errors[] = "Insufficient withdrawable balance. Available: " . CountryHelper::formatMoney($user->wallet->withdrawable_balance, $platformCountry);
        }

        // 4. Check daily withdrawal limit
        $todayCount = self::where('user_id', $user->id)
            ->whereDate('requested_at', today())
            ->whereIn('status', ['PENDING', 'PROCESSING', 'APPROVED', 'COMPLETED'])
            ->count();

        if ($todayCount >= $dailyLimit) {
            $errors[] = "Daily withdrawal limit reached ({$dailyLimit} per day for your rank)";
        }

        // 5. Check account status
        if ($user->status !== 'ACTIVE') {
            $errors[] = "Account must be ACTIVE to withdraw";
        }

        // 6. Check KYC requirement (if amount exceeds threshold)
        $kycThreshold = $settings->kyc_withdrawal_threshold ?? 50000;
        if ($amount > $kycThreshold && !$user->hasCompletedKYC()) {
            $errors[] = "KYC verification required for withdrawals above " . CountryHelper::formatMoney($kycThreshold, $platformCountry);
        }

        // 7. Check testimonial requirement for first withdrawal
        $isFirstWithdrawal = self::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->doesntExist();

        if ($isFirstWithdrawal && $settings->require_testimonial_first_withdrawal ?? true) {
            // Check if user has submitted testimonial
            $hasTestimonial = Testimonial::where('user_id', $user->id)
                ->where('status', 'APPROVED')
                ->exists();

            if (!$hasTestimonial) {
                $errors[] = "You must submit a testimonial before your first withdrawal";
            }
        }

        return [
            'can_withdraw' => empty($errors),
            'errors' => $errors,
            'is_first_withdrawal' => $isFirstWithdrawal,
        ];
    }

    /**
     * Calculate priority score for processing queue
     * Based on UserPerformance star rating system
     */
    public static function calculatePriorityScore(User $user): int
    {
        $score = 0;

        // Star rating priority (5-star users processed first)
        $performance = $user->performance;
        if ($performance) {
            // Star rating contributes most weight (1-5 stars = 20-100 points)
            $score += ($performance->star_rating * 20);

            // Team size bonus (big recruiters get priority)
            $score += intval($performance->team_size / 10);

            // Direct referrals bonus
            $score += ($performance->direct_referrals * 2);
        }

        // Account age bonus (older accounts slightly prioritized)
        if ($user->activation_date) {
            $daysActive = $user->activation_date->diffInDays(now());
            $score += intval($daysActive / 7);
        }

        // Previous withdrawal success (reliable users prioritized)
        $successfulWithdrawals = self::where('user_id', $user->id)
            ->where('status', 'COMPLETED')
            ->count();
        $score += ($successfulWithdrawals * 5);

        // Penalty for high withdrawal frequency
        $recentWithdrawals = self::where('user_id', $user->id)
            ->where('requested_at', '>=', now()->subDays(7))
            ->count();
        if ($recentWithdrawals > 3) {
            $score -= 20;
        }

        return max(0, $score); // Never negative
    }

    /**
     * Get priority level from user performance (1-5)
     */
    public function getPriorityLevel(): int
    {
        return $this->user->performance?->priority_level ?? 1;
    }

    // ==================== STATUS MANAGEMENT ====================

    public function approve(User $admin, ?string $notes = null): void
    {
        $this->update([
            'status' => 'APPROVED',
            'approved_at' => now(),
            'approved_by_id' => $admin->id,
            'admin_notes' => $notes,
        ]);

        // Create transaction record
        Transaction::create([
            'user_id' => $this->user_id,
            'transaction_type' => 'WITHDRAWAL',
            'balance_type' => 'WITHDRAWABLE',
            'amount' => $this->amount_requested,
            'priority' => $this->user->getPriorityLevel(),
            'is_credit' => false,
            'status' => 'COMPLETED',
            'description' => "Withdrawal approved - {$this->payment_method}",
            'reference_type' => self::class,
            'reference_id' => $this->id,
        ]);

        // TODO: Notify user
    }

    public function complete(?string $transactionRef = null): void
    {
        $processingHours = $this->requested_at->diffInHours(now());

        $this->update([
            'status' => 'COMPLETED',
            'processed_at' => now(),
            'transaction_reference' => $transactionRef,
            'processing_hours' => $processingHours,
        ]);

        // Update user wallet
        $this->user->wallet->increment('total_withdrawn', $this->amount_requested);

        // TODO: Notify user
    }

    public function reject(User $admin, string $reason): void
    {
        $this->update([
            'status' => 'REJECTED',
            'rejected_at' => now(),
            'approved_by_id' => $admin->id,
            'rejection_reason' => $reason,
        ]);

        // Refund user's balance
        $this->user->wallet->increment('withdrawable_balance', $this->amount_requested);

        // Create refund transaction
        Transaction::create([
            'user_id' => $this->user_id,
            'transaction_type' => 'WITHDRAWAL_REFUND',
            'balance_type' => 'WITHDRAWABLE',
            'amount' => $this->amount_requested,
            'priority' => $this->user->getPriorityLevel(),
            'is_credit' => true,
            'status' => 'COMPLETED',
            'description' => "Withdrawal rejected: {$reason}",
            'reference_type' => self::class,
            'reference_id' => $this->id,
        ]);

        // TODO: Notify user
    }

    // ==================== HELPER METHODS ====================

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'COMPLETED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }

    public function requiresTestimonial(): bool
    {
        return $this->is_first_withdrawal && !$this->testimonial_submitted;
    }
}
