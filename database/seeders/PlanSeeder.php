<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'basic',
                'display_name' => 'Basic Plan',
                'description' => 'Perfect for beginners! Start earning by completing daily tasks.',
                'price' => 1500.00,
                'order' => 1,
                'badge_color' => '#6b7280',
                'is_popular' => false,
                'is_active' => true,
                'features' => [
                    'max_daily_tasks' => 8,
                    'task_reward_multiplier' => 1.0,
                    'task_categories' => ['SURVEY', 'VIDEO', 'AI_RATING'],
                    'withdrawal_min' => 5000,
                    'withdrawal_max' => 50000,
                    'withdrawals_per_day' => 1,
                ],
            ],
            [
                'name' => 'premium',
                'display_name' => 'Premium Plan',
                'description' => 'Most popular! Earn 2x rewards and unlock priority support.',
                'price' => 5000.00,
                'order' => 2,
                'badge_color' => '#f59e0b',
                'is_popular' => true,
                'is_active' => true,
                'features' => [
                    'max_daily_tasks' => 15,
                    'task_reward_multiplier' => 2.0,
                    'task_categories' => ['SURVEY', 'VIDEO', 'AI_RATING', 'APP_SYNC', 'PRODUCT_REVIEW'],
                    'withdrawal_min' => 3000,
                    'withdrawal_max' => 100000,
                    'withdrawals_per_day' => 2,
                    'priority_support' => true,
                ],
            ],
            [
                'name' => 'vip',
                'display_name' => 'VIP Plan',
                'description' => 'Maximum earnings! Get 3x rewards, unlimited withdrawals, instant processing.',
                'price' => 15000.00,
                'order' => 3,
                'badge_color' => '#a855f7',
                'is_popular' => false,
                'is_active' => true,
                'features' => [
                    'max_daily_tasks' => 25,
                    'task_reward_multiplier' => 3.0,
                    'task_categories' => ['SURVEY', 'VIDEO', 'AI_RATING', 'APP_SYNC', 'PRODUCT_REVIEW', 'ADVANCED'],
                    'withdrawal_min' => 1000,
                    'withdrawal_max' => 500000,
                    'withdrawals_per_day' => 999,
                    'priority_support' => true,
                    'instant_withdrawal' => true,
                    'vip_badge' => true,
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
