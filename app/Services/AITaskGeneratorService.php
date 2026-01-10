<?php

namespace App\Services;

use App\Models\GlobalSetting;
use App\Models\TaskTemplate;
use App\Helpers\CountryHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class AITaskGeneratorService
{
    protected $groqService;
    protected $settings;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
        $this->settings = null;
    }

    protected function getSettings()
    {
        if ($this->settings === null) {
            $this->settings = GlobalSetting::first();
        }
        return $this->settings;
    }

    /**
     * Check if task generation is needed
     */
    public function shouldGenerateTasks()
    {
        $settings = $this->getSettings();

        if (!$settings->ai_task_generation_enabled) {
            Log::info('AI task generation is disabled');
            return false;
        }

        $activeTaskCount = TaskTemplate::active()->available()->count();
        $threshold = $settings->min_task_templates_threshold ?? 50;

        if ($activeTaskCount < $threshold) {
            Log::info("Task generation needed: {$activeTaskCount} tasks < {$threshold} threshold");
            return true;
        }

        Log::info("Task generation not needed: {$activeTaskCount} tasks >= {$threshold} threshold");
        return false;
    }

    /**
     * Generate all task types
     */
    public function generateAllTasks()
    {
        $settings = $this->getSettings();

        $countryCode = $settings->country_of_operation ?? 'NGA';
        $country = CountryHelper::getByAlpha3($countryCode);
        $countryName = $country['name'] ?? 'Nigeria';

        $tasksConfig = $settings->ai_configuration['tasks_to_generate'] ?? [];

        $results = [
            'surveys' => 0,
            'videos' => 0,
            'syncs' => 0,
            'reviews' => 0,
            'errors' => []
        ];

        // Generate surveys
        if (($tasksConfig['surveys'] ?? 0) > 0) {
            try {
                $results['surveys'] = $this->generateSurveys($countryName, $tasksConfig['surveys']);
            } catch (Exception $e) {
                $results['errors'][] = "Surveys: " . $e->getMessage();
                Log::error('Survey generation failed', ['error' => $e->getMessage()]);
            }
        }

        // Generate video tasks
        if (($tasksConfig['videos'] ?? 0) > 0) {
            try {
                $results['videos'] = $this->generateVideoTasks($countryCode, $tasksConfig['videos']);
            } catch (Exception $e) {
                $results['errors'][] = "Videos: " . $e->getMessage();
                Log::error('Video generation failed', ['error' => $e->getMessage()]);
            }
        }

        // Generate sync tasks (template-based, no AI needed)
        if (($tasksConfig['syncs'] ?? 0) > 0) {
            try {
                $results['syncs'] = $this->generateSyncTasks($tasksConfig['syncs']);
            } catch (Exception $e) {
                $results['errors'][] = "Syncs: " . $e->getMessage();
                Log::error('Sync generation failed', ['error' => $e->getMessage()]);
            }
        }

        // Generate product review tasks
        if (($tasksConfig['reviews'] ?? 0) > 0) {
            try {
                $results['reviews'] = $this->generateProductReviews($countryName, $tasksConfig['reviews']);
            } catch (Exception $e) {
                $results['errors'][] = "Reviews: " . $e->getMessage();
                Log::error('Review generation failed', ['error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * Generate survey tasks using AI
     */
    public function generateSurveys($country, $count)
    {
        $generated = 0;

        for ($i = 0; $i < $count; $i++) {
            $surveyData = $this->groqService->generateSurvey($country, rand(8, 15));

            if (!$surveyData || !isset($surveyData['title'], $surveyData['questions'])) {
                Log::warning("Failed to generate survey {$i}");
                continue;
            }

            // Get reward range from settings
            $settings = $this->getSettings();
            $rewardRange = $settings->ai_configuration['reward_ranges']['survey'] ?? ['min' => 30, 'max' => 100];

            // Create task template
            $task = TaskTemplate::create([
                'category' => 'SURVEY',
                'title' => substr($surveyData['title'], 0, 255),
                'description' => $surveyData['description'] ?? 'Complete this survey to earn rewards',
                'questions' => $surveyData['questions'],
                'reward_amount' => rand($rewardRange['min'], $rewardRange['max']),
                'completion_time_seconds' => count($surveyData['questions']) * 20, // ~20 seconds per question
                'is_active' => true, // Admin must review and activate
                'priority' => rand(1, 10),
                'max_completions' => rand(500, 2000),
                'min_rank_id' => null,
                'available_from' => now(),
                'available_until' => now()->addMonths(3),
            ]);

            $generated++;
            Log::info("Generated survey task", ['id' => $task->id, 'title' => $task->title]);
        }

        return $generated;
    }

    /**
     * Generate video watch tasks using YouTube API
     */
    public function generateVideoTasks($countryCode, $count)
    {
        $generated = 0;

        try {
            // Fetch YouTube trending videos
            $videos = $this->fetchYouTubeTrending($countryCode, $count);

            foreach ($videos as $video) {
                // Generate verification questions using AI
                $questions = $this->groqService->generateVideoQuestions(
                    $video['title'],
                    $video['description'] ?? '',
                    5
                );

                if (!$questions || !isset($questions['questions'])) {
                    Log::warning("Failed to generate questions for video: {$video['title']}");
                    continue;
                }

                $settings = $this->getSettings();
                $rewardRange = $settings->ai_configuration['reward_ranges']['video'] ?? ['min' => 150, 'max' => 400];

                $task = TaskTemplate::create([
                    'category' => 'VIDEO',
                    'title' => 'Watch: ' . substr($video['title'], 0, 200),
                    'description' => 'Watch this video and answer questions to verify',
                    'video_url' => 'https://www.youtube.com/embed/' . $video['video_id'],
                    'video_duration_seconds' => $video['duration'] ?? 300,
                    'questions' => $questions['questions'], // Verification questions
                    'reward_amount' => rand($rewardRange['min'], $rewardRange['max']),
                    'completion_time_seconds' => ($video['duration'] ?? 300) + 60, // Video + questions
                    'is_active' => true,
                    'priority' => rand(1, 10),
                    'max_completions' => rand(300, 1000),
                    'min_rank_id' => null,
                    'available_from' => now(),
                    'available_until' => now()->addMonths(2),
                ]);

                $generated++;
                Log::info("Generated video task", ['id' => $task->id, 'video_id' => $video['video_id']]);
            }

        } catch (Exception $e) {
            Log::error('YouTube API failed', ['error' => $e->getMessage()]);
        }

        return $generated;
    }

    /**
     * Generate sync tasks (template-based)
     */
    public function generateSyncTasks($count)
    {
        $generated = 0;

        $syncTemplates = [
            ['title' => 'Daily App Usage Data Sync', 'description' => 'Sync your device app usage data for research'],
            ['title' => 'Network Performance Analysis', 'description' => 'Analyze your network connection quality'],
            ['title' => 'Device Security Scan', 'description' => 'Scan device for security insights'],
            ['title' => 'Battery Usage Report', 'description' => 'Share battery consumption patterns'],
            ['title' => 'Screen Time Analysis', 'description' => 'Sync your daily screen time data'],
        ];

        $settings = $this->getSettings();
        $syncReward = $settings->ai_configuration['reward_ranges']['sync']['fixed'] ?? 200;

        for ($i = 0; $i < min($count, count($syncTemplates)); $i++) {
            $template = $syncTemplates[$i];

            $task = TaskTemplate::create([
                'category' => 'APP_SYNC',
                'title' => $template['title'],
                'description' => $template['description'],
                'sync_duration_seconds' => rand(30, 60),
                'required_data_points' => ['device_type', 'os_version', 'browser', 'screen_resolution', 'network_type'],
                'reward_amount' => $syncReward,
                'completion_time_seconds' => 60,
                'is_active' => true,
                'priority' => rand(1, 10),
                'max_completions' => rand(1000, 5000),
                'min_rank_id' => null,
                'available_from' => now(),
                'available_until' => now()->addMonths(6),
            ]);

            $generated++;
            Log::info("Generated sync task", ['id' => $task->id, 'title' => $task->title]);
        }

        return $generated;
    }

    /**
     * Generate product review tasks using AI
     */
    public function generateProductReviews($country, $count)
    {
        $generated = 0;

        // Generate product names using AI
        $productData = $this->groqService->generateProductNames($country, $count);

        if (!$productData || !isset($productData['products'])) {
            Log::warning('Failed to generate product names');
            return 0;
        }

        $settings = $this->getSettings();
        $rewardRange = $settings->ai_configuration['reward_ranges']['review'] ?? ['min' => 50, 'max' => 80];

        foreach ($productData['products'] as $product) {
            $task = TaskTemplate::create([
                'category' => 'PRODUCT_REVIEW',
                'title' => 'Review: ' . $product['name'],
                'description' => "Share your honest review of {$product['name']}",
                'product_name' => $product['name'],
                'product_category' => $product['category'] ?? 'General',
                'review_min_characters' => 20,
                'reward_amount' => rand($rewardRange['min'], $rewardRange['max']),
                'completion_time_seconds' => 120,
                'is_active' => true,
                'priority' => rand(1, 10),
                'max_completions' => rand(200, 800),
                'min_rank_id' => null,
                'available_from' => now(),
                'available_until' => now()->addMonths(3),
            ]);

            $generated++;
            Log::info("Generated review task", ['id' => $task->id, 'product' => $product['name']]);
        }

        return $generated;
    }

    /**
     * Fetch YouTube trending videos using YouTube Data API v3
     */
    protected function fetchYouTubeTrending($countryCode, $limit = 10)
    {
        // Get YouTube API key from settings (will add to UI later)
        $settings = $this->getSettings();
        $youtubeApiKey = $settings->ai_configuration['youtube_api_key']
            ?? 'AIzaSyA-aj1hVq7hmrqh5jJQHELNBnYDKdsfJTk';

        try {
            // Get ISO Alpha-2 code for YouTube API (e.g., NG, GB, US)
            $country = CountryHelper::getByAlpha3($countryCode);
            $regionCode = is_array($country) ? ($country['isoAlpha2'] ?? 'US') : 'US';

            $response = Http::timeout(15)->get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'snippet,contentDetails',
                'chart' => 'mostPopular',
                'regionCode' => $regionCode,
                'maxResults' => $limit,
                'key' => $youtubeApiKey
            ]);

            if (!$response->successful()) {
                Log::error('YouTube API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $data = $response->json();
            $videos = [];

            foreach ($data['items'] ?? [] as $item) {
                // Parse ISO 8601 duration (PT4M13S -> 253 seconds)
                $duration = $this->parseYouTubeDuration($item['contentDetails']['duration'] ?? 'PT5M');

                $videos[] = [
                    'video_id' => $item['id'],
                    'title' => $item['snippet']['title'],
                    'description' => $item['snippet']['description'] ?? '',
                    'duration' => $duration,
                    'thumbnail' => $item['snippet']['thumbnails']['high']['url'] ?? null,
                ];
            }

            Log::info('Fetched ' . count($videos) . ' trending videos from YouTube', ['region' => $regionCode]);
            return $videos;

        } catch (Exception $e) {
            Log::error('YouTube API fetch failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Parse YouTube ISO 8601 duration to seconds
     * Example: PT4M13S -> 253, PT1H2M10S -> 3730
     */
    protected function parseYouTubeDuration($duration)
    {
        preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/', $duration, $matches);

        $hours = isset($matches[1]) ? (int)$matches[1] : 0;
        $minutes = isset($matches[2]) ? (int)$matches[2] : 0;
        $seconds = isset($matches[3]) ? (int)$matches[3] : 0;

        return ($hours * 3600) + ($minutes * 60) + $seconds;
    }
}
