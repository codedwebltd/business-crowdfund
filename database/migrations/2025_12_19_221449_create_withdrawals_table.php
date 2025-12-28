<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Withdrawal system - All thresholds dynamic from global_settings
     */
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User requesting withdrawal
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Withdrawal amount
            $table->decimal('amount_requested', 15, 2);

            // Metadata (token price, rates, etc.)
            $table->json('meta_data')->nullable()->comment('original_amount, withdrawal_rate, token_price, etc.');

            // Payment method (BANK or CRYPTO)
            $table->string('payment_method')->default('BANK')->index()->comment('BANK, USDT, BTC, MONERO, etc.');

            // Bank details (for BANK withdrawals)
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();

            // Crypto wallet details (for CRYPTO withdrawals - JSON)
            $table->json('wallet_details')->nullable()->comment('coin_name, wallet_address, network');

            // Status tracking
            $table->string('status')->default('PENDING')->index()->comment('PENDING, PROCESSING, APPROVED, COMPLETED, REJECTED');

            // Timestamps
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // Admin management
            $table->uuid('approved_by_id')->nullable()->index()->comment('Admin who approved');
            $table->foreign('approved_by_id')->references('id')->on('users')->onDelete('set null');

            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Transaction reference (from payment gateway)
            $table->string('transaction_reference')->nullable()->unique();

            // Priority scoring (higher = processed first)
            $table->unsignedInteger('priority_score')->default(0)->index()->comment('Calculated: rank + team size + history');

            // Testimonial enforcement (map.md requirement)
            $table->boolean('is_first_withdrawal')->default(false)->comment('Requires testimonial if true');
            $table->boolean('testimonial_submitted')->default(false);

            // Track processing time (for analytics)
            $table->integer('processing_hours')->nullable()->comment('Hours from request to completion');

            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for admin dashboard queries
            $table->index(['status', 'priority_score']);
            $table->index(['user_id', 'status']);
            $table->index(['requested_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
