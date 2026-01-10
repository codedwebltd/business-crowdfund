<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - All money movements (earnings, commissions, withdrawals)
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User Reference
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Transaction Details (STRING not ENUM - admin can add new types!)
            $table->string('transaction_type')->index()->comment('TASK_EARNING, REFERRAL_BONUS, RANK_BONUS, WITHDRAWAL, MATURATION, ADJUSTMENT, PENALTY, etc.');
            $table->string('balance_type')->index()->comment('PENDING, WITHDRAWABLE, REFERRAL, BONUS, LOCKED, etc.');
            $table->string('status')->default('PENDING')->index()->comment('PENDING, COMPLETED, APPROVED, REJECTED, CANCELLED, etc.');
            $table->tinyInteger('priority')->default(1)->index()->comment('1-5, from user star rating (5=highest)');

            // Amount
            $table->decimal('amount', 15, 2)->index();
            $table->string('currency', 3)->default('NGN');
            $table->boolean('is_credit')->default(true)->index()->comment('true=credit, false=debit');

            // Description & Reference
            $table->text('description')->nullable()->comment('e.g., "Survey task #1234 completed"');
            $table->uuid('reference_id')->nullable()->index()->comment('Links to task_id, withdrawal_id, etc.');
            $table->string('reference_type')->nullable()->index()->comment('UserTask, Withdrawal, UserSubscription, etc.');

            // Processing
            $table->uuid('processed_by')->nullable()->comment('Admin who processed');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable()->index();

            // Metadata
            $table->json('metadata')->nullable()->comment('Extra data: commission_level, rank_multiplier, etc.');
            $table->string('transaction_hash')->nullable()->unique()->comment('Unique identifier for deduplication');

            $table->timestamps();

            // Composite Indexes for complex queries
            $table->index(['user_id', 'transaction_type']);
            $table->index(['user_id', 'status']);
            $table->index(['transaction_type', 'status']);
            $table->index(['balance_type', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
