<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlansSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'basic',
                'display_name' => 'Basic',
                'description' => 'Perfect for beginners to start earning',
                'price' => 5000,
                'features' => [
                    'max_daily_tasks' => 8,
                    'daily_earning_potential' => 800,
                    'referral_bonus_percentage' => 10,
                    'task_reward_multiplier' => 1.0,
                    'priority_support' => false,
                    'feature_list' => [
                        '8 tasks per day',
                        '₦800 daily earning potential',
                        '10% referral bonus',
                        'Email support',
                        'Basic analytics',
                    ],
                ],
                'is_active' => true,
                'is_popular' => false,
                'order' => 1,
            ],
            [
                'name' => 'bronze',
                'display_name' => 'Bronze',
                'description' => 'Level up your earnings with more tasks',
                'price' => 12000,
                'features' => [
                    'max_daily_tasks' => 15,
                    'daily_earning_potential' => 1500,
                    'referral_bonus_percentage' => 12,
                    'task_reward_multiplier' => 1.1,
                    'priority_support' => true,
                    'feature_list' => [
                        '15 tasks per day',
                        '₦1,500 daily earning potential',
                        '12% referral bonus',
                        'Priority email support',
                        'Advanced analytics',
                        'Faster withdrawals',
                    ],
                ],
                'is_active' => true,
                'is_popular' => true,
                'order' => 2,
            ],
            [
                'name' => 'silver',
                'display_name' => 'Silver',
                'description' => 'Maximize your income with premium features',
                'price' => 25000,
                'features' => [
                    'max_daily_tasks' => 25,
                    'daily_earning_potential' => 2500,
                    'referral_bonus_percentage' => 15,
                    'task_reward_multiplier' => 1.2,
                    'priority_support' => true,
                    'feature_list' => [
                        '25 tasks per day',
                        '₦2,500 daily earning potential',
                        '15% referral bonus',
                        '24/7 priority support',
                        'Premium analytics & insights',
                        'Instant withdrawals',
                        'Exclusive high-paying tasks',
                    ],
                ],
                'is_active' => true,
                'is_popular' => false,
                'order' => 3,
            ],
            [
                'name' => 'gold',
                'display_name' => 'Gold',
                'description' => 'For serious earners looking to scale',
                'price' => 50000,
                'features' => [
                    'max_daily_tasks' => 40,
                    'daily_earning_potential' => 4000,
                    'referral_bonus_percentage' => 18,
                    'task_reward_multiplier' => 1.3,
                    'priority_support' => true,
                    'feature_list' => [
                        '40 tasks per day',
                        '₦4,000 daily earning potential',
                        '18% referral bonus',
                        'Dedicated account manager',
                        'VIP analytics dashboard',
                        'Instant withdrawals',
                        'Premium task priority',
                        'Monthly performance bonuses',
                    ],
                ],
                'is_active' => true,
                'is_popular' => false,
                'order' => 4,
            ],
            [
                'name' => 'platinum',
                'display_name' => 'Platinum',
                'description' => 'Ultimate earning power for professionals',
                'price' => 100000,
                'features' => [
                    'max_daily_tasks' => 60,
                    'daily_earning_potential' => 6000,
                    'referral_bonus_percentage' => 20,
                    'task_reward_multiplier' => 1.5,
                    'priority_support' => true,
                    'feature_list' => [
                        '60 tasks per day',
                        '₦6,000 daily earning potential',
                        '20% referral bonus',
                        'Personal success coach',
                        'Executive analytics suite',
                        'Unlimited instant withdrawals',
                        'VIP task access',
                        'Quarterly profit sharing',
                        'Exclusive networking events',
                    ],
                ],
                'is_active' => true,
                'is_popular' => false,
                'order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
