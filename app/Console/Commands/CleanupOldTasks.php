<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TaskTemplate;
use App\Models\UserTask;
use App\Models\GlobalSetting;
use App\Services\ContentPoolService;
use Illuminate\Support\Facades\Log;

class CleanupOldTasks extends Command
{
    protected $signature = 'tasks:cleanup {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Cleanup old tasks based on global settings (deactivation & deletion days)';

    protected $contentPool;

    public function __construct(ContentPoolService $contentPool)
    {
        parent::__construct();
        $this->contentPool = $contentPool;
    }

    public function handle()
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🧹 Task Cleanup Process');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $settings = GlobalSetting::first();
        $deactivationDays = $settings->task_deactivation_days ?? 7;
        $deletionDays = $settings->task_deletion_days ?? 30;
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No actual changes will be made');
            $this->newLine();
        }

        // 1. DEACTIVATE old TaskTemplates
        $this->info("📋 Step 1: Deactivating TaskTemplates older than {$deactivationDays} days...");
        $toDeactivate = TaskTemplate::where('is_active', true)
            ->where('created_at', '<', now()->subDays($deactivationDays))
            ->count();

        if ($toDeactivate > 0) {
            if (!$isDryRun) {
                $deactivated = TaskTemplate::where('is_active', true)
                    ->where('created_at', '<', now()->subDays($deactivationDays))
                    ->update(['is_active' => false]);
                $this->info("  ✓ Deactivated {$deactivated} task templates");
                Log::info("Deactivated {$deactivated} old task templates");
            } else {
                $this->warn("  [DRY RUN] Would deactivate {$toDeactivate} task templates");
            }
        } else {
            $this->info("  ✓ No task templates to deactivate");
        }

        $this->newLine();

        // 2. DELETE old TaskTemplates (permanently)
        $this->info("🗑️  Step 2: Deleting TaskTemplates older than {$deletionDays} days...");
        $toDelete = TaskTemplate::where('created_at', '<', now()->subDays($deletionDays))->count();

        if ($toDelete > 0) {
            if (!$isDryRun) {
                $deleted = TaskTemplate::where('created_at', '<', now()->subDays($deletionDays))->delete();
                $this->info("  ✓ Deleted {$deleted} task templates permanently");
                Log::info("Deleted {$deleted} old task templates");
            } else {
                $this->warn("  [DRY RUN] Would delete {$toDelete} task templates");
            }
        } else {
            $this->info("  ✓ No task templates to delete");
        }

        $this->newLine();

        // 3. DELETE completed UserTasks older than deletion days
        $this->info("👤 Step 3: Deleting completed UserTasks older than {$deletionDays} days...");
        $userTasksToDelete = UserTask::where('status', 'completed')
            ->where('completed_at', '<', now()->subDays($deletionDays))
            ->count();

        if ($userTasksToDelete > 0) {
            if (!$isDryRun) {
                $deletedUserTasks = UserTask::where('status', 'completed')
                    ->where('completed_at', '<', now()->subDays($deletionDays))
                    ->delete();
                $this->info("  ✓ Deleted {$deletedUserTasks} completed user tasks");
                Log::info("Deleted {$deletedUserTasks} old completed user tasks");
            } else {
                $this->warn("  [DRY RUN] Would delete {$userTasksToDelete} completed user tasks");
            }
        } else {
            $this->info("  ✓ No completed user tasks to delete");
        }

        $this->newLine();

        // 4. CLEANUP Content Pool (old/overused items)
        $this->info("🗄️  Step 4: Cleaning up Content Pool (90+ days old, 50+ uses)...");
        if (!$isDryRun) {
            $poolDeleted = $this->contentPool->cleanup(90, 50);
            $this->info("  ✓ Deleted {$poolDeleted} overused pool items");
        } else {
            $poolToDelete = \App\Models\TaskContentPool::where('created_at', '<', now()->subDays(90))
                ->where('times_used', '>', 50)
                ->count();
            $this->warn("  [DRY RUN] Would delete {$poolToDelete} pool items");
        }

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        if ($isDryRun) {
            $this->warn('✅ Dry Run Complete - No changes made');
        } else {
            $this->info('✅ Cleanup Complete!');
        }
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        return Command::SUCCESS;
    }
}
