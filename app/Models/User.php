<?php

namespace App\Models;

use App\Traits\LocationTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes, LocationTrait;

    protected $fillable = [
        'phone_number', 'full_name', 'email', 'date_of_birth', 'password', 'phone_verified_at',
        'otp_code', 'otp_expires_at', 'google2fa_secret', 'google2fa_enabled', 'backup_codes',
        'role', 'bank_name', 'account_number', 'account_name', 'wallet_details',
        'referral_code', 'referred_by_id', 'plan_id', 'activation_amount', 'activation_date',
        'status', 'rank_id', 'direct_referrals_count', 'total_team_size',
        'last_task_completed_at', 'last_login_at', 'country', 'nin', 'bvn',
        'utility_bill_path', 'selfie_path', 'kyc_verified_at', 'password_confirmation',
        'task_ban_until', 'device_fingerprint','notification_preferences',
    ];

    protected $hidden = ['password', 'remember_token', 'otp_code', 'google2fa_secret', 'backup_codes', 'password_confirmation'];

    protected $casts = [
        'phone_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'password' => 'hashed',
        'wallet_details' => 'array',
        'notification_preferences' => 'array',
        'backup_codes' => 'array',
        'activation_amount' => 'decimal:2',
        'activation_date' => 'datetime',
        'otp_expires_at' => 'datetime',
        'google2fa_enabled' => 'boolean',
        'last_task_completed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'kyc_verified_at' => 'datetime',
        'task_ban_until' => 'datetime',
        'device_fingerprint' => 'array',
        'direct_referrals_count' => 'integer',
        'total_team_size' => 'integer',
    ];

    // Relationships
    public function rank() { return $this->belongsTo(Rank::class); }
    public function referrer() { return $this->belongsTo(User::class, 'referred_by_id'); }
    public function directReferrals() { return $this->hasMany(User::class, 'referred_by_id'); }
    public function plan() { return $this->belongsTo(Plan::class); }
    public function wallet() { return $this->hasOne(Wallet::class); }
    public function referralTree() { return $this->hasOne(ReferralTree::class); }
    public function subscriptions() { return $this->hasMany(UserSubscription::class); }
    public function activeSubscription() { return $this->hasOne(UserSubscription::class)->where('status', 'ACTIVE')->latest(); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function tasks() { return $this->hasMany(UserTask::class); }
    public function withdrawals() { return $this->hasMany(Withdrawal::class); }
    public function ipAddresses() { return $this->hasMany(UserIpAddress::class); }
    public function fraudIncidents() { return $this->hasMany(FraudIncident::class); }
    public function testimonials() { return $this->hasMany(Testimonial::class); }
    public function kycVerifications() { return $this->hasMany(KycVerification::class); }
    public function latestKyc() { return $this->hasOne(KycVerification::class)->latest(); }
    public function dismissedAnnouncements() { return $this->belongsToMany(Announcement::class, 'announcement_users')->withTimestamps(); }

    // Scopes
    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeByRank($query, $rankId) { return $query->where('rank_id', $rankId); }
    public function scopeRecentlyActive($query) { return $query->where('last_task_completed_at', '>=', now()->subDays(7)); }
    public function scopeWith2FA($query) { return $query->where('google2fa_enabled', true); }

    // Helper Methods
    public function isAdmin(): bool { return $this->role === 1; }
    public function isPhoneVerified(): bool { return !is_null($this->phone_verified_at); }
    public function isActive(): bool { return $this->status === 'ACTIVE'; }
    public function hasCompletedKYC(): bool { return !is_null($this->kyc_verified_at); }
    public function has2FAEnabled(): bool { return $this->google2fa_enabled && !is_null($this->google2fa_secret); }

    public function isAtLeast18(): bool
    {
        if (!$this->date_of_birth) return false;
        return $this->date_of_birth->age >= 18;
    }

    public function isOTPValid(string $code): bool
    {
        return $this->otp_code === $code && $this->otp_expires_at && $this->otp_expires_at->isFuture();
    }

    public function generateOTP(int $expiryMinutes = 10): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update(['otp_code' => $otp, 'otp_expires_at' => now()->addMinutes($expiryMinutes)]);
        return $otp;
    }

    public function clearOTP(): void
    {
        $this->update(['otp_code' => null, 'otp_expires_at' => null]);
    }

    /**
     * Get FeatureManager instance for this user
     * Usage: $user->features()->getMaxDailyTasks()
     *        $user->features()->has('priority_support')
     *        $user->features()->canWithdraw(10000)
     */
    public function features()
    {
        return new \App\Helpers\FeatureManager($this);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Auto-detect country from IP using LocationTrait
            if (empty($user->country)) {
                $locationData = (new self)->extractLocationData();
                $user->country = $locationData['country']['code'] ?? config('app.default_country', 'NGA');
            }

            // Generate referral code with detected country
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateReferralCode($user->country);
            }

            // NO rank assigned during registration
            // Rank assigned to Bronze on first activation (when subscription is paid)
            // After that, rank evaluated daily by cron based on performance
        });
    }

    /**
     * Assign Bronze rank on first activation (called after payment)
     */
    public function assignBronzeRank(): void
    {
        if (is_null($this->rank_id)) {
            $bronzeRank = Rank::where('is_active', true)->orderBy('order')->first();
            $this->update(['rank_id' => $bronzeRank?->id]);
        }
    }

    /**
     * Generate referral code: CP-{COUNTRY}-{RANDOM}
     * Example: CP-NGA-847392, CP-GHA-456789, CP-KEN-123456
     */
    public static function generateReferralCode(string $country): string
    {
        do {
            $code = 'CP-' . strtoupper($country) . '-' . rand(100000, 999999);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Calculate priority score for withdrawal processing
     * Higher score = processed first
     */
    public function calculatePriorityScore(): int
    {
        $score = 0;

        // Rank-based priority (Diamond users processed first)
        if ($this->rank) {
            $score += match($this->rank->name) {
                'Diamond' => 100,
                'Gold' => 50,
                'Silver' => 25,
                default => 0,
            };
        }

        // Team size bonus (big recruiters get priority)
        $score += (int)($this->total_team_size / 10);

        // Account age bonus (older accounts slightly prioritized)
        if ($this->activation_date) {
            $daysActive = now()->diffInDays($this->activation_date);
            $score += (int)($daysActive / 7);
        }

        // Previous withdrawal success (reliable users prioritized)
        $successfulWithdrawals = $this->withdrawals()->where('status', 'COMPLETED')->count();
        $score += $successfulWithdrawals * 5;

        // Penalty for high withdrawal frequency
        $recentWithdrawals = $this->withdrawals()->where('created_at', '>=', now()->subDays(7))->count();
        if ($recentWithdrawals > 3) {
            $score -= 20;
        }

        return max(0, $score);
    }

    public function deviceFingerprints()
    {
        return $this->hasMany(DeviceFingerprint::class);
    }
}
