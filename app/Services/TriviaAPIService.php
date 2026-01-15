<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TriviaAPIService
{
    /**
     * Available trivia categories from Open Trivia DB
     */
    protected $categories = [
        9 => 'General Knowledge',
        17 => 'Science & Nature',
        21 => 'Sports',
        22 => 'Geography',
        23 => 'History',
        18 => 'Science: Computers',
        19 => 'Science: Mathematics',
        20 => 'Mythology',
        25 => 'Art',
        27 => 'Animals',
    ];

    /**
     * Fetch bulk questions from Trivia API and group into multiple surveys
     * EFFICIENT: One API call gets 50 questions, split into multiple surveys
     *
     * @param int $surveyCount Number of surveys to generate
     * @param int $questionsPerSurvey Questions per survey
     * @return array Array of survey data
     */
    public function generateBulkSurveys($surveyCount, $questionsPerSurvey = 10)
    {
        $surveys = [];
        $questionsNeeded = $surveyCount * $questionsPerSurvey;

        // Calculate API calls needed (50 questions per call with 90s delays between)
        // For 45 surveys: 45 × 10 = 450 questions ÷ 50 = 9 calls
        $apiCallsNeeded = ceil($questionsNeeded / 50);

        Log::info("Fetching trivia questions", [
            'surveys_needed' => $surveyCount,
            'questions_per_survey' => $questionsPerSurvey,
            'total_questions' => $questionsNeeded,
            'api_calls' => $apiCallsNeeded,
            'max_possible_surveys' => floor(($apiCallsNeeded * 50) / $questionsPerSurvey)
        ]);

        $allQuestions = [];

        try {
            // Make up to 2 API calls (50 questions each = 100 total)
            for ($call = 0; $call < $apiCallsNeeded; $call++) {
                // ALWAYS use category 9 (General Knowledge) - most reliable, always has 50+ questions
                $categoryId = 9;
                $categoryName = 'General Knowledge';

                // Always fetch 50 (max per call)
                $response = Http::timeout(30)->get('https://opentdb.com/api.php', [
                    'amount' => 50,
                    'category' => $categoryId,
                    'difficulty' => 'easy',
                ]);

                if (!$response->successful()) {
                    Log::error('Trivia API request failed', [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    continue;
                }

                $data = $response->json();

                if ($data['response_code'] !== 0) {
                    Log::error('Trivia API returned error code', ['code' => $data['response_code']]);
                    continue;
                }

                // Parse and store questions with their category
                foreach ($data['results'] as $item) {
                    $allQuestions[] = [
                        'item' => $item,
                        'category' => $categoryName
                    ];
                }

                Log::info("Fetched 50 questions from category: {$categoryName}");

                // 90s delay between API calls to respect rate limits
                if ($call < $apiCallsNeeded - 1) {
                    Log::info("Waiting 90s before next Trivia API call...");
                    sleep(90);
                }
            }

            // Group questions into surveys
            $questionIndex = 0;
            for ($i = 0; $i < $surveyCount; $i++) {
                $surveyQuestions = [];
                $currentCategory = null;

                for ($q = 0; $q < $questionsPerSurvey; $q++) {
                    if ($questionIndex >= count($allQuestions)) {
                        break; // No more questions available
                    }

                    $questionData = $allQuestions[$questionIndex];
                    $item = $questionData['item'];
                    $currentCategory = $questionData['category'];

                    // Decode HTML entities
                    $questionText = html_entity_decode($item['question'], ENT_QUOTES | ENT_HTML5);
                    $correctAnswer = html_entity_decode($item['correct_answer'], ENT_QUOTES | ENT_HTML5);

                    // Prepare options
                    if ($item['type'] === 'boolean') {
                        $options = ['True', 'False'];
                    } else {
                        $options = array_map(function($ans) {
                            return html_entity_decode($ans, ENT_QUOTES | ENT_HTML5);
                        }, $item['incorrect_answers']);
                        $options[] = $correctAnswer;

                        // Add "None of the above" option for variety
                        $options[] = 'None of the above';

                        shuffle($options);
                    }

                    $surveyQuestions[] = [
                        'id' => $q + 1,
                        'text' => $questionText,
                        'type' => 'single_choice',
                        'options' => $options,
                        'required' => true,
                    ];

                    $questionIndex++;
                }

                if (count($surveyQuestions) > 0) {
                    $surveys[] = [
                        'title' => $this->generateSurveyTitle($currentCategory, $i + 1), // Add unique ID
                        'description' => $this->generateSurveyDescription($currentCategory, count($surveyQuestions)),
                        'questions' => $surveyQuestions,
                        'category' => $currentCategory,
                    ];
                }
            }

            Log::info("Generated {$i} surveys from trivia questions");
            return $surveys;

        } catch (Exception $e) {
            Log::error('Error calling Trivia API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Generate unique survey title based on category
     */
    protected function generateSurveyTitle($category, $uniqueId = null)
    {
        $templates = [
            'Test Your {category} Knowledge!',
            '{category} Quiz Challenge',
            'How Well Do You Know {category}?',
            '{category} Trivia Challenge',
            'Quick {category} Survey',
            '{category} Knowledge Test',
            '{category} Facts Quiz',
            'Ultimate {category} Test',
            '{category} Brain Teaser',
            'Daily {category} Quiz',
        ];

        $template = $templates[array_rand($templates)];
        $title = str_replace('{category}', $category, $template);

        // Add unique identifier to prevent duplicates
        if ($uniqueId !== null) {
            $title .= ' #' . $uniqueId;
        }

        return $title;
    }

    /**
     * Generate survey description
     */
    protected function generateSurveyDescription($category, $questionCount)
    {
        $templates = [
            "Answer {count} questions about {category} and earn rewards!",
            "Test your knowledge with {count} {category} questions.",
            "Take this quick {count}-question survey on {category}.",
            "Complete {count} {category} questions to earn your reward.",
            "Show what you know! {count} questions on {category}.",
        ];

        $template = $templates[array_rand($templates)];
        return str_replace(['{count}', '{category}'], [$questionCount, $category], $template);
    }

    /**
     * Get list of available categories
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
