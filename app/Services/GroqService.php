<?php

namespace App\Services;

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GroqService
{
    protected $settings;

    public function __construct()
    {
        // Lazy load settings only when needed
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
     * Generate content using Groq AI
     *
     * @param string $systemPrompt
     * @param string $userPrompt
     * @param int|null $maxTokens
     * @param float|null $temperature
     * @return string|null
     */
    public function generate($systemPrompt, $userPrompt, $maxTokens = null, $temperature = null)
    {
        $settings = $this->getSettings();

        // Check if AI generation is enabled
        if (!$settings->ai_task_generation_enabled) {
            Log::info('AI task generation is disabled in settings');
            return null;
        }

        $aiConfig = $settings->ai_configuration;

        if (empty($aiConfig['groq_api_key'])) {
            Log::error('Groq API key not configured');
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $aiConfig['groq_api_key'],
                    'Content-Type' => 'application/json',
                ])
                ->post($aiConfig['groq_endpoint'], [
                    'model' => $aiConfig['groq_model'] ?? 'llama-3.1-8b-instant',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt]
                    ],
                    'max_tokens' => $maxTokens ?? 2000,
                    'temperature' => $temperature ?? ($aiConfig['temperature'] ?? 0.7),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? null;

                if ($content) {
                    Log::info('Groq AI generation successful', [
                        'prompt_length' => strlen($userPrompt),
                        'response_length' => strlen($content)
                    ]);
                    return trim($content);
                }
            } else {
                Log::error('Groq API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return null;

        } catch (Exception $e) {
            Log::error('Error calling Groq API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Generate JSON output using Groq AI
     *
     * @param string $systemPrompt
     * @param string $userPrompt
     * @param int|null $maxTokens
     * @return array|null
     */
    public function generateJSON($systemPrompt, $userPrompt, $maxTokens = null)
    {
        $content = $this->generate($systemPrompt, $userPrompt, $maxTokens);

        if (!$content) {
            return null;
        }

        // Try to extract JSON from the response
        // Sometimes AI wraps JSON in markdown code blocks
        $content = preg_replace('/```json\s*(.*?)\s*```/s', '$1', $content);
        $content = preg_replace('/```\s*(.*?)\s*```/s', '$1', $content);
        $content = trim($content);

        try {
            $decoded = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            } else {
                Log::error('Failed to decode AI JSON response', [
                    'error' => json_last_error_msg(),
                    'content' => substr($content, 0, 500)
                ]);
                return null;
            }
        } catch (Exception $e) {
            Log::error('Exception decoding AI JSON', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate survey task using AI
     *
     * @param string $country
     * @param int $questionCount
     * @return array|null
     */
    public function generateSurvey($country = 'Nigeria', $questionCount = 10)
    {
        $settings = $this->getSettings();
        $maxTokens = $settings->ai_configuration['max_tokens']['survey'] ?? 2000;

        $systemPrompt = "You are a survey designer creating realistic market research surveys for {$country}. Generate surveys that are culturally relevant, engaging, and take 2-5 minutes to complete. Always return ONLY valid JSON.";

        $userPrompt = "Generate 1 survey with {$questionCount} multiple choice questions about daily life, technology usage, or consumer habits in {$country}.

Return ONLY this exact JSON structure with no additional text:
{
  \"title\": \"Survey title (max 100 chars)\",
  \"description\": \"Brief description (max 200 chars)\",
  \"questions\": [
    {
      \"id\": 1,
      \"text\": \"Question text\",
      \"type\": \"single_choice\",
      \"options\": [\"Option 1\", \"Option 2\", \"Option 3\", \"Option 4\"]
    }
  ]
}

Make questions relevant to {$country} (use local brands, habits, locations). Mix question types: preferences, frequency, ratings.";

        return $this->generateJSON($systemPrompt, $userPrompt, $maxTokens);
    }

    /**
     * Generate product review tasks using AI
     *
     * @param string $country
     * @param int $count
     * @return array|null
     */
    public function generateProductNames($country = 'Nigeria', $count = 10)
    {
        $settings = $this->getSettings();
        $maxTokens = $settings->ai_configuration['max_tokens']['product_names'] ?? 300;

        $systemPrompt = "You are a product database for {$country}. Generate realistic, popular product names that people in {$country} commonly review. Return ONLY valid JSON.";

        $userPrompt = "Generate {$count} popular product names in {$country} across categories: phones, apps, food brands, electronics, services.

Return ONLY this JSON structure:
{
  \"products\": [
    {\"name\": \"Product Name\", \"category\": \"Electronics\"},
    {\"name\": \"Product Name\", \"category\": \"Food\"}
  ]
}

Use real brands popular in {$country}.";

        return $this->generateJSON($systemPrompt, $userPrompt, $maxTokens);
    }

    /**
     * Generate verification questions for a video
     *
     * @param string $videoTitle
     * @param string $videoDescription
     * @param int $questionCount
     * @return array|null
     */
    public function generateVideoQuestions($videoTitle, $videoDescription, $questionCount = 5)
    {
        $settings = $this->getSettings();
        $maxTokens = $settings->ai_configuration['max_tokens']['video_questions'] ?? 800;

        $systemPrompt = "You are a quiz generator creating verification questions for video watchers. Create meaningful questions that test if users actually watched the video. Focus on specific details, key points, visual elements, and memorable moments. Return ONLY valid JSON.";

        $userPrompt = "Video Title: {$videoTitle}
Description: {$videoDescription}

CRITICAL INSTRUCTIONS:
1. Generate {$questionCount} multiple choice questions based on the video's content
2. Questions should be SPECIFIC and meaningful (test actual video content, not generic questions)
3. Each question MUST have exactly 4 options
4. The LAST option (4th option) MUST ALWAYS be \"None of the above\"
5. Questions should test:
   - Specific details mentioned in title/description
   - Key topics or themes
   - Named entities (people, places, products)
   - Numerical facts or dates if present

GOOD examples:
- \"What is the main topic of this video?\" (based on title/description)
- \"Who is the creator/channel mentioned?\" (from description)
- \"What product/service is being discussed?\" (from title)

BAD examples (too generic):
- \"Did you like the video?\"
- \"Was this video helpful?\"
- \"Would you recommend this?\"

Return ONLY this JSON structure (no markdown, no backticks):
{
  \"questions\": [
    {
      \"id\": 1,
      \"text\": \"Question about specific video content\",
      \"type\": \"single_choice\",
      \"options\": [\"Specific Option 1\", \"Specific Option 2\", \"Specific Option 3\", \"None of the above\"],
      \"required\": true
    }
  ]
}";

        return $this->generateJSON($systemPrompt, $userPrompt, $maxTokens);
    }
}
