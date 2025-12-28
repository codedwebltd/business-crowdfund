<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * AI Training Platform - Referral network tree (dynamic depth from global_settings)
     * Materialized Path + Nested Set hybrid for fast queries
     */
    public function up(): void
    {
        Schema::create('referral_trees', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // User in this node
            $table->uuid('user_id')->unique()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Direct referrer (parent)
            $table->uuid('parent_id')->nullable()->index();
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('cascade');

            // Level in tree (1 = direct referral, 2-40+ based on settings)
            $table->unsignedInteger('level')->default(1)->index()->comment('1=direct, 2-N based on global_settings.referral_levels_depth');

            // Root ancestor (top of this chain)
            $table->uuid('root_ancestor_id')->nullable()->index();
            $table->foreign('root_ancestor_id')->references('id')->on('users')->onDelete('cascade');

            // Materialized Path (for fast ancestry queries)
            $table->text('path')->index()->comment('e.g., user1.user2.user3 - full ancestry chain');

            // Nested Set Model (for fast subtree operations)
            $table->unsignedBigInteger('left_boundary')->index();
            $table->unsignedBigInteger('right_boundary')->index();

            $table->timestamps();

            // Composite Indexes for complex queries
            $table->index(['root_ancestor_id', 'level']);
            $table->index(['parent_id', 'level']);
            $table->index(['user_id', 'level']);
            $table->index(['left_boundary', 'right_boundary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_trees');
    }
};
