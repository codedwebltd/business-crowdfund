<?php

namespace Database\Seeders;

use App\Models\TaskTemplate;
use Illuminate\Database\Seeder;

class TaskTemplateSeeder extends Seeder
{
    /**
     * Seed task templates - AI Training & Other tasks from map.md
     */
    public function run(): void
    {
        $tasks = [
            // Task 1: AI Rating (AI Training)
            [
                'title' => 'Rate AI Assistant Responses',
                'description' => 'Help train AI models by rating the quality and accuracy of AI-generated responses.',
                'category' => 'AI_RATING',
                'reward_amount' => 250.00,
                'completion_time_seconds' => 120,
                'questions' => [
                    [
                        'id' => 1,
                        'text' => 'Read this AI-generated response: "The capital of Nigeria is Lagos." Is this response accurate?',
                        'type' => 'single_choice',
                        'options' => ['Accurate', 'Inaccurate', 'Partially Accurate'],
                        'required' => true,
                    ],
                    [
                        'id' => 2,
                        'text' => 'How would you rate the helpfulness of this AI response?',
                        'type' => 'single_choice',
                        'options' => ['Very Helpful', 'Helpful', 'Neutral', 'Not Helpful'],
                        'required' => true,
                    ],
                    [
                        'id' => 3,
                        'text' => 'Does the AI response sound natural and human-like?',
                        'type' => 'single_choice',
                        'options' => ['Yes', 'No', 'Somewhat'],
                        'required' => true,
                    ],
                ],
                'validation_rules' => [
                    'minimum_time' => 60,
                    'maximum_time' => 600,
                    'all_questions_required' => true,
                ],
                'is_active' => true,
                'priority' => 10,
                'min_rank_id' => null,
                'max_completions' => null,
                'current_completions' => 0,
            ],

            // Task 2: Text Moderation (AI Training)
            [
                'title' => 'Content Moderation - Flag Inappropriate Text',
                'description' => 'Help AI learn what content is appropriate by reviewing and flagging text samples.',
                'category' => 'TEXT_MODERATION',
                'reward_amount' => 300.00,
                'completion_time_seconds' => 90,
                'questions' => [
                    [
                        'id' => 1,
                        'text' => 'Review this comment: "Great product! Highly recommend to everyone." Does it violate community guidelines?',
                        'type' => 'single_choice',
                        'options' => ['Safe', 'Spam', 'Offensive', 'Misleading'],
                        'required' => true,
                    ],
                    [
                        'id' => 2,
                        'text' => 'What is the overall tone of this text?',
                        'type' => 'single_choice',
                        'options' => ['Positive', 'Neutral', 'Negative', 'Aggressive'],
                        'required' => true,
                    ],
                    [
                        'id' => 3,
                        'text' => 'Would you allow this content on a family-friendly platform?',
                        'type' => 'single_choice',
                        'options' => ['Yes', 'No', 'With Warning'],
                        'required' => true,
                    ],
                ],
                'validation_rules' => [
                    'minimum_time' => 45,
                    'maximum_time' => 600,
                    'all_questions_required' => true,
                ],
                'is_active' => true,
                'priority' => 9,
                'min_rank_id' => null,
                'max_completions' => null,
                'current_completions' => 0,
            ],

            // Task 3: Video Watch Task
            [
                'title' => 'Watch: New Product Launch Video',
                'description' => 'Watch a short video about an exciting new product and answer questions to verify you watched it.',
                'category' => 'VIDEO',
                'reward_amount' => 400.00,
                'completion_time_seconds' => 180,
                'video_url' => 'https://youtu.be/8ngiF5JSQJk', // Sample YouTube video
                'video_duration_seconds' => 120,
                'questions' => [
                    [
                        'id' => 1,
                        'text' => 'What was the main product featured in the video?',
                        'type' => 'single_choice',
                        'options' => ['Smartphone', 'Laptop', 'Smartwatch', 'Headphones'],
                        'required' => true,
                    ],
                    [
                        'id' => 2,
                        'text' => 'What color was the product shown?',
                        'type' => 'single_choice',
                        'options' => ['Black', 'White', 'Silver', 'Blue'],
                        'required' => true,
                    ],
                    [
                        'id' => 3,
                        'text' => 'Would you be interested in purchasing this product?',
                        'type' => 'single_choice',
                        'options' => ['Definitely', 'Maybe', 'Not Interested'],
                        'required' => true,
                    ],
                ],
                'validation_rules' => [
                    'minimum_time' => 100,
                    'maximum_time' => 600,
                    'minimum_watch_time' => 108, // 90% of 120 seconds
                    'all_questions_required' => true,
                ],
                'is_active' => true,
                'priority' => 8,
                'min_rank_id' => null,
                'max_completions' => 500,
                'current_completions' => 0,
            ],

            // Task 4: Product Review
            [
                'title' => 'Review: Popular Nigerian E-commerce Platform',
                'description' => 'Share your honest opinion about your online shopping experience on popular Nigerian platforms.',
                'category' => 'PRODUCT_REVIEW',
                'reward_amount' => 200.00,
                'completion_time_seconds' => 150,
                'questions' => [
                    [
                        'id' => 1,
                        'text' => 'Which e-commerce platform do you use most often?',
                        'type' => 'single_choice',
                        'options' => ['Jumia', 'Konga', 'Jiji', 'Others'],
                        'required' => true,
                    ],
                    [
                        'id' => 2,
                        'text' => 'How would you rate your overall shopping experience?',
                        'type' => 'single_choice',
                        'options' => ['Excellent', 'Good', 'Fair', 'Poor'],
                        'required' => true,
                    ],
                    [
                        'id' => 3,
                        'text' => 'What do you like most about the platform?',
                        'type' => 'text',
                        'placeholder' => 'Share your thoughts (minimum 20 characters)',
                        'min_length' => 20,
                        'required' => true,
                    ],
                    [
                        'id' => 4,
                        'text' => 'Would you recommend it to friends?',
                        'type' => 'single_choice',
                        'options' => ['Yes', 'No', 'Maybe'],
                        'required' => true,
                    ],
                ],
                'validation_rules' => [
                    'minimum_time' => 60,
                    'maximum_time' => 600,
                    'all_questions_required' => true,
                    'minimum_text_length' => 20,
                ],
                'is_active' => true,
                'priority' => 7,
                'min_rank_id' => null,
                'max_completions' => null,
                'current_completions' => 0,
            ],
        ];

        foreach ($tasks as $taskData) {
            TaskTemplate::updateOrCreate(
                ['title' => $taskData['title']],
                $taskData
            );
        }

        $this->command->info('✅ 4 Task Templates created successfully');
        $this->command->info('   - 2 AI Training tasks (AI Rating, Text Moderation)');
        $this->command->info('   - 1 Video Watch task');
        $this->command->info('   - 1 Product Review task');
    }
}
