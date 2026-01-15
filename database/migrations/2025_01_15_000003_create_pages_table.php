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
        Schema::create('pages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Basic info
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable(); // HTML from WYSIWYG

            // Type & Category
            $table->enum('type', ['faq', 'blog', 'terms', 'privacy', 'about', 'help', 'support', 'custom'])->default('custom');
            $table->string('category')->nullable(); // For grouping (e.g., FAQs: account, tasks, withdrawals)

            // FAQ specific - JSON array of {question, answer} objects
            $table->json('faq_items')->nullable();

            // Blog specific
            $table->string('featured_image')->nullable();
            $table->text('excerpt')->nullable();
            $table->uuid('author_id')->nullable();
            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable();

            // Display settings
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('show_in_menu')->default(false);
            $table->string('menu_location')->nullable(); // header, footer, sidebar
            $table->integer('sort_order')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();

            // Stats
            $table->unsignedBigInteger('views_count')->default(0);

            // Extra data
            $table->json('meta_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type', 'is_published', 'sort_order']);
            $table->index(['category', 'is_published']);
            $table->index(['type', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
