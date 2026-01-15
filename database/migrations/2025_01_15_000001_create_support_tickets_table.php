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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User info (nullable for guests)
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Guest info (when not authenticated)
            $table->string('guest_session_id')->nullable()->index();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            // Ticket details
            $table->string('subject')->nullable();
            $table->text('first_message');
            $table->string('ticket_number')->unique(); // e.g., TKT-2025-00001

            // Status & Priority
            $table->enum('status', ['open', 'in_progress', 'awaiting_reply', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('category')->nullable(); // account, tasks, withdrawals, referrals, other

            // Tracking
            $table->boolean('is_read_by_admin')->default(false);
            $table->boolean('is_read_by_user')->default(true);
            $table->boolean('has_new_admin_reply')->default(false);
            $table->boolean('has_new_user_reply')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('last_admin_reply_at')->nullable();
            $table->timestamp('last_user_reply_at')->nullable();

            // Resolution
            $table->uuid('resolved_by')->nullable();
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_note')->nullable();

            // Rating
            $table->tinyInteger('rating')->nullable(); // 1-5 stars
            $table->text('rating_comment')->nullable();

            // Visitor metadata
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('page_url')->nullable(); // Where they initiated the chat
            $table->json('meta_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
