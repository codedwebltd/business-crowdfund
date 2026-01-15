<?php

namespace App\Console\Commands;

use App\Models\TaskTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupDuplicateTasks extends Command
{
    protected $signature = 'tasks:cleanup-duplicates {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Remove duplicate task templates, keeping only the newest version of each';

    public function handle()
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🧹 Cleaning Up Duplicate Tasks');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No tasks will be deleted');
            $this->newLine();
        }

        $totalDeleted = 0;

        // 1. Clean duplicate VIDEO tasks (same video_url)
        $this->info('📹 Checking VIDEO tasks...');
        $videoDeleted = $this->cleanDuplicateVideos($dryRun);
        $totalDeleted += $videoDeleted;
        $this->info("   Deleted: {$videoDeleted}");
        $this->newLine();

        // 2. Clean duplicate SURVEY tasks (same title)
        $this->info('📋 Checking SURVEY tasks...');
        $surveyDeleted = $this->cleanDuplicateSurveys($dryRun);
        $totalDeleted += $surveyDeleted;
        $this->info("   Deleted: {$surveyDeleted}");
        $this->newLine();

        // 3. Clean duplicate APP_SYNC tasks (same title)
        $this->info('🔄 Checking APP_SYNC tasks...');
        $syncDeleted = $this->cleanDuplicateSyncs($dryRun);
        $totalDeleted += $syncDeleted;
        $this->info("   Deleted: {$syncDeleted}");
        $this->newLine();

        // 4. Clean duplicate PRODUCT_REVIEW tasks (same product_name)
        $this->info('⭐ Checking PRODUCT_REVIEW tasks...');
        $reviewDeleted = $this->cleanDuplicateReviews($dryRun);
        $totalDeleted += $reviewDeleted;
        $this->info("   Deleted: {$reviewDeleted}");
        $this->newLine();

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        if ($dryRun) {
            $this->info("✅ Dry run complete! {$totalDeleted} tasks would be deleted");
        } else {
            $this->info("✅ Cleanup complete! {$totalDeleted} duplicate tasks deleted");
        }
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        Log::info('Duplicate task cleanup completed', [
            'total_deleted' => $totalDeleted,
            'dry_run' => $dryRun
        ]);

        return Command::SUCCESS;
    }

    /**
     * Clean duplicate VIDEO tasks (same video_url)
     */
    protected function cleanDuplicateVideos($dryRun)
    {
        $duplicates = DB::table('task_templates')
            ->select('video_url', DB::raw('COUNT(*) as count'))
            ->where('category', 'VIDEO')
            ->whereNotNull('video_url')
            ->groupBy('video_url')
            ->having('count', '>', 1)
            ->get();

        $deleted = 0;

        foreach ($duplicates as $duplicate) {
            // Get all tasks with this video_url, ordered by newest first
            $tasks = TaskTemplate::where('category', 'VIDEO')
                ->where('video_url', $duplicate->video_url)
                ->orderBy('created_at', 'DESC')
                ->get();

            // Keep the first (newest), delete the rest
            foreach ($tasks->skip(1) as $task) {
                if (!$dryRun) {
                    $task->delete();
                }
                $deleted++;
                $this->line("   - Removing duplicate: {$task->title} (ID: {$task->id})");
            }
        }

        return $deleted;
    }

    /**
     * Clean duplicate SURVEY tasks (same title)
     */
    protected function cleanDuplicateSurveys($dryRun)
    {
        $duplicates = DB::table('task_templates')
            ->select('title', DB::raw('COUNT(*) as count'))
            ->where('category', 'SURVEY')
            ->groupBy('title')
            ->having('count', '>', 1)
            ->get();

        $deleted = 0;

        foreach ($duplicates as $duplicate) {
            $tasks = TaskTemplate::where('category', 'SURVEY')
                ->where('title', $duplicate->title)
                ->orderBy('created_at', 'DESC')
                ->get();

            foreach ($tasks->skip(1) as $task) {
                if (!$dryRun) {
                    $task->delete();
                }
                $deleted++;
                $this->line("   - Removing duplicate: {$task->title} (ID: {$task->id})");
            }
        }

        return $deleted;
    }

    /**
     * Clean duplicate APP_SYNC tasks (same title)
     */
    protected function cleanDuplicateSyncs($dryRun)
    {
        $duplicates = DB::table('task_templates')
            ->select('title', DB::raw('COUNT(*) as count'))
            ->where('category', 'APP_SYNC')
            ->groupBy('title')
            ->having('count', '>', 1)
            ->get();

        $deleted = 0;

        foreach ($duplicates as $duplicate) {
            $tasks = TaskTemplate::where('category', 'APP_SYNC')
                ->where('title', $duplicate->title)
                ->orderBy('created_at', 'DESC')
                ->get();

            foreach ($tasks->skip(1) as $task) {
                if (!$dryRun) {
                    $task->delete();
                }
                $deleted++;
                $this->line("   - Removing duplicate: {$task->title} (ID: {$task->id})");
            }
        }

        return $deleted;
    }

    /**
     * Clean duplicate PRODUCT_REVIEW tasks (same title)
     */
    protected function cleanDuplicateReviews($dryRun)
    {
        $duplicates = DB::table('task_templates')
            ->select('title', DB::raw('COUNT(*) as count'))
            ->where('category', 'PRODUCT_REVIEW')
            ->groupBy('title')
            ->having('count', '>', 1)
            ->get();

        $deleted = 0;

        foreach ($duplicates as $duplicate) {
            $tasks = TaskTemplate::where('category', 'PRODUCT_REVIEW')
                ->where('title', $duplicate->title)
                ->orderBy('created_at', 'DESC')
                ->get();

            foreach ($tasks->skip(1) as $task) {
                if (!$dryRun) {
                    $task->delete();
                }
                $deleted++;
                $this->line("   - Removing duplicate: {$task->title} (ID: {$task->id})");
            }
        }

        return $deleted;
    }
}
