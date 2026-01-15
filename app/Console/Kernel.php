<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Purge unpaid accounts after 48 hours (runs every hour)
        $schedule->command('purge:unpaid-accounts')->hourly();

        // Cleanup soft-deleted accounts after 30 days (runs daily at 2 AM)
        $schedule->command('cleanup:soft-deletes')->dailyAt('02:00');

        // ===== DAILY TASK CYCLE (AGE-BASED ASSIGNMENT) =====
        // UserTasks expire at 00:01 AM (24 hours after assignment)
        // 1. Generate fresh tasks + cleanup old ones at 00:05 AM (after tasks expire)
        $schedule->job(new \App\Jobs\CleanupAndRegenerateTasksJob)->dailyAt('00:05');

        // 2. Assign fresh tasks at 00:10 AM (after generation completes)
        $schedule->command('tasks:assign-daily')->dailyAt('00:10');

        // Reset weekly task counters (every Monday at midnight)
        $schedule->call(function () {
            \App\Models\User::query()->update(['tasks_completed_this_week' => 0]);
        })->weekly()->mondays()->at('00:00');

        // Reset monthly task counters (first day of month at midnight)
        $schedule->call(function () {
            \App\Models\User::query()->update(['tasks_completed_this_month' => 0]);
        })->monthlyOn(1, '00:00');

        // Mature pending earnings to withdrawable (runs every hour)
        $schedule->command('tasks:mature-earnings')->hourly();

        // Disburse pending commissions in batches (runs every minute for testing)
        $schedule->command('commissions:disburse')->everyMinute();

        // Calculate daily burn rate for liquidity monitoring (runs daily at midnight)
        $schedule->command('liquidity:calculate-burn-rate')->dailyAt('00:01');

        // Calculate star ratings for all active users (runs daily at 1:00 AM)
        $schedule->command('performance:calculate')->dailyAt('01:00');

        // ===== CONTENT POOL & TASK CLEANUP =====
        // Fill content pool daily at 2:00 AM (adds fresh content for task generation)
        // DEFAULT: Groq AI (country-specific, realistic surveys & reviews - ENABLED)
        $schedule->command('content:fill-pool --videos=30 --surveys=30 --reviews=25')->dailyAt('02:00');

        // ALTERNATIVE: Use Trivia API for general knowledge surveys (uncomment if needed)
        // $schedule->command('content:fill-pool --videos=30 --surveys=50 --reviews=25 --use-trivia')->dailyAt('02:00');

        // Cleanup old tasks daily at 3:00 AM (deactivates after 7 days, deletes after 30 days)
        // $schedule->command('tasks:cleanup')->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
