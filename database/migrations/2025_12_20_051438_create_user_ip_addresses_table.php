<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * IP Pool - Track all unique IPs used during registration
     */
    public function up(): void
    {
        Schema::create('user_ip_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('ip_address', 45)->index(); // IPv4 or IPv6
            $table->string('ip_type')->default('IPv4'); // IPv4, IPv6

            // VPN/Proxy Detection Flags
            $table->boolean('is_vpn')->default(false)->index();
            $table->boolean('is_proxy')->default(false)->index();
            $table->boolean('is_tor')->default(false)->index();
            $table->boolean('is_datacenter')->default(false)->index();
            $table->string('threat_level')->nullable(); // LOW, MEDIUM, HIGH, CRITICAL

            // Geolocation from IP
            $table->string('country_code', 3)->nullable()->index();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('isp')->nullable();
            $table->string('organization')->nullable();

            // Detection metadata
            $table->json('detection_data')->nullable()->comment('Raw data from VPN/Proxy detection service');

            // Usage tracking
            $table->enum('usage_type', ['REGISTRATION', 'LOGIN', 'TASK_COMPLETION', 'WITHDRAWAL'])->default('REGISTRATION')->index();
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->useCurrent();
            $table->unsignedInteger('usage_count')->default(1);

            $table->timestamps();

            // Composite indexes for fraud detection queries
            $table->unique(['user_id', 'ip_address', 'usage_type'], 'user_ip_usage_unique');
            $table->index(['ip_address', 'usage_type']);
            $table->index(['is_vpn', 'is_proxy', 'is_tor']);
            $table->index(['country_code', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_ip_addresses');
    }
};
