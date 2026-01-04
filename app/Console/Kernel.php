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

        // Cleanup and regenerate tasks nightly (runs at 3 AM daily - fresh tasks for the day)
        $schedule->job(new \App\Jobs\CleanupAndRegenerateTasksJob)->dailyAt('03:00');

        // Assign daily tasks to all active users (runs at 12:01 AM daily)
        $schedule->command('tasks:assign-daily')->dailyAt('00:01');

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
