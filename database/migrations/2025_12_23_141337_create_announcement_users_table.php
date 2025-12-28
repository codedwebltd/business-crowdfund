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
        Schema::create('announcement_users', function (Blueprint $table) {
            $table->id();

            // Pivot relationship
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Tracking
            $table->timestamp('dismissed_at')->nullable();

            $table->timestamps();

            // Unique constraint - user can only dismiss once per announcement
            $table->unique(['announcement_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_users');
    }
};
