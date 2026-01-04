<?php

namespace App\Console\Commands;

use App\Models\GlobalSetting;
use App\Services\AITaskGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateTaskTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:generate-templates {--force : Force generation even if threshold not reached}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate task templates using AI with batch processing and rate limiting';

    protected $taskGenerator;

    public function __construct(AITaskGeneratorService $taskGenerator)
    {
        parent::__construct();
        $this->taskGenerator = $taskGenerator;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🤖 AI Task Template Generator (Batched)');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        // Check if generation is needed
        if (!$this->option('force') && !$this->taskGenerator->shouldGenerateTasks()) {
            $this->warn('⚠ Task generation not needed. Active templates above threshold.');
            $this->info('💡 Use --force to generate anyway');
            return Command::SUCCESS;
        }

        if ($this->option('force')) {
            $this->warn('⚡ Forced generation mode enabled');
        }

        $this->newLine();

        // Get task counts from settings
        $settings = GlobalSetting::first();
        $tasksConfig = $settings->ai_configuration['tasks_to_generate'] ?? [];

        $videoCount = $tasksConfig['videos'] ?? 6;
        $surveyCount = $tasksConfig['surveys'] ?? 7;
        $reviewCount = $tasksConfig['reviews'] ?? 5;
        $syncCount = $tasksConfig['syncs'] ?? 3;

        $totalTasks = $videoCount + $surveyCount + $reviewCount + $syncCount;

        $this->info("📦 Preparing to generate {$totalTasks} tasks:");
        $this->line("  🎬 Videos: {$videoCount}");
        $this->line("  📋 Surveys: {$surveyCount}");
        $this->line("  ⭐ Reviews: {$reviewCount}");
        $this->line("  🔄 Syncs: {$syncCount}");
        $this->newLine();

        // Generate tasks directly with delays to avoid rate limiting
        $generated = ['videos' => 0, 'surveys' => 0, 'reviews' => 0, 'syncs' => 0];

        $settings = GlobalSetting::first();
        $countryCode = $settings->country_of_operation ?? 'NGA';
        $country = \App\Helpers\CountryHelper::getByAlpha3($countryCode);
        $countryName = $country['name'] ?? 'Nigeria';

        try {
            // Videos - immediate
            if ($videoCount > 0) {
                $this->info("🎬 Generating {$videoCount} video tasks...");
                $generated['videos'] = $this->taskGenerator->generateVideoTasks($countryCode, $videoCount);
                $this->info("  ✓ Generated {$generated['videos']} videos");

                if ($surveyCount > 0 || $reviewCount > 0 || $syncCount > 0) {
                    $this->info("  ⏱ Waiting 60s before next batch...");
                    sleep(60);
                }
            }

            // Surveys - after 60s
            if ($surveyCount > 0) {
                $this->info("📋 Generating {$surveyCount} survey tasks...");
                $generated['surveys'] = $this->taskGenerator->generateSurveys($countryName, $surveyCount);
                $this->info("  ✓ Generated {$generated['surveys']} surveys");

                if ($reviewCount > 0 || $syncCount > 0) {
                    $this->info("  ⏱ Waiting 60s before next batch...");
                    sleep(60);
                }
            }

            // Reviews - after another 60s
            if ($reviewCount > 0) {
                $this->info("⭐ Generating {$reviewCount} review tasks...");
                $generated['reviews'] = $this->taskGenerator->generateProductReviews($countryName, $reviewCount);
                $this->info("  ✓ Generated {$generated['reviews']} reviews");

                if ($syncCount > 0) {
                    $this->info("  ⏱ Waiting 60s before next batch...");
                    sleep(60);
                }
            }

            // Syncs - after another 60s
            if ($syncCount > 0) {
                $this->info("🔄 Generating {$syncCount} sync tasks...");
                $generated['syncs'] = $this->taskGenerator->generateSyncTasks($syncCount);
                $this->info("  ✓ Generated {$generated['syncs']} syncs");
            }

            $totalGenerated = array_sum($generated);

            $this->newLine();
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info('✅ Task Generation Complete!');
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info("📊 Total Generated: {$totalGenerated} tasks");
            $this->info("  🎬 Videos: {$generated['videos']}");
            $this->info("  📋 Surveys: {$generated['surveys']}");
            $this->info("  ⭐ Reviews: {$generated['reviews']}");
            $this->info("  🔄 Syncs: {$generated['syncs']}");
            $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

            Log::info('Task generation completed', [
                'total_generated' => $totalGenerated,
                'breakdown' => $generated
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Task generation failed: {$e->getMessage()}");
            Log::error('Task generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}
