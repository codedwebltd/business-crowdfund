<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - Task library (survey, video, AI rating, etc.)
     */
    public function up(): void
    {
        Schema::create('task_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Task Details (STRING not ENUM - admin can add AI_RATING, TEXT_MODERATION, etc!)
            $table->string('category')->index()->comment('SURVEY, VIDEO, APP_SYNC, PRODUCT_REVIEW, AI_RATING, IMAGE_MODERATION, etc.');
            $table->string('title');
            $table->text('description')->nullable();

            // Task Content
            $table->json('questions')->nullable()->comment('Question structure for surveys/tasks');
            $table->json('validation_rules')->nullable()->comment('Min time, required fields, etc.');

            // Rewards & Timing
            $table->decimal('reward_amount', 15, 2)->index();
            $table->string('currency', 3)->default('NGN');
            $table->unsignedInteger('completion_time_seconds')->default(120)->comment('Expected duration');
            $table->unsignedInteger('min_completion_time')->default(30)->comment('Min time to prevent bots');
            $table->unsignedInteger('max_completion_time')->default(600)->comment('Max time before expiry');

            // Assignment Settings
            $table->unsignedInteger('priority')->default(0)->index()->comment('Higher = shown first');
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index()->comment('Special/high reward tasks');

            // Limited Slots (from map.md - FOMO feature)
            $table->unsignedInteger('max_completions')->nullable()->comment('e.g., Only 500 users can complete');
            $table->unsignedInteger('current_completions')->default(0);

            // Task Requirements
            $table->foreignId('min_rank_id')->nullable()->index()->comment('Minimum rank required');
            $table->foreign('min_rank_id')->references('id')->on('ranks')->onDelete('set null');

            // Metadata
            $table->string('video_url')->nullable()->comment('For VIDEO category tasks');
            $table->unsignedInteger('video_duration_seconds')->nullable();
            $table->text('instructions')->nullable();

            $table->timestamps();

            // Composite Indexes
            $table->index(['category', 'is_active']);
            $table->index(['is_active', 'priority']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_templates');
    }
};
