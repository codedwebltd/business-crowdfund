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
        Schema::create('support_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('ticket_id');
            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');

            // Sender info
            $table->enum('sender_type', ['user', 'guest', 'admin', 'system'])->default('user');
            $table->uuid('sender_id')->nullable(); // user_id for user/admin messages
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');

            // Message content
            $table->text('message');
            $table->enum('message_type', ['text', 'file', 'image', 'system'])->default('text');

            // File attachments
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            // Read status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // Metadata
            $table->json('meta_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['ticket_id', 'created_at']);
            $table->index(['sender_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }
};
