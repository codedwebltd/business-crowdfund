<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - User plan subscriptions/activations
     */
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User & Plan
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('plan_id')->index();
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');

            // Payment Details
            $table->decimal('amount_paid', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('payment_method')->index()->comment('Dynamic: BANK_TRANSFER, OPAY, USDT, BTC, MONERO, etc.');
            $table->string('payment_reference')->nullable()->unique()->index();
            $table->string('transaction_id')->nullable()->unique();

            // Status & Dates (Lifetime activation, no expiry)
            $table->enum('status', ['PENDING', 'VERIFIED', 'ACTIVE', 'REJECTED', 'CANCELLED'])->default('PENDING')->index();
            $table->timestamp('payment_verified_at')->nullable()->index();
            $table->timestamp('activated_at')->nullable()->index();

            // Admin Actions
            $table->uuid('verified_by')->nullable()->comment('Admin who verified payment');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();

            // Metadata
            $table->json('payment_proof')->nullable()->comment('Screenshot path, receipt details');
            $table->boolean('is_upgrade')->default(false)->index()->comment('Upgraded from lower plan');
            $table->uuid('upgraded_from_subscription_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Composite Indexes
            $table->index(['user_id', 'status']);
            $table->index(['status', 'activated_at']);
            $table->index(['payment_method', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
