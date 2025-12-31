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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('total_tasks_completed')->default(0)->after('last_task_completed_at');
            $table->unsignedInteger('tasks_completed_this_week')->default(0)->after('total_tasks_completed');
            $table->unsignedInteger('tasks_completed_this_month')->default(0)->after('tasks_completed_this_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_tasks_completed', 'tasks_completed_this_week', 'tasks_completed_this_month']);
        });
    }
};
