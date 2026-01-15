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
     * Generate content using Groq AI with retry logic
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

        // Retry logic: 3 attempts with exponential backoff
        $maxAttempts = 3;
        $baseDelay = 3; // seconds

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                Log::info("Groq API attempt {$attempt}/{$maxAttempts}");

                $response = Http::timeout(60) // Increased from 30s to 60s for scalability
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
                            'attempt' => $attempt,
                            'prompt_length' => strlen($userPrompt),
                            'response_length' => strlen($content)
                        ]);
                        return trim($content);
                    }
                } else {
                    Log::warning('Groq API request failed', [
                        'attempt' => $attempt,
                        'status' => $response->status(),
                        'body' => substr($response->body(), 0, 200)
                    ]);
                }

                // If not successful and not last attempt, wait before retrying
                if ($attempt < $maxAttempts) {
                    $delay = $baseDelay * pow(2, $attempt - 1); // Exponential backoff: 3s, 6s
                    Log::info("Retrying in {$delay}s...");
                    sleep($delay);
                }

            } catch (Exception $e) {
                Log::warning('Error calling Groq API', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);

                // If not last attempt, wait before retrying
                if ($attempt < $maxAttempts) {
                    $delay = $baseDelay * pow(2, $attempt - 1);
                    Log::info("Retrying in {$delay}s after exception...");
                    sleep($delay);
                } else {
                    Log::error('All Groq API attempts failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        Log::error('Groq API failed after all retry attempts');
        return null;
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

CRITICAL INSTRUCTIONS:
1. Each question MUST have exactly 5 options
2. The 5th option MUST ALWAYS be \"None of the above\"
3. Questions should be relevant to {$country} (use local brands, habits, locations)
4. Mix question types: preferences, frequency, ratings

Return ONLY this exact JSON structure with no additional text:
{
  \"title\": \"Survey title (max 100 chars)\",
  \"description\": \"Brief description (max 200 chars)\",
  \"questions\": [
    {
      \"id\": 1,
      \"text\": \"Question text\",
      \"type\": \"single_choice\",
      \"options\": [\"Option 1\", \"Option 2\", \"Option 3\", \"Option 4\", \"None of the above\"]
    }
  ]
}";

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

    /**
     * Analyze and review a testimonial
     *
     * @param string $testimonial
     * @return array|null
     */
    public function reviewTestimonial($testimonial)
    {
        $settings = $this->getSettings();
        $appName = $settings->app_name ?? 'our platform';
        $maxTokens = 1500;

        $systemPrompt = "You are a STRICT AI testimonial reviewer for {$appName}. Your job is to analyze user testimonials and determine:
1. If it's GENUINELY POSITIVE with NO doubts/complaints (approve automatically)
2. If it's trash/jargon/random text (flag as trash)
3. If it's negative or complaining (flag accordingly)
4. Correct any grammar/spelling errors while keeping the original tone

CRITICAL RULES:
- ONLY approve testimonials that are CLEARLY POSITIVE with NO complaints, doubts, or negative sentiment
- ANY mention of distrust, doubt, complaints, issues, delays = MANUAL REVIEW (do NOT approve)
- Be lenient with grammar/spelling, but STRICT with sentiment
- When in doubt, send to MANUAL REVIEW (better safe than sorry)
- The platform needs feedback from complaints to improve, so flag them for manual review

Return ONLY valid JSON with your analysis.";

        $userPrompt = "Analyze this testimonial and return your assessment:

Testimonial:
\"{$testimonial}\"

Return ONLY this exact JSON structure (no markdown, no backticks):
{
  \"approved\": true/false (approve if genuinely positive, even with poor grammar),
  \"trash_testimonial\": true/false (random text, repeated words, nonsense that doesn't make sense),
  \"negative_testimonial\": true/false (speaking bad about the platform, harsh criticism),
  \"complaint_testimonial\": true/false (legitimate complaint or issue reported),
  \"corrected_message\": \"Grammar-corrected version in same tone (fix spelling/grammar but keep natural feel, NO dashes or over-formatting)\",
  \"reason\": \"Brief explanation of your decision (1-2 sentences)\"
}

Evaluation criteria:
- APPROVE if: ONLY if 100% positive, praising the platform, no doubts, no complaints, genuine good experience (even with bad grammar)
- TRASH if: Random characters, repeated nonsense, no coherent meaning, not even attempting to write a review
- NEGATIVE if: Any criticism, distrust, saying platform is bad/scam/fraud, expressing doubt about legitimacy
- COMPLAINT if: Reporting issues, delays, problems, expressing ANY concerns or doubts (these need manual review for improvement)

STRICT APPROVAL POLICY: When in doubt, DO NOT APPROVE. Only approve clearly positive testimonials. Poor English is OK, but sentiment MUST be genuinely positive with NO complaints or doubts.";

        return $this->generateJSON($systemPrompt, $userPrompt, $maxTokens);
    }
}
