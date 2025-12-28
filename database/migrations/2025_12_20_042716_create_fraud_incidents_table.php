<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('incident_type')->index(); // CIRCULAR_REFERRAL, DEVICE_MATCH, IP_MATCH, PATTERN_ABUSE
            $table->string('severity')->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->json('incident_data')->nullable(); // Details: matched IPs, devices, etc.
            $table->json('affected_users')->nullable(); // User IDs in the fraud chain
            $table->string('action_taken'); // WARNED, TASK_BANNED_2DAYS, TREE_FROZEN
            $table->timestamp('banned_until')->nullable();
            $table->boolean('resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_incidents');
    }
};
