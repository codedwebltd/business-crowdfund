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
        Schema::create('commission_ledgers', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id'); // User earning the commission
            $table->uuid('source_user_id'); // User who completed the task
            $table->uuid('source_task_id'); // Task that generated commission
            $table->decimal('amount', 10, 2); // Commission amount
            $table->unsignedTinyInteger('level'); // Referral level (1-40)
            $table->decimal('commission_rate', 5, 2); // Percentage used (e.g., 5.00 for 5%)
            $table->enum('status', ['PENDING', 'PROCESSED', 'FAILED'])->default('PENDING');
            $table->string('batch_id')->nullable(); // Laravel queue batch ID
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('source_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('source_task_id')->references('id')->on('user_tasks')->onDelete('cascade');

            // Indexes for efficient queries
            $table->index(['user_id', 'status']);
            $table->index(['source_user_id']);
            $table->index(['batch_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_ledgers');
    }
};
