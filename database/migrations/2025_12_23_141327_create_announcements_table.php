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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            // Content
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'success', 'danger'])->default('info')->index();

            // Display Control
            $table->unsignedInteger('priority')->default(0)->index()->comment('Higher = shown first');
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_dismissable')->default(true)->index()->comment('Can users dismiss this?');

            // Targeting
            $table->enum('target_audience', ['all', 'active', 'pending', 'unverified'])->default('all')->index();

            // Scheduling
            $table->timestamp('start_date')->nullable()->index();
            $table->timestamp('end_date')->nullable()->index();

            // Optional CTA
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();

            $table->timestamps();

            // Composite indexes
            $table->index(['is_active', 'priority', 'start_date']);
            $table->index(['target_audience', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
