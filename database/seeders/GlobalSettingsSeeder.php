<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalSetting;

class GlobalSettingsSeeder extends Seeder
{
    public function run()
    {
        GlobalSetting::create([
            // App Info
            'app_name' => 'QivioTalk',
            'app_url' => 'https://business.qiviotalk.online',
            'site_logo' => null,
            'app_description' => 'Earn money daily by completing simple tasks',
            'country_of_operation' => 'Nigeria',
            'platform_currency' => 'NGN',

            // Metrics
            'daily_earning_average' => 5000,
            'time_required' => '30-60 minutes',
            'anonymity_level' => 'High',
            'total_users' => 0,
            'total_members' => 0,

            // Maturation & Referrals
            'pending_balance_maturation_hours' => 72,
            'referral_levels_depth' => 10,
            'commission_rates' => [
                'level_1' => 10,
                'level_2' => 5,
                'level_3' => 3,
                'level_4' => 2,
                'level_5' => 1,
                'level_6' => 0.5,
                'level_7' => 0.5,
                'level_8' => 0.5,
                'level_9' => 0.5,
                'level_10' => 0.5,
            ],

            // Tasks
            'task_distribution_percentages' => [
                'VIDEO' => 40,
                'SURVEY' => 30,
                'APP_SYNC' => 20,
                'PRODUCT_REVIEW' => 10,
            ],
            'daily_task_limits' => [
                'basic' => 8,
                'bronze' => 15,
                'silver' => 25,
                'gold' => 40,
                'platinum' => 60,
            ],
            'task_validation_rules' => [
                'survey_min_time' => 60,
                'survey_max_time' => 600,
                'video_min_watch_percentage' => 90,
                'sync_min_duration' => 40,
                'review_min_characters' => 15,
            ],

            // Ranks
            'rank_criteria' => [
                'bronze' => ['referrals' => 5, 'tasks' => 50],
                'silver' => ['referrals' => 15, 'tasks' => 200],
                'gold' => ['referrals' => 50, 'tasks' => 500],
                'platinum' => ['referrals' => 150, 'tasks' => 1500],
                'diamond' => ['referrals' => 500, 'tasks' => 5000],
            ],
            'rank_commission_multipliers' => [
                'basic' => 1.0,
                'bronze' => 1.1,
                'silver' => 1.2,
                'gold' => 1.3,
                'platinum' => 1.5,
                'diamond' => 2.0,
            ],
            'diamond_leadership_bonus' => 5000,

            // Withdrawals
            'withdrawal_limits_by_rank' => [
                'basic' => ['min' => 5000, 'max' => 50000],
                'bronze' => ['min' => 3000, 'max' => 100000],
                'silver' => ['min' => 2000, 'max' => 200000],
                'gold' => ['min' => 1000, 'max' => 500000],
                'platinum' => ['min' => 500, 'max' => 1000000],
                'diamond' => ['min' => 100, 'max' => 5000000],
            ],
            'withdrawal_processing_times' => [
                'bank_transfer' => '1-3 business days',
                'crypto' => '10-30 minutes',
            ],
            'withdrawal_rate' => 0.68,
            'minimum_withdrawal' => 5000,
            'maximum_withdrawal' => 50000,
            'withdrawals_per_day' => 3,
            'require_testimonial_first_withdrawal' => true,
            'kyc_withdrawal_threshold' => 50000,

            // Fraud Detection
            'fraud_detection_rules' => [
                'max_tasks_per_hour' => 15,
                'bot_speed_threshold' => 30,
                'velocity_limit' => 10,
                'device_fingerprint_enabled' => true,
            ],

            // Notifications
            'notification_channels' => [
                'database' => true,
                'email' => true,
                'sms' => false,
                'firebase' => false,
                'whatsapp' => false,
                'telegram' => false,
            ],

            // AI Task Generation
            'ai_task_generation_enabled' => false,
            'ai_configuration' => [
                'provider' => 'openai',
                'model' => 'gpt-4',
                'api_key' => null,
            ],
            'ai_generation_frequency_hours' => 168,
            'min_task_templates_threshold' => 50,

            // Payment Gateways
            'payment_gateways' => [
                'bank_transfer' => [
                    'enabled' => true,
                ],
                'crypto_transfer' => [
                    'enabled' => true,
                ],
            ],

            // Bank Accounts (Admin manages these)
            'bank_accounts' => [
                [
                    'bank_name' => 'GTBank',
                    'account_number' => '0123456789',
                    'account_name' => 'QivioTalk Limited',
                    'special_note' => 'Use your phone number as reference',
                ],
            ],

            // Crypto Wallets (Admin manages these)
            'crypto_wallets' => [
                [
                    'coin_name' => 'USDT',
                    'network' => 'TRC20',
                    'address' => 'TYourWalletAddressHere',
                    'special_note' => 'Minimum deposit: $10 USDT',
                ],
            ],

            // Liquidity (matches Token.vue lines 76-98)
            'liquidity_settings' => [
                'healthy_burn_rate' => 0.7,
                'caution_burn_rate' => 0.9,
                'critical_burn_rate' => 1.2,
            ],

            // Token System
            'token_settings' => [
                'token_price' => 850,
                'fluctuation_enabled' => false,
                'fluctuation_percentage' => 5,
                'fluctuation_interval_hours' => 24,
            ],

            // KYC
            'kyc_requirements' => [
                'bvn_required' => false,
                'nin_required' => false,
                'document_upload_required' => false,
            ],

            // System Toggles
            'maintenance_mode' => false,
            'new_registrations_enabled' => true,
            'withdrawals_enabled' => true,
            'referral_bonuses_enabled' => true,
            'email_notifications_enabled' => true,
            'sms_notifications_enabled' => false,

            // Mail Configuration
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.mailtrap.io',
            'mail_port' => 587,
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@qiviotalk.online',
            'mail_from_name' => 'QivioTalk',

            // Pusher
            'pusher_app_id' => null,
            'pusher_app_key' => null,
            'pusher_app_secret' => null,
            'pusher_app_cluster' => 'mt1',

            // Support
            'support_email' => 'support@qiviotalk.online',
            'support_phone' => '+234',
            'support_whatsapp' => '+234',

            // Recaptcha
            'recaptcha_site_key' => null,
            'recaptcha_secret_key' => null,
            'recaptcha_enabled' => false,
            'recaptcha_trigger_on_fraud' => true,
        ]);
    }
}
