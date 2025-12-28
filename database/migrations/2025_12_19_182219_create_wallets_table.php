<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - User wallet for balance tracking
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User Reference
            $table->uuid('user_id')->unique()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Balance Types (from map.md)
            $table->decimal('pending_balance', 15, 2)->default(0)->index()->comment('Task rewards maturing (48-72hrs)');
            $table->decimal('withdrawable_balance', 15, 2)->default(0)->index()->comment('Available for withdrawal');
            $table->decimal('total_earned', 15, 2)->default(0)->index()->comment('Lifetime total earnings');
            $table->decimal('total_withdrawn', 15, 2)->default(0)->index()->comment('Lifetime total withdrawals');

            // Additional Balance Types (for future flexibility)
            $table->decimal('referral_balance', 15, 2)->default(0)->comment('Referral commissions only');
            $table->decimal('bonus_balance', 15, 2)->default(0)->comment('Platform bonuses/promotions');
            $table->decimal('locked_balance', 15, 2)->default(0)->comment('Locked/frozen funds');

            // Task Earnings Tracking
            $table->decimal('task_earnings_today', 15, 2)->default(0)->comment('Reset daily');
            $table->decimal('task_earnings_this_month', 15, 2)->default(0)->comment('Reset monthly');
            $table->unsignedInteger('tasks_completed_today')->default(0)->comment('Reset daily');

            // Metadata
            $table->string('currency', 3)->default('NGN');
            $table->timestamp('last_transaction_at')->nullable()->index();

            $table->timestamps();

            // Composite Indexes for queries
            $table->index(['user_id', 'withdrawable_balance']);
            $table->index(['withdrawable_balance', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
