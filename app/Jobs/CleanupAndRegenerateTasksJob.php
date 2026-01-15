<?php

namespace App\Jobs;

use App\Models\GlobalSetting;
use App\Models\TaskTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CleanupAndRegenerateTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    public function __construct()
    {
        $this->onQueue('default');
    }

    /**
     * Execute the job - Generate fresh tasks daily (Age-Based Assignment)
     *
     * Strategy:
     * - Generate NEW tasks daily without deleting old ones
     * - Deactivate tasks older than 7 days (prevent assignment but keep for history)
     * - Delete tasks older than 30 days (permanent cleanup)
     * - Assignment logic filters to only tasks created in last 48 hours
     */
    public function handle(): void
    {
        Log::info('🧹 Starting nightly task generation and cleanup');

        try {
            // Get cleanup settings from global settings
            $settings = GlobalSetting::first();
            $deactivationDays = $settings->task_deactivation_days ?? 7;
            $deletionDays = $settings->task_deletion_days ?? 30;

            // Step 1: Deactivate old task templates
            $deactivatedCount = TaskTemplate::where('created_at', '<', now()->subDays($deactivationDays))
                ->where('is_active', true)
                ->update(['is_active' => false]);

            Log::info("✓ Deactivated {$deactivatedCount} old task templates (>{$deactivationDays} days)");

            // Step 2: Delete very old task templates to save space
            $deletedCount = TaskTemplate::where('created_at', '<', now()->subDays($deletionDays))->delete();
            Log::info("✓ Deleted {$deletedCount} expired task templates (>{$deletionDays} days)");

            // Step 3: Trigger fresh task generation (respects frequency settings)
            Log::info('🚀 Checking if task generation is needed...');

            Artisan::call('tasks:generate-templates'); // No --force, respects frequency!

            $output = Artisan::output();
            Log::info('Task generation command output:', ['output' => $output]);

            // Step 4: Clean up expired UserTasks
            $expiredUserTasks = \App\Models\UserTask::where('expires_at', '<', now()->subDays($deactivationDays))->delete();
            Log::info("✓ Cleaned up {$expiredUserTasks} expired UserTasks (>{$deactivationDays} days)");

            Log::info('✅ Nightly task generation and cleanup completed successfully', [
                'deactivated_tasks' => $deactivatedCount,
                'deleted_old_tasks' => $deletedCount,
                'deleted_user_tasks' => $expiredUserTasks,
                'deactivation_threshold' => "{$deactivationDays} days",
                'deletion_threshold' => "{$deletionDays} days",
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Nightly task generation and cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupAndRegenerateTasksJob failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
