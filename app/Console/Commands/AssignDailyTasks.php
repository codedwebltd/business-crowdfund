<?php

namespace App\Console\Commands;

use App\Jobs\AssignTaskBatch;
use App\Models\GlobalSetting;
use App\Models\Plan;
use App\Models\TaskTemplate;
use App\Models\User;
use App\Models\UserTask;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignDailyTasks extends Command
{
    protected $signature = 'tasks:assign-daily';
    protected $description = 'Assign daily tasks to all active users based on their plan';

    public function handle()
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🎯 Daily Task Assignment');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $startTime = microtime(true);

        // Get global settings
        $settings = GlobalSetting::first();
        $dailyLimits = $settings->daily_task_limits ?? ['basic' => 8, 'premium' => 15, 'vip' => 25];
        $distribution = $settings->task_distribution_percentages ?? ['SURVEY' => 60, 'VIDEO' => 20, 'APP_SYNC' => 15, 'PRODUCT_REVIEW' => 5];

        // Get all active users with their performance data
        $users = User::where('status', 'active')->with('performance')->get();

        $this->info("Found {$users->count()} active users");
        $this->newLine();

        $assigned = 0;
        $errors = 0;
        $batchJobs = [];
        $userTaskCounts = [];

        $this->info("Preparing batch jobs for {$users->count()} users...");
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            try {
                // Clear yesterday's pending/in_progress tasks -> mark as EXPIRED (from map.md)
                UserTask::where('user_id', $user->id)
                    ->whereIn('status', ['PENDING', 'IN_PROGRESS'])
                    ->whereDate('assigned_at', '<', today())
                    ->update(['status' => 'EXPIRED']);

                // Get user's plan
                $plan = $user->plan;
                $planName = strtolower($plan->name ?? 'basic');
                $maxTasks = $dailyLimits[$planName] ?? 8;

                // Calculate task distribution (from map.md: 60% surveys, 20% videos, 15% syncs, 5% reviews)
                $taskCount = [
                    'SURVEY' => (int) ceil($maxTasks * (($distribution['SURVEY'] ?? 60) / 100)),
                    'VIDEO' => (int) ceil($maxTasks * (($distribution['VIDEO'] ?? 20) / 100)),
                    'APP_SYNC' => (int) ceil($maxTasks * (($distribution['APP_SYNC'] ?? 15) / 100)),
                    'PRODUCT_REVIEW' => (int) ceil($maxTasks * (($distribution['PRODUCT_REVIEW'] ?? 5) / 100)),
                ];

                // Adjust to exact max tasks (from map.md logic)
                $total = array_sum($taskCount);
                if ($total > $maxTasks) {
                    $taskCount['SURVEY'] -= ($total - $maxTasks);
                }

                $taskTemplateIds = [];

                // Get user's star rating for performance-based task assignment
                $starRating = $user->performance->star_rating ?? 3; // Default to 3-star (medium)

                // Collect tasks by category for batch job
                foreach ($taskCount as $category => $count) {
                    if ($count <= 0) continue;

                    // Build base query for available tasks in this category
                    $query = TaskTemplate::active()
                        ->available()
                        ->byCategory($category)
                        ->where(function($q) use ($user) {
                            $q->whereNull('min_rank_id')
                              ->orWhere('min_rank_id', '<=', $user->rank_id ?? 1);
                        });

                    // Performance-based task assignment:
                    // High performers (4-5 stars) get highest value tasks
                    // Medium performers (3 stars) get random tasks (maintain current behavior)
                    // Low performers (1-2 stars) get lower value tasks
                    if ($starRating >= 4) {
                        // High performers: prioritize highest rewards
                        $query->orderBy('reward_amount', 'DESC');
                    } elseif ($starRating <= 2) {
                        // Low performers: get lower value tasks
                        $query->orderBy('reward_amount', 'ASC');
                    } else {
                        // Medium performers (3-star): random selection
                        $query->inRandomOrder();
                    }

                    $templates = $query->limit($count)->get();

                    foreach ($templates as $template) {
                        $taskTemplateIds[] = $template->id;
                    }
                }

                // Add batch job for this user
                if (!empty($taskTemplateIds)) {
                    $batchJobs[] = new AssignTaskBatch($user->id, $taskTemplateIds, 24);
                    $userTaskCounts[$user->id] = count($taskTemplateIds);
                    $assigned += count($taskTemplateIds);
                }

            } catch (\Exception $e) {
                $errors++;
                Log::error('Task preparation failed for user ' . $user->id, [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Dispatch batch jobs with notification callback
        if (!empty($batchJobs)) {
            $this->info("Dispatching batch of {$users->count()} jobs...");

            $batch = Bus::batch($batchJobs)
                ->name('Daily Task Assignment - ' . now()->format('Y-m-d'))
                ->allowFailures()
                ->finally(function () use ($userTaskCounts) {
                    // Send notifications after batch completes
                    $notificationService = app(NotificationService::class);
                    $users = User::whereIn('id', array_keys($userTaskCounts))->get();

                    foreach ($users as $user) {
                        $count = $userTaskCounts[$user->id] ?? 0;
                        if ($count > 0) {
                            try {
                                $notificationService->send($user, 'tasks_assigned', [
                                    'count' => $count,
                                    'message' => "🎯 {$count} new tasks available! Start earning now.",
                                ]);
                            } catch (\Exception $e) {
                                Log::error("Failed to notify user {$user->id}: " . $e->getMessage());
                            }
                        }
                    }
                })
                ->dispatch();

            $this->info("Batch dispatched! ID: {$batch->id}");
            $this->info("Run 'php artisan queue:work' to process batch jobs");
        }

        $duration = round(microtime(true) - $startTime, 2);

        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Task Assignment Complete!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("✓ Users processed: {$users->count()}");
        $this->info("✓ Tasks assigned: {$assigned}");
        $this->info("✗ Errors: {$errors}");
        $this->info("⏱  Duration: {$duration}s");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        Log::info('Daily task assignment completed', [
            'users_processed' => $users->count(),
            'tasks_assigned' => $assigned,
            'errors' => $errors,
            'duration' => $duration
        ]);

        return Command::SUCCESS;
    }
}
