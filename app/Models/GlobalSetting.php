<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        // Platform Core Settings
        'app_name',
        'app_url',
        'site_logo',
        'country_of_operation',
        'platform_currency',
        'app_description',

        // Landing Page Display Data
        'daily_earning_average',
        'time_required',
        'anonymity_level',
        'total_users',

        // Balance & Maturation
        'pending_balance_maturation_hours',

        // Referral System
        'referral_levels_depth',
        'commission_rates',

        // Task System
        'task_distribution_percentages',
        'daily_task_limits',
        'task_validation_rules',

        // Rank System
        'rank_criteria',
        'rank_commission_multipliers',
        'diamond_leadership_bonus',

        // Withdrawal Settings
        'withdrawal_limits_by_rank',
        'withdrawal_processing_times',

        // Fraud Detection
        'fraud_detection_rules',

        // Notification Channels
        'notification_channels',

        // Payment Gateways
        'payment_gateways',
        'bank_accounts',
        'crypto_wallets',

        // Liquidity Management
        'liquidity_settings',

        // Token System
        'token_settings',

        // KYC Settings
        'kyc_requirements',
        'enable_kyc_on_first_withdrawal',

        // System Controls
        'maintenance_mode',
        'new_registrations_enabled',
        'withdrawals_enabled',
        'referral_bonuses_enabled',

        // Notification Controls
        'email_notifications_enabled',
        'sms_notifications_enabled',

        // Member Tracking & Withdrawal Rate
        'total_members',
        'withdrawal_rate',

        // Testimonial & Welcome Settings
        'require_testimonial_first_withdrawal',
        'testimonial_required_for_withdrawal',
        'kyc_withdrawal_threshold',
        'minimum_withdrawal',
        'maximum_withdrawal',
        'withdrawals_per_day',

        // Mail Settings
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',

        // Pusher/WebSocket Settings
        'pusher_app_id',
        'pusher_app_key',
        'pusher_app_secret',
        'pusher_app_cluster',

        // Support Settings
        'support_email',
        'support_phone',
        'support_whatsapp',

        // AI Task Generation
        'ai_task_generation_enabled',
        'ai_configuration',
        'ai_generation_frequency_hours',
        'min_task_templates_threshold',

        // reCAPTCHA Settings
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'recaptcha_enabled',
        'recaptcha_trigger_on_fraud',
    ];

    protected $casts = [
        // JSON columns
        'commission_rates' => 'array',
        'task_distribution_percentages' => 'array',
        'daily_task_limits' => 'array',
        'task_validation_rules' => 'array',
        'rank_criteria' => 'array',
        'rank_commission_multipliers' => 'array',
        'diamond_leadership_bonus' => 'array',
        'withdrawal_limits_by_rank' => 'array',
        'withdrawal_processing_times' => 'array',
        'fraud_detection_rules' => 'array',
        'notification_channels' => 'array',
        'payment_gateways' => 'array',
        'bank_accounts' => 'array',
        'crypto_wallets' => 'array',
        'liquidity_settings' => 'array',
        'token_settings' => 'array',
        'kyc_requirements' => 'array',
        'ai_configuration' => 'array',

        // Boolean columns
        'ai_task_generation_enabled' => 'boolean',
        'maintenance_mode' => 'boolean',
        'new_registrations_enabled' => 'boolean',
        'withdrawals_enabled' => 'boolean',
        'referral_bonuses_enabled' => 'boolean',
        'email_notifications_enabled' => 'boolean',
        'sms_notifications_enabled' => 'boolean',
        'require_testimonial_first_withdrawal' => 'boolean',
        'testimonial_required_for_withdrawal' => 'boolean',
        'enable_kyc_on_first_withdrawal' => 'boolean',
        'recaptcha_enabled' => 'boolean',
        'recaptcha_trigger_on_fraud' => 'boolean',

        // Decimal columns
        'kyc_withdrawal_threshold' => 'decimal:2',
        'minimum_withdrawal' => 'decimal:2',
        'maximum_withdrawal' => 'decimal:2',
        'withdrawal_rate' => 'decimal:2',

        // Integer columns
        'pending_balance_maturation_hours' => 'integer',
        'referral_levels_depth' => 'integer',
        'mail_port' => 'integer',
        'total_members' => 'integer',
        'daily_earning_average' => 'integer',
        'withdrawals_per_day' => 'integer',
        'ai_generation_frequency_hours' => 'integer',
        'min_task_templates_threshold' => 'integer',
    ];

    /**
     * Get the global settings instance (singleton pattern)
     * Only one record should exist in this table
     */
    public static function get()
    {
        return self::firstOrCreate([]);
    }

    /**
     * Helper: Get commission rate for specific level and type
     */
    public function getCommissionRate($level, $type = 'activation')
    {
        $rates = $this->commission_rates[$type] ?? [];
        return $rates[$level] ?? 0;
    }

    /**
     * Helper: Get task distribution percentage for a category
     */
    public function getTaskDistribution($category)
    {
        return $this->task_distribution_percentages[$category] ?? 0;
    }

    /**
     * Helper: Get daily task limit for a plan
     */
    public function getDailyTaskLimit($plan)
    {
        return $this->daily_task_limits[$plan] ?? 0;
    }

    /**
     * Helper: Get withdrawal limits for a specific rank
     */
    public function getWithdrawalLimits($rank)
    {
        return $this->withdrawal_limits_by_rank[$rank] ?? [];
    }

    /**
     * Helper: Get rank criteria for a specific rank
     */
    public function getRankCriteria($rank)
    {
        return $this->rank_criteria[$rank] ?? [];
    }

    /**
     * Helper: Check if liquidity is healthy
     */
    public function isLiquidityHealthy($burnRate)
    {
        $healthy = $this->liquidity_settings['healthy_burn_rate'] ?? 0.7;
        return $burnRate < $healthy;
    }

    /**
     * Helper: Get liquidity status based on burn rate
     */
    public function getLiquidityStatus($burnRate)
    {
        $healthy = $this->liquidity_settings['healthy_burn_rate'] ?? 0.7;
        $caution = $this->liquidity_settings['caution_burn_rate'] ?? 0.9;
        $critical = $this->liquidity_settings['critical_burn_rate'] ?? 1.2;

        if ($burnRate < $healthy) {
            return 'healthy';
        } elseif ($burnRate < $caution) {
            return 'caution';
        } elseif ($burnRate < $critical) {
            return 'critical';
        } else {
            return 'collapse_imminent';
        }
    }
}
