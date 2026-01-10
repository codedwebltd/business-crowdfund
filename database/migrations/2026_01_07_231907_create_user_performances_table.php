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
        Schema::create('user_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->integer('tasks_completed_this_week')->default(0);
            $table->integer('referrals_this_week')->default(0);
            $table->integer('total_referrals')->default(0);
            $table->integer('direct_referrals')->default(0);
            $table->integer('team_size')->default(0);
            $table->integer('referral_depth')->default(0);
            $table->tinyInteger('star_rating')->default(1)->comment('1-5 stars');
            $table->tinyInteger('priority_level')->default(1)->comment('1-5, where 5 is highest priority');
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('star_rating');
            $table->index('priority_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_performances');
    }
};
