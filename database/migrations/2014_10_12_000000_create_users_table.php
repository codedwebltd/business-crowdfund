<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - User accounts table
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->uuid('id')->primary();

            // Authentication & Contact (all nullable for 3-step registration)
            $table->string('phone_number')->nullable()->unique()->index();
            $table->string('full_name')->nullable()->index();
            $table->string('email')->nullable()->unique();
            $table->date('date_of_birth')->nullable()->comment('Required for 18+ age verification');
            $table->string('password')->nullable();
            $table->string('password_confirmation')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->rememberToken();

            // SMS OTP Verification
            $table->string('otp_code', 6)->nullable()->comment('SMS verification code');
            $table->timestamp('otp_expires_at')->nullable();

            // Google 2FA Authentication
            $table->string('google2fa_secret')->nullable();
            $table->boolean('google2fa_enabled')->default(false)->index();
            $table->json('backup_codes')->nullable()->comment('Encrypted backup codes for 2FA recovery');

            // Role & Permissions (0 = user, 1 = admin)
            $table->tinyInteger('role')->default(0)->index();

            // User Type (USER = normal user, AGENT = platform agent)
            $table->enum('user_type', ['USER', 'AGENT'])->default('USER')->index()->comment('USER = normal user, AGENT = platform agent');

            // Bank Details for Withdrawals
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();

            // Crypto Wallet Details (JSON for flexibility)
            $table->json('wallet_details')->nullable()->comment('wallet_address, coin_name, coin_network');

            // Referral System
            $table->string('referral_code', 20)->unique()->index();
            $table->uuid('referred_by_id')->nullable()->index();
            $table->foreign('referred_by_id')->references('id')->on('users')->onDelete('set null');

            // Subscription/Plan
            $table->uuid('plan_id')->nullable()->index();
            $table->decimal('activation_amount', 15, 2)->nullable();
            $table->timestamp('activation_date')->nullable()->index();

            // Account Status
            $table->enum('status', ['UNVERIFIED', 'PENDING', 'ACTIVE', 'SUSPENDED', 'BANNED'])->default('UNVERIFIED')->index();

            // Rank System (dynamic from ranks table)
            $table->foreignId('rank_id')->nullable()->index()->constrained('ranks')->onDelete('set null');

            // Team Metrics (cached for performance)
            $table->unsignedInteger('direct_referrals_count')->default(0)->index();
            $table->unsignedInteger('total_team_size')->default(0)->index()->comment('All downlines across all levels');

            // Activity Tracking
            $table->timestamp('last_task_completed_at')->nullable()->index();
            $table->timestamp('last_login_at')->nullable()->index();

            // Fraud Detection
            $table->timestamp('task_ban_until')->nullable()->comment('User banned from completing tasks until this time');
            $table->json('device_fingerprint')->nullable()->comment('Device info for fraud detection');

            // User Preferences
            $table->json('notification_preferences')->nullable()->comment('User notification settings');

            // Location (auto-detected, no default)
            $table->string('country', 3)->nullable()->index()->comment('ISO 3166-1 alpha-3 country code');

            // KYC Fields
            $table->string('nin')->nullable()->comment('National Identification Number');
            $table->string('bvn')->nullable()->comment('Bank Verification Number');
            $table->string('utility_bill_path')->nullable();
            $table->string('selfie_path')->nullable();
            $table->timestamp('kyc_verified_at')->nullable()->index();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Composite Indexes for complex queries
            $table->index(['status', 'rank_id']);
            $table->index(['status', 'last_task_completed_at']);
            $table->index(['rank_id', 'total_team_size']);
            $table->index(['country', 'status']);
            $table->index(['referred_by_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
