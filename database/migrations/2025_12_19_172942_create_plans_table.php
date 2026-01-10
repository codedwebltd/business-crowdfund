<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - Subscription plans (Basic, Premium, VIP)
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Plan Details
            $table->string('name')->unique()->index();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->unique()->index()->comment('Display order: 1=Basic, 2=Premium, 3=VIP');

            // Pricing
            $table->decimal('price', 15, 2)->index();
            $table->string('currency', 3)->default('NGN');

            // Features (JSON for complete flexibility)
            $table->json('features')->comment('max_daily_tasks, task_reward_multiplier, priority_support, etc.');

            // Visual & Marketing
            $table->string('badge_color')->nullable()->comment('Hex color for plan badge');
            $table->string('icon')->nullable();
            $table->boolean('is_popular')->default(false)->index()->comment('Featured/recommended plan');

            // Status
            $table->boolean('is_active')->default(true)->index();

            // Rank Assignment
            $table->unsignedBigInteger('rank_id')->nullable()->comment('Rank to assign when user purchases this plan');
            $table->foreign('rank_id')->references('id')->on('ranks')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'order']);
            $table->index(['is_active', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
