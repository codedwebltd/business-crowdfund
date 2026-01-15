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
        Schema::create('task_content_pool', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['VIDEO', 'SURVEY', 'REVIEW', 'SYNC'])->index();
            $table->string('title')->index();
            $table->longText('content'); // JSON: questions, video_url, product data, etc.
            $table->string('source', 50)->default('api'); // youtube, trivia, groq, static
            $table->unsignedInteger('times_used')->default(0);
            $table->timestamp('last_used_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicates: same type + title combination
            $table->unique(['type', 'title']);

            // Composite indexes for common query patterns (FAST OPTIMIZATION)
            $table->index(['type', 'is_active', 'last_used_at'], 'idx_type_active_lastused'); // Get fresh content
            $table->index(['type', 'times_used', 'is_active'], 'idx_type_usage_active'); // Rotation by usage
            $table->index(['source', 'is_active'], 'idx_source_active'); // Filter by source
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_content_pool');
    }
};
