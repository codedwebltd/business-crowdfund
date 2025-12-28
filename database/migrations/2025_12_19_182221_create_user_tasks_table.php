<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - Tasks assigned to users
     */
    public function up(): void
    {
        Schema::create('user_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User & Task References
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('task_template_id')->index();
            $table->foreign('task_template_id')->references('id')->on('task_templates')->onDelete('cascade');

            // Status (STRING not ENUM - admin might add CANCELLED, FLAGGED, UNDER_REVIEW, etc.)
            $table->string('status')->default('PENDING')->index()->comment('PENDING, IN_PROGRESS, COMPLETED, EXPIRED, CANCELLED, FLAGGED, etc.');

            // Timing
            $table->timestamp('assigned_at')->index()->comment('When task was assigned');
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->timestamp('expires_at')->index()->comment('24 hours from assignment (dynamic)');

            // Submission Data
            $table->json('response_data')->nullable()->comment('User answers/submission');
            $table->unsignedInteger('completion_duration_seconds')->nullable()->comment('Actual time taken');

            // Reward & Crediting
            $table->decimal('reward_amount', 15, 2)->comment('Copied from template at assignment');
            $table->boolean('credited')->default(false)->index()->comment('Has reward been added to wallet?');
            $table->uuid('transaction_id')->nullable()->comment('Link to transaction record');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');

            // Validation & Quality
            $table->unsignedInteger('validation_score')->nullable()->comment('0-100 quality score');
            $table->text('validation_notes')->nullable()->comment('Why flagged/rejected');

            // Fraud Detection
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('device_fingerprint')->nullable();

            $table->timestamps();

            // Composite Indexes for queries
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'assigned_at']);
            $table->index(['status', 'expires_at']);
            $table->index(['task_template_id', 'status']);
            $table->index(['credited', 'status']);
            $table->index(['completed_at', 'credited']);

            // Unique constraint to prevent duplicate assignment
            $table->unique(['user_id', 'task_template_id', 'assigned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tasks');
    }
};
