<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();

            // ==================== PLATFORM CORE SETTINGS ====================
            $table->string('app_name')->default('CrowdPower');
            $table->string('app_url')->nullable();
            $table->string('site_logo')->nullable(); // Backblaze B2 URL
            $table->string('country_of_operation')->default('NG'); // For dynamic country handling
            $table->string('platform_currency')->default('NGN'); // NGN, USD, etc.
            $table->text('app_description')->nullable();

            // Landing Page Display Data
            $table->integer('daily_earning_average')->default(850);
            $table->string('time_required')->default('5min');
            $table->string('anonymity_level')->default('100%');
            $table->string('total_users')->default('10,000+');

            // ==================== BALANCE & MATURATION ====================
            $table->integer('pending_balance_maturation_hours')->default(72); // From blueprint

            // ==================== REFERRAL SYSTEM ====================
            $table->integer('referral_levels_depth')->default(40); // Blueprint: 40-level deep
            // Commission rates for all 40 levels (activation fee % and task earnings %)
            $table->json('commission_rates')->nullable();
            /**
             * Structure: {
             *   "activation": {"1": 20, "2": 10, "3": 5, ...},
             *   "task_earnings": {"1": 10, "2": 5, "3": 3, ...}
             * }
             */

            // ==================== TASK SYSTEM ====================
            $table->json('task_distribution_percentages')->nullable();
            /**
             * Structure: {
             *   "surveys": 60,
             *   "videos": 20,
             *   "syncs": 15,
             *   "reviews": 5
             * }
             */

            $table->json('daily_task_limits')->nullable();
            /**
             * Structure: {
             *   "basic": 8,
             *   "premium": 15,
             *   "vip": 25
             * }
             */

            $table->json('task_validation_rules')->nullable();
            /**
             * Structure: {
             *   "survey_min_time": 60,
             *   "survey_max_time": 600,
             *   "video_min_watch_percentage": 90,
             *   "sync_min_duration": 30,
             *   "review_min_characters": 20
             * }
             */

            // ==================== RANK SYSTEM ====================
            $table->json('rank_criteria')->nullable();
            /**
             * Structure: {
             *   "bronze": {"direct_referrals": 0, "team_size": 0, "monthly_volume": 0},
             *   "silver": {"direct_referrals": 10, "team_size": 50, "monthly_volume": 0},
             *   "gold": {"direct_referrals": 30, "team_size": 200, "monthly_volume": 100000},
             *   "diamond": {"direct_referrals": 100, "team_size": 1000, "monthly_volume": 500000}
             * }
             */

            $table->json('rank_commission_multipliers')->nullable();
            /**
             * Structure: {
             *   "bronze": 1.00,
             *   "silver": 1.02,
             *   "gold": 1.05,
             *   "diamond": 1.10
             * }
             */

            $table->json('diamond_leadership_bonus')->nullable();
            /**
             * Structure: {
             *   "base_bonus": 50000,
             *   "multipliers": {
             *     "1000000": 1.5,
             *     "750000": 1.25
             *   }
             * }
             */

            // ==================== WITHDRAWAL SETTINGS ====================
            $table->json('withdrawal_limits_by_rank')->nullable();
            /**
             * Structure: {
             *   "bronze": {"min": 5000, "max": 50000, "frequency": "weekly"},
             *   "silver": {"min": 3000, "max": 50000, "frequency": "daily"},
             *   "gold": {"min": 2000, "max": 100000, "frequency": "2_per_day"},
             *   "diamond": {"min": 1000, "max": 500000, "frequency": "unlimited"}
             * }
             */

            $table->json('withdrawal_processing_times')->nullable();
            /**
             * Structure: {
             *   "bronze": "48-72 hours",
             *   "silver": "24-48 hours",
             *   "gold": "24 hours",
             *   "diamond": "instant"
             * }
             */

            // ==================== FRAUD DETECTION ====================
            $table->json('fraud_detection_rules')->nullable();
            /**
             * Structure: {
             *   "max_tasks_per_hour": 15,
             *   "min_task_completion_time": 30,
             *   "suspension_intervals": {
             *     "first_offense": "warning",
             *     "second_offense": "48_hours",
             *     "third_offense": "review",
             *     "persistent": "permanent_ban"
             *   },
             *   "device_fingerprinting_enabled": true,
             *   "ip_monitoring_enabled": true
             * }
             */

            // ==================== NOTIFICATION CHANNELS ====================
            $table->json('notification_channels')->nullable();
            /**
             * Structure: {
             *   "email": {"enabled": true, "provider": "smtp", "credentials": {}},
             *   "firebase": {"enabled": true, "credentials": {}},
             *   "whatsapp": {"enabled": false, "api_key": "", "group_id": ""},
             *   "telegram": {"enabled": false, "bot_token": "", "chat_id": ""}
             * }
             */

            // ==================== AI TASK GENERATION ====================
            $table->boolean('ai_task_generation_enabled')->default(true);
            $table->json('ai_configuration')->nullable();
            /**
             * Structure: {
             *   "groq_api_key": "gsk_xxxx",
             *   "groq_model": "llama-3.1-8b-instant",
             *   "groq_endpoint": "https://api.groq.com/openai/v1/chat/completions",
             *   "temperature": 0.7,
             *   "max_tokens": {
             *     "survey": 2000,
             *     "video_questions": 800,
             *     "product_names": 300
             *   },
             *   "tasks_to_generate": {
             *     "surveys": 20,
             *     "videos": 10,
             *     "syncs": 5,
             *     "reviews": 10
             *   }
             * }
             */
            $table->integer('ai_generation_frequency_hours')->default(168); // Weekly
            $table->integer('min_task_templates_threshold')->default(50);

            // ==================== PAYMENT GATEWAYS ====================
            $table->json('payment_gateways')->nullable();
            /**
             * Structure: {
             *   "bank_transfer": {"enabled": true},
             *   "crypto_transfer": {"enabled": true}
             * }
             */

            // Payment Account Details (Admin managed)
            $table->json('bank_accounts')->nullable()->comment('Array of bank accounts for manual transfers');
            /**
             * Structure: [
             *   {"bank_name": "GTBank", "account_number": "0123456789", "account_name": "CrowdPower Ltd", "special_note": "Reference your phone number"},
             *   {"bank_name": "Opay", "account_number": "9876543210", "account_name": "CrowdPower", "special_note": null}
             * ]
             */

            $table->json('crypto_wallets')->nullable()->comment('Array of crypto wallets for payments');
            /**
             * Structure: [
             *   {"coin_name": "USDT", "network": "TRC20", "address": "TXxxx...", "special_note": "Minimum: $10"},
             *   {"coin_name": "BTC", "network": "Bitcoin", "address": "bc1xxx...", "special_note": null}
             * ]
             */

            // ==================== LIQUIDITY MANAGEMENT ====================
            $table->json('liquidity_settings')->nullable();
            /**
             * Structure: {
             *   "healthy_burn_rate": 0.7,
             *   "caution_burn_rate": 0.9,
             *   "critical_burn_rate": 1.2,
             *   "delay_tactics_enabled": true,
             *   "processing_delays": {
             *     "healthy": "24-48 hours",
             *     "caution": "3-5 business days",
             *     "critical": "7-14 days"
             *   }
             * }
             */

            // ==================== TOKEN SYSTEM ====================
            $table->json('token_settings')->nullable();
            /**
             * Structure: {
             *   "enabled": true,
             *   "token_price": 0.01,
             *   "fluctuation_enabled": true
             * }
             */

            // ==================== KYC SETTINGS ====================
            $table->json('kyc_requirements')->nullable();
            /**
             * Structure: {
             *   "basic_registration": {
             *     "phone_verification": true,
             *     "bank_account": true,
             *     "bvn": false
             *   },
             *   "withdrawal_requirements": {
             *     "nin": {"enabled": true, "threshold": 50000},
             *     "utility_bill": {"enabled": true, "threshold": 100000},
             *     "selfie": {"enabled": false, "for_ranks": ["diamond"]}
             *   }
             * }
             */

            // ==================== SYSTEM CONTROLS ====================
            $table->boolean('maintenance_mode')->default(false);
            $table->boolean('new_registrations_enabled')->default(true);
            $table->boolean('withdrawals_enabled')->default(true);
            $table->boolean('referral_bonuses_enabled')->default(true);

            // Notification Controls
            $table->boolean('email_notifications_enabled')->default(true);
            $table->boolean('sms_notifications_enabled')->default(false); // Disabled by default (costs money)

            // Member Tracking (for PDFs and UI display)
            $table->unsignedBigInteger('total_members')->default(0)->index(); // Auto-incremented on user registration

            // Withdrawal Rate (dynamic, announced globally when changed)
            $table->decimal('withdrawal_rate', 5, 2)->default(1.00); // 1.0 = normal, <1.0 = reduced rate, >1.0 = bonus rate

            // Testimonial & Welcome Settings
            $table->boolean('require_testimonial_first_withdrawal')->default(true);
            $table->decimal('kyc_withdrawal_threshold', 15, 2)->default(50000); // Amount above which KYC is required
            $table->decimal('minimum_withdrawal', 15, 2)->default(5000);
            $table->decimal('maximum_withdrawal', 15, 2)->default(50000);
            $table->integer('withdrawals_per_day')->default(1);

            // ==================== MAIL SETTINGS ====================
            $table->string('mail_mailer')->default('smtp');
            $table->string('mail_host')->nullable();
            $table->integer('mail_port')->default(587);
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->default('tls');
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();

            // ==================== PUSHER/WEBSOCKET SETTINGS ====================
            $table->string('pusher_app_id')->nullable();
            $table->string('pusher_app_key')->nullable();
            $table->string('pusher_app_secret')->nullable();
            $table->string('pusher_app_cluster')->default('mt1');

            // ==================== SUPPORT SETTINGS ====================
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('support_whatsapp')->nullable();

            // ==================== RECAPTCHA SETTINGS ====================
            $table->string('recaptcha_site_key')->nullable();
            $table->string('recaptcha_secret_key')->nullable();
            $table->boolean('recaptcha_enabled')->default(false);
            $table->boolean('recaptcha_trigger_on_fraud')->default(true); // Show CAPTCHA when fraud detected

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
