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
    protected $questionPoolService;
    protected $triviaApiService;
    protected $contentPool;
    protected $settings;

    public function __construct(
        GroqService $groqService,
        QuestionPoolService $questionPoolService,
        TriviaAPIService $triviaApiService,
        ContentPoolService $contentPool
    ) {
        $this->groqService = $groqService;
        $this->questionPoolService = $questionPoolService;
        $this->triviaApiService = $triviaApiService;
        $this->contentPool = $contentPool;
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
     *
     * Generation is triggered when EITHER condition is met:
     * 1. Active tasks below threshold (min_task_templates_threshold)
     * 2. Enough time has passed since last generation (ai_generation_frequency_hours)
     */
    public function shouldGenerateTasks()
    {
        $settings = $this->getSettings();

        if (!$settings->ai_task_generation_enabled) {
            Log::info('AI task generation is disabled');
            return false;
        }

        // Check 1: Task count threshold
        $activeTaskCount = TaskTemplate::active()->available()->count();
        $threshold = $settings->min_task_templates_threshold ?? 50;

        if ($activeTaskCount < $threshold) {
            Log::info("Task generation needed: {$activeTaskCount} tasks < {$threshold} threshold");
            return true;
        }

        // Check 2: Time since last generation
        $frequencyHours = $settings->ai_generation_frequency_hours ?? 24;
        $lastGeneration = TaskTemplate::orderBy('created_at', 'DESC')->first();

        if (!$lastGeneration) {
            Log::info('Task generation needed: No tasks exist yet');
            return true;
        }

        $hoursSinceLastGeneration = now()->diffInHours($lastGeneration->created_at);

        if ($hoursSinceLastGeneration >= $frequencyHours) {
            Log::info("Task generation needed: {$hoursSinceLastGeneration} hours since last generation (threshold: {$frequencyHours}h)");
            return true;
        }

        Log::info("Task generation not needed: {$activeTaskCount} tasks >= {$threshold} threshold AND only {$hoursSinceLastGeneration}h since last generation (need {$frequencyHours}h)");
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
     * Generate survey tasks from content pool (INSTANT - no API calls!)
     */
    public function generateSurveys($country, $count)
    {
        $generated = 0;

        // Get reward range from settings
        $settings = $this->getSettings();
        $rewardRange = $settings->ai_configuration['reward_ranges']['survey'] ?? ['min' => 30, 'max' => 100];

        // POOL-BASED: Pull from content pool (instant, no API calls)
        $surveys = $this->contentPool->getFromPool('SURVEY', $count);

        if (empty($surveys)) {
            Log::warning("Content pool is empty for SURVEY. Run 'content:fill-pool' command.");
            return 0;
        }

        Log::info("Pulled {$surveys->count()} surveys from content pool");

        // Create task templates from pool
        foreach ($surveys as $poolItem) {
            // Content is already an array (model cast)
            $surveyData = $poolItem->content;

            if (!isset($surveyData['questions'])) {
                Log::warning("Invalid survey data in pool", ['id' => $poolItem->id]);
                continue;
            }

            // Create task template
            $task = TaskTemplate::create([
                'category' => 'SURVEY',
                'title' => $poolItem->title,
                'description' => $surveyData['description'] ?? 'Complete this survey to earn rewards',
                'questions' => $surveyData['questions'],
                'reward_amount' => rand($rewardRange['min'], $rewardRange['max']),
                'completion_time_seconds' => count($surveyData['questions']) * 20, // ~20 seconds per question
                'is_active' => true,
                'priority' => rand(1, 10),
                'max_completions' => rand(20, 150),
                'min_rank_id' => null,
                'available_from' => now(),
                'available_until' => now()->addMonths(3),
            ]);

            // Mark as used in pool
            $poolItem->increment('times_used');
            $poolItem->update(['last_used_at' => now()]);

            $generated++;
            Log::info("Generated survey task from pool", ['id' => $task->id, 'title' => $task->title, 'pool_id' => $poolItem->id]);
        }

        return $generated;
    }

    /**
     * Generate video watch tasks from content pool (INSTANT - no API calls!)
     */
    public function generateVideoTasks($countryCode, $count)
    {
        $generated = 0;

        // POOL-BASED: Pull from content pool (instant, no API calls)
        $videos = $this->contentPool->getFromPool('VIDEO', $count);

        if ($videos->isEmpty()) {
            Log::warning("Content pool is empty for VIDEO. Run 'content:fill-pool' command.");
            return 0;
        }

        Log::info("Pulled {$videos->count()} videos from content pool");

        $settings = $this->getSettings();
        $rewardRange = $settings->ai_configuration['reward_ranges']['video'] ?? ['min' => 150, 'max' => 400];

        foreach ($videos as $poolItem) {
            // Content is already an array (model cast)
            $videoData = $poolItem->content;

            if (!isset($videoData['video_url'], $videoData['questions'])) {
                Log::warning("Invalid video data in pool", ['id' => $poolItem->id]);
                continue;
            }

            $task = TaskTemplate::create([
                'category' => 'VIDEO',
                'title' => $poolItem->title,
                'description' => $videoData['description'] ?? 'Watch this video and answer questions to verify',
                'video_url' => $videoData['video_url'],
                'video_duration_seconds' => $videoData['video_duration'] ?? 300,
                'questions' => $videoData['questions'],
                'reward_amount' => rand($rewardRange['min'], $rewardRange['max']),
                'completion_time_seconds' => ($videoData['video_duration'] ?? 300) + 60,
                'is_active' => true,
                'priority' => rand(1, 10),
                'max_completions' => rand(20, 150),
                'min_rank_id' => null,
                'available_from' => now(),
                'available_until' => now()->addMonths(2),
            ]);

            // Mark as used in pool
            $poolItem->increment('times_used');
            $poolItem->update(['last_used_at' => now()]);

            $generated++;
            Log::info("Generated video task from pool", ['id' => $task->id, 'pool_id' => $poolItem->id]);
        }

        return $generated;
    }

    /**
     * Generate sync tasks (template-based with rotation for scalability)
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
            ['title' => 'Mobile Data Usage Report', 'description' => 'Share your mobile data consumption patterns'],
            ['title' => 'WiFi Connection Quality Check', 'description' => 'Analyze your WiFi connection stability'],
            ['title' => 'Device Storage Analysis', 'description' => 'Share device storage usage insights'],
            ['title' => 'Browser Cache Data Sync', 'description' => 'Sync browser performance metrics'],
            ['title' => 'Location Sharing Insights', 'description' => 'Share location data for service improvement'],
            ['title' => 'Call Log Analytics', 'description' => 'Analyze call patterns for research'],
            ['title' => 'SMS Usage Patterns', 'description' => 'Share SMS usage statistics'],
            ['title' => 'Bluetooth Device Scan', 'description' => 'Scan and share connected Bluetooth devices'],
            ['title' => 'Device Sensor Data Sync', 'description' => 'Share device sensor readings'],
            ['title' => 'Notification History Analysis', 'description' => 'Analyze your notification patterns'],
            ['title' => 'RAM Usage Report', 'description' => 'Share device RAM consumption data'],
            ['title' => 'CPU Performance Metrics', 'description' => 'Share CPU usage statistics'],
            ['title' => 'App Permissions Audit', 'description' => 'Audit and share app permissions data'],
            ['title' => 'Device Temperature Monitoring', 'description' => 'Share device temperature readings'],
            ['title' => 'Audio Output Quality Test', 'description' => 'Test and share audio performance data'],
        ];

        $settings = $this->getSettings();
        $syncReward = $settings->ai_configuration['reward_ranges']['sync']['fixed'] ?? 200;

        // Shuffle templates for randomness
        shuffle($syncTemplates);
        $templateIndex = 0;

        // Loop until we generate the required count
        while ($generated < $count) {
            // Get next template (loop back if exhausted)
            $template = $syncTemplates[$templateIndex % count($syncTemplates)];
            $templateIndex++;

            // DUPLICATE PREVENTION: Skip if this sync task already exists in last 30 days
            $existingSync = TaskTemplate::where('category', 'APP_SYNC')
                ->where('title', $template['title'])
                ->where('created_at', '>=', now()->subDays(30))
                ->exists();

            if ($existingSync) {
                Log::info("Skipping duplicate sync task", ['title' => $template['title']]);

                // Reshuffle if we've gone through all templates without success
                if ($templateIndex >= count($syncTemplates)) {
                    Log::warning("All sync templates are duplicates. Stopping generation.");
                    break;
                }
                continue;
            }

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
                'max_completions' => rand(20, 150),
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
     * Generate product review tasks from content pool (INSTANT - no API calls!)
     */
    public function generateProductReviews($country, $count)
    {
        $generated = 0;

        // POOL-BASED: Pull from content pool (instant, no API calls)
        $reviews = $this->contentPool->getFromPool('REVIEW', $count);

        if ($reviews->isEmpty()) {
            Log::warning("Content pool is empty for REVIEW. Run 'content:fill-pool' command.");
            return 0;
        }

        Log::info("Pulled {$reviews->count()} reviews from content pool");

        $settings = $this->getSettings();
        $rewardRange = $settings->ai_configuration['reward_ranges']['review'] ?? ['min' => 50, 'max' => 80];

        foreach ($reviews as $poolItem) {
            // Content is already an array (model cast)
            $reviewData = $poolItem->content;

            if (!isset($reviewData['product_name'])) {
                Log::warning("Invalid review data in pool", ['id' => $poolItem->id]);
                continue;
            }

            $task = TaskTemplate::create([
                'category' => 'PRODUCT_REVIEW',
                'title' => $poolItem->title,
                'description' => $reviewData['description'] ?? "Share your honest review of {$reviewData['product_name']}",
                'product_name' => $reviewData['product_name'],
                'product_category' => $reviewData['product_category'] ?? 'General',
                'review_min_characters' => $reviewData['review_min_characters'] ?? 20,
                'reward_amount' => rand($rewardRange['min'], $rewardRange['max']),
                'completion_time_seconds' => 120,
                'is_active' => true,
                'priority' => rand(1, 10),
                'max_completions' => rand(20, 150),
                'min_rank_id' => null,
                'available_from' => now(),
                'available_until' => now()->addMonths(3),
            ]);

            // Mark as used in pool
            $poolItem->increment('times_used');
            $poolItem->update(['last_used_at' => now()]);

            $generated++;
            Log::info("Generated review task from pool", ['id' => $task->id, 'pool_id' => $poolItem->id]);
        }

        return $generated;
    }

    /**
     * Get static product pool as fallback when Groq fails
     */
    protected function getStaticProductPool($country, $count)
    {
        $productPool = [
            ['name' => 'MTN Nigeria', 'category' => 'Telecommunications'],
            ['name' => 'Glo Mobile', 'category' => 'Telecommunications'],
            ['name' => 'Airtel Nigeria', 'category' => 'Telecommunications'],
            ['name' => '9Mobile', 'category' => 'Telecommunications'],
            ['name' => 'OPay', 'category' => 'FinTech'],
            ['name' => 'PalmPay', 'category' => 'FinTech'],
            ['name' => 'Kuda Bank', 'category' => 'Banking'],
            ['name' => 'Moniepoint', 'category' => 'FinTech'],
            ['name' => 'Paystack', 'category' => 'Payment'],
            ['name' => 'Flutterwave', 'category' => 'Payment'],
            ['name' => 'Jumia Nigeria', 'category' => 'E-commerce'],
            ['name' => 'Konga', 'category' => 'E-commerce'],
            ['name' => 'Jiji Nigeria', 'category' => 'E-commerce'],
            ['name' => 'DStv', 'category' => 'Entertainment'],
            ['name' => 'GOtv', 'category' => 'Entertainment'],
            ['name' => 'Showmax', 'category' => 'Streaming'],
            ['name' => 'Netflix Nigeria', 'category' => 'Streaming'],
            ['name' => 'Spotify Nigeria', 'category' => 'Music'],
            ['name' => 'Boomplay', 'category' => 'Music'],
            ['name' => 'Uber Nigeria', 'category' => 'Ride-hailing'],
            ['name' => 'Bolt Nigeria', 'category' => 'Ride-hailing'],
            ['name' => 'Indomie Noodles', 'category' => 'Food'],
            ['name' => 'Dangote Cement', 'category' => 'Building Materials'],
            ['name' => 'Peak Milk', 'category' => 'Dairy'],
            ['name' => 'Milo', 'category' => 'Beverages'],
            ['name' => 'Golden Penny Flour', 'category' => 'Food'],
            ['name' => 'Maggi Seasoning', 'category' => 'Food'],
            ['name' => 'Coca-Cola Nigeria', 'category' => 'Beverages'],
            ['name' => 'Tecno Mobile', 'category' => 'Electronics'],
            ['name' => 'Infinix', 'category' => 'Electronics'],
            ['name' => 'Samsung Nigeria', 'category' => 'Electronics'],
            ['name' => 'HP Laptops Nigeria', 'category' => 'Electronics'],
            ['name' => 'LG Electronics', 'category' => 'Electronics'],
            ['name' => 'WhatsApp', 'category' => 'Social Media'],
            ['name' => 'Instagram', 'category' => 'Social Media'],
            ['name' => 'TikTok', 'category' => 'Social Media'],
            ['name' => 'Facebook', 'category' => 'Social Media'],
            ['name' => 'Twitter (X)', 'category' => 'Social Media'],
            ['name' => 'Shoprite Nigeria', 'category' => 'Retail'],
            ['name' => 'Spar Nigeria', 'category' => 'Retail'],
            ['name' => 'Mr Biggs', 'category' => 'Fast Food'],
            ['name' => 'Chicken Republic', 'category' => 'Fast Food'],
            ['name' => 'Dominos Pizza Nigeria', 'category' => 'Fast Food'],
            ['name' => 'KFC Nigeria', 'category' => 'Fast Food'],
            ['name' => 'Zenith Bank', 'category' => 'Banking'],
            ['name' => 'GTBank', 'category' => 'Banking'],
            ['name' => 'Access Bank', 'category' => 'Banking'],
            ['name' => 'First Bank Nigeria', 'category' => 'Banking'],
            ['name' => 'UBA', 'category' => 'Banking'],
            ['name' => 'Ecobank Nigeria', 'category' => 'Banking'],
        ];

        // Shuffle and take requested count
        shuffle($productPool);
        $selectedProducts = array_slice($productPool, 0, min($count, count($productPool)));

        return ['products' => $selectedProducts];
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
