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
    public $tries = 1;

    public function __construct()
    {
        $this->onQueue('default');
    }

    /**
     * Execute the job - Clean up old tasks and regenerate fresh ones
     */
    public function handle(): void
    {
        Log::info('🧹 Starting nightly task cleanup and regeneration');

        try {
            // Step 1: Delete all existing task templates
            $deletedCount = TaskTemplate::query()->delete();
            Log::info("✓ Deleted {$deletedCount} task templates");

            // Step 2: Trigger regeneration via command
            Log::info('🚀 Triggering fresh task generation...');

            Artisan::call('tasks:generate-templates', ['--force' => true]);

            $output = Artisan::output();
            Log::info('Task generation command output:', ['output' => $output]);

            Log::info('✅ Nightly cleanup and regeneration completed successfully', [
                'deleted_tasks' => $deletedCount,
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Nightly cleanup and regeneration failed', [
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
