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
            // Task 1: Survey Task (from map.md blueprint: 60% of daily tasks)
            [
                'title' => 'Social Media Usage Survey',
                'description' => 'Share your social media habits and preferences to help us understand user behavior.',
                'category' => 'SURVEY',
                'reward_amount' => 250.00,
                'completion_time_seconds' => 120,
                'questions' => [
                    [
                        'id' => 1,
                        'text' => 'Which social media platform do you use most frequently?',
                        'type' => 'single_choice',
                        'options' => ['Facebook', 'Instagram', 'TikTok', 'Twitter/X', 'WhatsApp'],
                        'required' => true,
                    ],
                    [
                        'id' => 2,
                        'text' => 'How many hours do you spend on social media daily?',
                        'type' => 'single_choice',
                        'options' => ['Less than 1hr', '1-2hrs', '3-5hrs', 'More than 5hrs'],
                        'required' => true,
                    ],
                    [
                        'id' => 3,
                        'text' => 'Would you pay for ad-free social media?',
                        'type' => 'single_choice',
                        'options' => ['Yes', 'No', 'Maybe'],
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

            // Task 2: App Sync Task (from map.md blueprint: 15% of daily tasks)
            [
                'title' => 'Daily App Usage Report',
                'description' => 'Sync your device app usage data to help us understand mobile behavior patterns.',
                'category' => 'APP_SYNC',
                'reward_amount' => 200.00,
                'completion_time_seconds' => 60,
                'questions' => [
                    [
                        'id' => 1,
                        'text' => 'What is your primary mobile device?',
                        'type' => 'single_choice',
                        'options' => ['Android', 'iPhone', 'Other Smartphone', 'Tablet'],
                        'required' => true,
                    ],
                    [
                        'id' => 2,
                        'text' => 'How many apps do you have installed on your device?',
                        'type' => 'single_choice',
                        'options' => ['Less than 20', '20-50', '50-100', 'More than 100'],
                        'required' => true,
                    ],
                    [
                        'id' => 3,
                        'text' => 'Which type of apps do you use most?',
                        'type' => 'single_choice',
                        'options' => ['Social Media', 'Gaming', 'Productivity', 'Entertainment'],
                        'required' => true,
                    ],
                ],
                'validation_rules' => [
                    'minimum_time' => 30,
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
        $this->command->info('   - 1 Survey task (SURVEY)');
        $this->command->info('   - 1 App Sync task (APP_SYNC)');
        $this->command->info('   - 1 Video Watch task (VIDEO)');
        $this->command->info('   - 1 Product Review task (PRODUCT_REVIEW)');
    }
}
