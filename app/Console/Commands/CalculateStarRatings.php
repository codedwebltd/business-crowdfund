<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserPerformance;
use App\Services\PerformanceCalculationService;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CalculateStarRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:calculate {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate star ratings for users based on performance (tasks + referrals)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌟 Starting star rating calculation...');

        $performanceService = app(PerformanceCalculationService::class);
        $notificationService = app(NotificationService::class);

        $userId = $this->option('user');

        if ($userId) {
            // Calculate for specific user
            $user = User::find($userId);
            if (!$user) {
                $this->error("User not found: {$userId}");
                return 1;
            }

            $this->processUser($user, $performanceService, $notificationService);
        } else {
            // Calculate for all active users
            $count = 0;
            $promoted = 0;
            $demoted = 0;

            User::where('status', 'ACTIVE')
                ->chunk(100, function ($users) use (&$count, &$promoted, &$demoted, $performanceService, $notificationService) {
                    foreach ($users as $user) {
                        $result = $this->processUser($user, $performanceService, $notificationService);
                        $count++;

                        if ($result['promoted']) $promoted++;
                        if ($result['demoted']) $demoted++;
                    }
                });

            $this->info("✅ Processed {$count} users");
            $this->info("📈 Promoted: {$promoted}");
            $this->info("📉 Demoted: {$demoted}");
        }

        return 0;
    }

    protected function processUser(User $user, PerformanceCalculationService $performanceService, NotificationService $notificationService): array
    {
        // Get old performance if exists
        $oldPerformance = $user->performance;
        $oldStars = $oldPerformance?->star_rating ?? 0;

        // Calculate new performance
        $newPerformance = $performanceService->calculateUserPerformance($user);

        $promoted = false;
        $demoted = false;

        // Check for star rating change
        if ($oldStars > 0 && $newPerformance->star_rating !== $oldStars) {
            if ($newPerformance->star_rating > $oldStars) {
                // Promoted
                $promoted = true;
                $this->line("📈 {$user->email}: {$oldStars}⭐ → {$newPerformance->star_rating}⭐ (PROMOTED)");

                // Send promotion notification
                $notificationService->sendStarRatingChange($user, $oldStars, $newPerformance->star_rating, 'promoted');
            } else {
                // Demoted
                $demoted = true;
                $this->line("📉 {$user->email}: {$oldStars}⭐ → {$newPerformance->star_rating}⭐ (DEMOTED)");

                // Send demotion notification
                $notificationService->sendStarRatingChange($user, $oldStars, $newPerformance->star_rating, 'demoted');
            }
        } else {
            $this->line("✓ {$user->email}: {$newPerformance->star_rating}⭐");
        }

        return ['promoted' => $promoted, 'demoted' => $demoted];
    }
}
