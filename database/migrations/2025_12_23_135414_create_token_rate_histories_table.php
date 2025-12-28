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
        Schema::create('token_rate_histories', function (Blueprint $table) {
            $table->id();

            // Rate Data
            $table->decimal('token_price', 15, 2)->index()->comment('CROW token price at this snapshot');
            $table->decimal('withdrawal_rate', 5, 4)->index()->comment('Withdrawal rate (e.g., 0.68 = 68%)');

            // Change Tracking
            $table->decimal('price_change', 15, 2)->nullable()->comment('Change from previous rate');
            $table->decimal('rate_change', 5, 4)->nullable()->comment('Change from previous withdrawal rate');
            $table->enum('trend', ['up', 'down', 'stable'])->default('stable')->index();

            // Admin Who Made Change
            $table->uuid('changed_by')->nullable()->index();
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');

            // Metadata
            $table->text('change_reason')->nullable()->comment('Admin can provide reason for change');

            $table->timestamps();

            // Indexes for trend queries
            $table->index(['created_at', 'trend']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_rate_histories');
    }
};
