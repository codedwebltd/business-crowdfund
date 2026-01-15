<?php

namespace App\Services;

use Illuminate\Support\Str;

class QuestionPoolService
{
    /**
     * Video category keywords for detection
     */
    protected $categoryKeywords = [
        'music' => ['music', 'song', 'audio', 'album', 'artist', 'singer', 'gospel', 'live performance', 'official video', 'music video'],
        'sports' => ['football', 'soccer', 'basketball', 'match', 'game', 'fifa', 'afcon', 'premier league', 'championship', 'tournament', 'vs'],
        'movie' => ['trailer', 'movie', 'film', 'official trailer', 'teaser', 'cinema', 'blockbuster'],
        'gaming' => ['gameplay', 'game', 'simulation', 'pes', 'fifa', 'fortnite', 'minecraft', 'live stream', 'gaming'],
        'cooking' => ['recipe', 'cooking', 'food', 'kitchen', 'chef', 'how to cook', 'meal'],
        'news' => ['news', 'breaking', 'live', 'update', 'report', 'interview'],
        'comedy' => ['comedy', 'funny', 'skit', 'laugh', 'humor'],
        'education' => ['tutorial', 'how to', 'learn', 'guide', 'lesson', 'course'],
    ];

    /**
     * Question templates by category
     */
    protected $questionTemplates = [
        'music' => [
            ['question' => 'What is the title of this song?', 'type' => 'text'],
            ['question' => 'What genre of music is this?', 'type' => 'multiple_choice', 'options' => ['Gospel', 'Afrobeats', 'Hip-hop', 'Pop']],
            ['question' => 'Did you watch the entire video?', 'type' => 'boolean', 'options' => ['Yes', 'No']],
            ['question' => 'What was the main theme of the song?', 'type' => 'multiple_choice', 'options' => ['Love', 'Praise', 'Party', 'Motivation']],
            ['question' => 'Was this a live performance or studio version?', 'type' => 'multiple_choice', 'options' => ['Live Performance', 'Studio Version', 'Not Sure']],
        ],
        'sports' => [
            ['question' => 'Which teams were playing?', 'type' => 'text'],
            ['question' => 'What sport was featured in this video?', 'type' => 'multiple_choice', 'options' => ['Football', 'Basketball', 'Tennis', 'Boxing']],
            ['question' => 'Did you watch the full match highlights?', 'type' => 'boolean', 'options' => ['Yes', 'No']],
            ['question' => 'Was this a real match or simulation?', 'type' => 'multiple_choice', 'options' => ['Real Match', 'Video Game Simulation', 'Not Sure']],
            ['question' => 'What was the video about?', 'type' => 'multiple_choice', 'options' => ['Match Highlights', 'Live Game', 'Analysis', 'Training']],
        ],
        'movie' => [
            ['question' => 'What is the name of this movie?', 'type' => 'text'],
            ['question' => 'What genre is this movie?', 'type' => 'multiple_choice', 'options' => ['Action', 'Comedy', 'Drama', 'Thriller']],
            ['question' => 'Did you watch the complete trailer?', 'type' => 'boolean', 'options' => ['Yes', 'No']],
            ['question' => 'What year is this movie releasing?', 'type' => 'multiple_choice', 'options' => ['2025', '2026', '2027', 'Already Released']],
            ['question' => 'What was the main theme shown in the trailer?', 'type' => 'multiple_choice', 'options' => ['Adventure', 'Romance', 'Conflict', 'Mystery']],
        ],
        'gaming' => [
            ['question' => 'What game was being played?', 'type' => 'text'],
            ['question' => 'Was this live gameplay or recorded?', 'type' => 'multiple_choice', 'options' => ['Live Stream', 'Recorded Gameplay', 'Not Sure']],
            ['question' => 'Did you watch the entire video?', 'type' => 'boolean', 'options' => ['Yes', 'No']],
            ['question' => 'What type of game is this?', 'type' => 'multiple_choice', 'options' => ['Sports Game', 'Action Game', 'Strategy Game', 'Adventure Game']],
            ['question' => 'What was the main focus of this gameplay?', 'type' => 'multiple_choice', 'options' => ['Competition', 'Tutorial', 'Entertainment', 'Review']],
        ],
        'general' => [
            ['question' => 'Did you watch the entire video?', 'type' => 'boolean', 'options' => ['Yes', 'No']],
            ['question' => 'What was the main topic of this video?', 'type' => 'text'],
            ['question' => 'How would you rate the video quality?', 'type' => 'multiple_choice', 'options' => ['Excellent', 'Good', 'Average', 'Poor']],
            ['question' => 'What type of content is this?', 'type' => 'multiple_choice', 'options' => ['Entertainment', 'Educational', 'News', 'Advertisement']],
            ['question' => 'Would you recommend this video to others?', 'type' => 'boolean', 'options' => ['Yes', 'No']],
        ],
    ];

    /**
     * Generate questions for a YouTube video
     *
     * @param string $title Video title
     * @param string $description Video description
     * @param int $count Number of questions to generate
     * @return array
     */
    public function generateVideoQuestions($title, $description = '', $count = 5)
    {
        // Detect video category
        $category = $this->detectCategory($title, $description);

        // Get question templates for this category
        $templates = $this->questionTemplates[$category] ?? $this->questionTemplates['general'];

        // Shuffle to randomize question order
        $shuffled = collect($templates)->shuffle();

        // Take the required number of questions
        $selectedQuestions = $shuffled->take($count);

        // Enhance questions with video-specific context
        $questions = $selectedQuestions->map(function ($template, $index) use ($title, $category) {
            $options = $template['options'] ?? [];

            // Add "None of the above" option to multiple choice questions (not boolean)
            if ($template['type'] === 'multiple_choice' && !empty($options)) {
                $options[] = 'None of the above';
            }

            return [
                'id' => $index + 1,
                'question' => $template['question'],
                'type' => $template['type'],
                'options' => $options,
                'required' => true,
                'category' => $category,
            ];
        })->values()->toArray();

        return [
            'questions' => $questions,
            'video_category' => $category,
        ];
    }

    /**
     * Detect video category from title and description
     *
     * @param string $title
     * @param string $description
     * @return string
     */
    protected function detectCategory($title, $description = '')
    {
        $text = strtolower($title . ' ' . $description);

        $scores = [];

        foreach ($this->categoryKeywords as $category => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (Str::contains($text, strtolower($keyword))) {
                    $score++;
                }
            }
            $scores[$category] = $score;
        }

        // Get category with highest score
        arsort($scores);
        $topCategory = array_key_first($scores);

        // Only return category if score > 0, otherwise return general
        return $scores[$topCategory] > 0 ? $topCategory : 'general';
    }

    /**
     * Extract key information from video title
     * (Can be used for more advanced question generation in the future)
     *
     * @param string $title
     * @return array
     */
    protected function extractVideoInfo($title)
    {
        $info = [
            'has_year' => preg_match('/\b(202[0-9]|20[3-9][0-9])\b/', $title, $yearMatch),
            'year' => $yearMatch[0] ?? null,
            'has_vs' => Str::contains($title, ['vs', 'VS', 'v.s', 'versus']),
            'is_live' => Str::contains(strtolower($title), ['live', '🔴', 'streaming']),
            'is_official' => Str::contains(strtolower($title), ['official', 'trailer']),
        ];

        return $info;
    }

    /**
     * Get available categories
     *
     * @return array
     */
    public function getCategories()
    {
        return array_keys($this->categoryKeywords);
    }

    /**
     * Add custom question template
     *
     * @param string $category
     * @param array $template
     * @return void
     */
    public function addQuestionTemplate($category, $template)
    {
        if (!isset($this->questionTemplates[$category])) {
            $this->questionTemplates[$category] = [];
        }

        $this->questionTemplates[$category][] = $template;
    }
}
