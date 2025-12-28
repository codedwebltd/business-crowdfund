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
