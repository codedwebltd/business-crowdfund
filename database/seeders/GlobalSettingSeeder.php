<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalSetting;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GlobalSetting::create([
            // Platform Core
            'app_name' => 'CrowdPower',
            'app_url' => 'https://business.qiviotalk.online',
            'site_logo' => null, // Set Backblaze B2 URL when uploaded
            'country_of_operation' => 'NG',
            'platform_currency' => '₦',
            'app_description' => 'Earn money by sharing your browsing data and completing simple tasks. Join thousands earning daily.',

            // Landing Page Display
            'daily_earning_average' => 850,
            'time_required' => '5min',
            'anonymity_level' => '100%',
            'total_users' => '10,000+',

            // Balance & Maturation
            'pending_balance_maturation_hours' => 72,

            // Referral System
            'referral_levels_depth' => 40,
            'commission_rates' => [
                'activation' => [
                    '1' => 20, '2' => 10, '3' => 5, '4' => 5, '5' => 3,
                    '6' => 3, '7' => 2, '8' => 2, '9' => 2, '10' => 1,
                    '11' => 1, '12' => 1, '13' => 1, '14' => 1, '15' => 1,
                    '16' => 0.5, '17' => 0.5, '18' => 0.5, '19' => 0.5, '20' => 0.5,
                ],
                'task_earnings' => [
                    '1' => 10, '2' => 5, '3' => 3, '4' => 2, '5' => 2,
                    '6' => 1, '7' => 1, '8' => 1, '9' => 0.5, '10' => 0.5,
                ],
            ],

            // Task System
            'task_distribution_percentages' => [
                'surveys' => 60,
                'videos' => 20,
                'syncs' => 15,
                'reviews' => 5,
            ],
            'daily_task_limits' => [
                'basic' => 8,
                'premium' => 15,
                'vip' => 25,
            ],
            'task_validation_rules' => [
                'survey_min_time' => 60,
                'survey_max_time' => 600,
                'video_min_watch_percentage' => 90,
                'sync_min_duration' => 30,
                'review_min_characters' => 20,
            ],

            // Rank System
            'rank_criteria' => [
                'bronze' => ['direct_referrals' => 0, 'team_size' => 0, 'monthly_volume' => 0],
                'silver' => ['direct_referrals' => 10, 'team_size' => 50, 'monthly_volume' => 0],
                'gold' => ['direct_referrals' => 30, 'team_size' => 200, 'monthly_volume' => 100000],
                'diamond' => ['direct_referrals' => 100, 'team_size' => 1000, 'monthly_volume' => 500000],
            ],
            'rank_commission_multipliers' => [
                'bronze' => 1.00,
                'silver' => 1.02,
                'gold' => 1.05,
                'diamond' => 1.10,
            ],
            'diamond_leadership_bonus' => [
                'base_bonus' => 50000,
                'multipliers' => [
                    '1000000' => 1.5,
                    '750000' => 1.25,
                ],
            ],

            // Withdrawal Settings (will be synced by settings:sync-plans command)
            'withdrawal_limits_by_plan' => [], // Auto-populated by sync command
            'withdrawal_processing_times_by_plan' => [], // Auto-populated by sync command
            'withdrawal_limits_by_rank' => [], // Auto-populated by sync command
            'withdrawal_processing_times_by_rank' => [], // Auto-populated by sync command

            // Fraud Detection
            'fraud_detection_rules' => [
                'max_tasks_per_hour' => 15,
                'min_task_completion_time' => 30,
                'suspension_intervals' => [
                    'first_offense' => 'warning',
                    'second_offense' => '48_hours',
                    'third_offense' => 'review',
                    'persistent' => 'permanent_ban',
                ],
                'device_fingerprinting_enabled' => true,
                'ip_monitoring_enabled' => true,
            ],

            // Notification Channels
            'notification_channels' => [
                'email' => ['enabled' => true, 'provider' => 'smtp'],
                'firebase' => ['enabled' => false],
                'whatsapp' => ['enabled' => false],
                'telegram' => ['enabled' => false],
            ],

            // Payment Gateways
            'payment_gateways' => [
                'bank_transfer' => ['enabled' => false], // Under maintenance
                'crypto_transfer' => ['enabled' => true], // Active
            ],

            // Liquidity Management
            'liquidity_settings' => [
                'healthy_burn_rate' => 0.7,
                'caution_burn_rate' => 0.9,
                'critical_burn_rate' => 1.2,
                'delay_tactics_enabled' => true,
                'processing_delays' => [
                    'healthy' => '24-48 hours',
                    'caution' => '3-5 business days',
                    'critical' => '7-14 days',
                ],
            ],

            // Token System
            'token_settings' => [
                'enabled' => true,
                'token_price' => 850, // 1 CROW = ₦850
                'fluctuation_enabled' => true,
            ],

            // KYC Settings
            'kyc_requirements' => [
                'basic_registration' => [
                    'phone_verification' => true,
                    'bank_account' => true,
                    'bvn' => false,
                ],
                'withdrawal_requirements' => [
                    'nin' => ['enabled' => true, 'threshold' => 50000],
                    'utility_bill' => ['enabled' => true, 'threshold' => 100000],
                    'selfie' => ['enabled' => false, 'for_ranks' => ['diamond']],
                ],
            ],

            // System Controls
            'maintenance_mode' => false,
            'new_registrations_enabled' => true,
            'withdrawals_enabled' => true,
            'referral_bonuses_enabled' => true,

            // Notification Controls
            'email_notifications_enabled' => true,
            'sms_notifications_enabled' => false, // Disabled by default (costs money)

            // Testimonial & Welcome Settings
            'require_testimonial_first_withdrawal' => true,
            'kyc_withdrawal_threshold' => 50000,
            'minimum_withdrawal' => 5000,
            'maximum_withdrawal' => 50000,
            'withdrawals_per_day' => 1,

            // Mail Settings
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp-relay.brevo.com',
            'mail_port' => 587,
            'mail_username' => '8eade3001@smtp-brevo.com',
            'mail_password' => 'jt0skG6nq3YOT4KZ',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'support@thesoftsystem.cloud',
            'mail_from_name' => 'CrowdPower',

            // Pusher Settings
            'pusher_app_id' => '1624131',
            'pusher_app_key' => '38b5c6c6e09853ed572a',
            'pusher_app_secret' => 'f3403a94711d724d6188',
            'pusher_app_cluster' => 'eu',

            // Support Settings
            'support_email' => 'support@thesoftsystem.cloud',
            'support_phone' => '+234',
            'support_whatsapp' => '+234',

            // Member Tracking (for PDFs and UI)
            'total_members' => 0, // Auto-incremented on user registration

            // Withdrawal Rate (dynamic, announced globally when changed)
            'withdrawal_rate' => 1.0, // 1.0 = normal, <1.0 = reduced rate, >1.0 = bonus rate
        ]);
    }
}
