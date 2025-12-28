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
        Schema::create('device_fingerprints', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->index();
            $table->string('fingerprint_hash')->unique()->index(); // Unique device identifier
            $table->json('fingerprint_data')->nullable(); // Full fingerprint details
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable()->index();
            $table->integer('usage_count')->default(1); // How many times this device used
            $table->timestamp('first_seen_at');
            $table->timestamp('last_seen_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_fingerprints');
    }
};
