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
        Schema::create('burn_rate_histories', function (Blueprint $table) {
            $table->id();

            // Date tracking (unique - one record per day)
            $table->date('report_date')->unique()->index()->comment('Date for this burn rate calculation');

            // Financial metrics (from map.MD line 891-897)
            $table->decimal('total_deposits', 15, 2)->default(0)->comment('Activation payments received this day');
            $table->decimal('total_withdrawals', 15, 2)->default(0)->comment('Completed withdrawals this day');
            $table->decimal('pending_withdrawals', 15, 2)->default(0)->comment('Pending withdrawal requests');
            $table->decimal('platform_balance', 15, 2)->default(0)->comment('Total platform balance snapshot');

            // Burn rate calculation
            $table->decimal('burn_rate', 5, 4)->default(0)->index()->comment('Withdrawals / Deposits ratio');

            // Liquidity status (from map.MD line 900-903)
            $table->string('liquidity_status')->index()->comment('healthy, caution, critical, collapse_imminent');

            // Consecutive critical days tracking (map.MD line 903 - alert if >1.2 for 3 days)
            $table->unsignedInteger('consecutive_critical_days')->default(0)->comment('Days with burn_rate > 1.2');

            // User activity metrics
            $table->unsignedInteger('active_users_count')->default(0)->comment('Users who completed tasks today');
            $table->unsignedInteger('new_activations_count')->default(0)->comment('New user activations today');
            $table->unsignedInteger('withdrawal_requests_count')->default(0)->comment('Withdrawal requests today');

            // Threshold snapshot (in case admin changes them later)
            $table->json('thresholds_snapshot')->nullable()->comment('liquidity_settings at time of calculation');

            // Metadata
            $table->json('metadata')->nullable()->comment('Additional stats: avg task earnings, top earners, etc.');

            // Alert tracking
            $table->boolean('admin_alerted')->default(false)->comment('Whether admin was notified');
            $table->timestamp('alerted_at')->nullable();

            $table->timestamps();

            // Composite indexes
            $table->index(['report_date', 'liquidity_status']);
            $table->index(['burn_rate', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('burn_rate_histories');
    }
};
