<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ContentPoolService;
use App\Services\TriviaAPIService;
use App\Services\GroqService;
use App\Services\AITaskGeneratorService;
use App\Models\GlobalSetting;
use App\Helpers\CountryHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FillContentPool extends Command
{
    protected $signature = 'content:fill-pool
                            {--videos=30}
                            {--surveys=50}
                            {--reviews=20}
                            {--use-trivia : Use Trivia API for general knowledge surveys (default: Groq country-specific)}
                            {--reduce-reviews=25 : Recommended daily count for reviews (default 25)}';
    protected $description = 'Fill task content pool with fresh content from APIs (runs slowly, no rush)';

    protected $contentPool;
    protected $triviaService;
    protected $groqService;
    protected $taskGenerator;

    public function __construct(
        ContentPoolService $contentPool,
        TriviaAPIService $triviaService,
        GroqService $groqService,
        AITaskGeneratorService $taskGenerator
    ) {
        parent::__construct();
        $this->contentPool = $contentPool;
        $this->triviaService = $triviaService;
        $this->groqService = $groqService;
        $this->taskGenerator = $taskGenerator;
    }

    public function handle()
    {
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('🗄️  Content Pool Filler (Slow & Steady)');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $added = [
            'videos' => $this->fillVideos($this->option('videos')),
            'surveys' => $this->fillSurveys($this->option('surveys')),
            'reviews' => $this->fillReviews($this->option('reviews')),
        ];

        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ Pool Fill Complete!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("📹 Videos: {$added['videos']}");
        $this->info("📋 Surveys: {$added['surveys']}");
        $this->info("⭐ Reviews: {$added['reviews']}");
        $this->newLine();

        // Show stats
        $stats = $this->contentPool->getStats();
        $this->info("📊 Total Pool Size: {$stats['total']}");
        $this->info("   Unused: {$stats['unused']}");

        return Command::SUCCESS;
    }

    protected function fillVideos($count)
    {
        $this->info("📹 Fetching {$count} YouTube videos...");
        $added = 0;

        try {
            $settings = GlobalSetting::first();
            $countryCode = $settings->country_of_operation ?? 'NGA';

            // Use fetchYouTubeTrending method from AITaskGeneratorService
            $reflection = new \ReflectionClass($this->taskGenerator);
            $method = $reflection->getMethod('fetchYouTubeTrending');
            $method->setAccessible(true);
            $videos = $method->invoke($this->taskGenerator, $countryCode, $count);

            foreach ($videos as $video) {
                // Generate questions using QuestionPoolService
                $questions = app(\App\Services\QuestionPoolService::class)->generateVideoQuestions(
                    $video['title'],
                    $video['description'] ?? '',
                    5
                );

                if ($questions && isset($questions['questions'])) {
                    $this->contentPool->addToPool(
                        'VIDEO',
                        'Watch: ' . substr($video['title'], 0, 200),
                        [
                            'video_id' => $video['video_id'],
                            'video_url' => 'https://www.youtube.com/embed/' . $video['video_id'],
                            'video_duration' => $video['duration'] ?? 300,
                            'description' => 'Watch this video and answer questions to verify',
                            'questions' => $questions['questions'],
                        ],
                        'youtube'
                    );
                    $added++;
                }
            }

            $this->info("  ✓ Added {$added} videos to pool");
        } catch (\Exception $e) {
            $this->error("  ✗ Video fetch failed: " . $e->getMessage());
        }

        return $added;
    }

    protected function fillSurveys($count)
    {
        $added = 0;

        // Option 1: Use Trivia API (only if --use-trivia flag is set)
        if ($this->option('use-trivia')) {
            $this->info("📋 Fetching {$count} trivia surveys (general knowledge)...");

            try {
                // Fetch surveys in bulk (calls API efficiently)
                $surveys = $this->triviaService->generateBulkSurveys($count, 10);

                foreach ($surveys as $survey) {
                    $this->contentPool->addToPool(
                        'SURVEY',
                        $survey['title'],
                        [
                            'description' => $survey['description'],
                            'questions' => $survey['questions'],
                            'category' => $survey['category'],
                        ],
                        'trivia'
                    );
                    $added++;
                }

                $this->info("  ✓ Added {$added} trivia surveys to pool");
            } catch (\Exception $e) {
                $this->error("  ✗ Survey fetch failed: " . $e->getMessage());
            }
        }
        // Option 2: Use Groq for country-specific surveys (DEFAULT - better quality)
        else {
            $this->info("📋 Fetching {$count} country-specific surveys using Groq AI...");

            try {
                $settings = GlobalSetting::first();
                $countryCode = $settings->country_of_operation ?? 'NGA';
                $country = CountryHelper::getByAlpha3($countryCode);
                $countryName = $country['name'] ?? 'Nigeria';

                for ($i = 0; $i < $count; $i++) {
                    $surveyData = $this->groqService->generateSurvey($countryName, rand(8, 12));

                    if ($surveyData && isset($surveyData['title'], $surveyData['questions'])) {
                        $this->contentPool->addToPool(
                            'SURVEY',
                            $surveyData['title'],
                            [
                                'description' => $surveyData['description'] ?? 'Complete this survey',
                                'questions' => $surveyData['questions'],
                                'category' => $surveyData['category'] ?? 'General',
                            ],
                            'groq'
                        );
                        $added++;
                    }

                    // Small delay to avoid rate limits
                    if ($i < $count - 1 && $i % 10 == 0) {
                        sleep(5);
                    }
                }

                $this->info("  ✓ Added {$added} Groq surveys to pool");
            } catch (\Exception $e) {
                $this->error("  ✗ Groq survey fetch failed: " . $e->getMessage());
            }
        }

        return $added;
    }

    protected function fillReviews($count)
    {
        $this->info("⭐ Fetching {$count} product reviews...");
        $added = 0;

        try {
            $settings = GlobalSetting::first();
            $country = CountryHelper::getByAlpha3($settings->country_of_operation ?? 'NGA');
            $countryName = $country['name'] ?? 'Nigeria';

            // Try Groq first
            $productData = $this->groqService->generateProductNames($countryName, $count);

            // Fallback to static pool if Groq fails
            if (!$productData || empty($productData['products'])) {
                $reflection = new \ReflectionClass($this->taskGenerator);
                $method = $reflection->getMethod('getStaticProductPool');
                $method->setAccessible(true);
                $productData = $method->invoke($this->taskGenerator, $countryName, $count);
            }

            foreach ($productData['products'] as $product) {
                $this->contentPool->addToPool(
                    'REVIEW',
                    'Review: ' . $product['name'],
                    [
                        'product_name' => $product['name'],
                        'product_category' => $product['category'] ?? 'General',
                        'description' => "Share your honest review of {$product['name']}",
                        'review_min_characters' => 20,
                    ],
                    'groq'
                );
                $added++;
            }

            $this->info("  ✓ Added {$added} reviews to pool");
        } catch (\Exception $e) {
            $this->error("  ✗ Review fetch failed: " . $e->getMessage());
        }

        return $added;
    }
}

