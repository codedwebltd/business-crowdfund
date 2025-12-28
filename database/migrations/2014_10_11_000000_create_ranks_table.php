<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - Dynamic rank system
     */
    public function up(): void
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();

            // Rank Details
            $table->string('name')->unique()->index();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->unique()->index()->comment('1=BRONZE, 2=SILVER, etc.');

            // Rank Criteria (JSON for flexibility)
            $table->json('criteria')->comment('min_direct_referrals, min_team_size, min_monthly_volume, min_active_directs, stability_months');

            // Benefits & Limits (JSON for flexibility)
            $table->json('benefits')->comment('withdrawal_min, withdrawal_max, daily_withdrawal_limit, withdrawals_per_day, commission_multiplier, processing_hours');

            // Visual & Display
            $table->string('badge_color')->nullable()->comment('Hex color code for badge');
            $table->string('icon')->nullable()->comment('Icon class or path');

            // Status
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranks');
    }
};
