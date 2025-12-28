<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'title' => '🎉 Welcome to CrowdPower!',
                'message' => 'Start earning by completing simple tasks. Your first withdrawal is just a few tasks away!',
                'type' => 'success',
                'priority' => 100,
                'is_active' => true,
                'is_dismissable' => true,
                'target_audience' => 'all',
            ],
            [
                'title' => '⚠️ SYSTEM ANNOUNCEMENT',
                'message' => 'Withdrawal processing times may be extended during high volume periods. Thank you for your patience.',
                'type' => 'warning',
                'priority' => 90,
                'is_active' => true,
                'is_dismissable' => false, // Cannot be dismissed
                'target_audience' => 'active',
            ],
            [
                'title' => '📈 Token Rate Update',
                'message' => 'Current withdrawal rate is 68%. This is a great time to cash out your earnings!',
                'type' => 'info',
                'priority' => 80,
                'is_active' => true,
                'is_dismissable' => true,
                'target_audience' => 'active',
                'link_url' => '/withdrawals',
                'link_text' => 'Withdraw Now',
            ],
            [
                'title' => '🚀 New Task Categories Available',
                'message' => 'We\'ve added AI rating and image moderation tasks with higher rewards. Check them out!',
                'type' => 'info',
                'priority' => 70,
                'is_active' => true,
                'is_dismissable' => true,
                'target_audience' => 'active',
                'link_url' => '/tasks',
                'link_text' => 'View Tasks',
            ],
            [
                'title' => '💎 Upgrade Your Rank',
                'message' => 'Refer 10 friends to unlock Silver rank and enjoy daily withdrawals with reduced minimums!',
                'type' => 'success',
                'priority' => 60,
                'is_active' => true,
                'is_dismissable' => true,
                'target_audience' => 'active',
                'link_url' => '/referrals',
                'link_text' => 'Share Link',
            ],
            [
                'title' => '⏰ Complete Your Profile',
                'message' => 'Add your bank details to enable instant withdrawals. Takes less than 2 minutes!',
                'type' => 'warning',
                'priority' => 50,
                'is_active' => true,
                'is_dismissable' => true,
                'target_audience' => 'pending',
                'link_url' => '/settings',
                'link_text' => 'Update Profile',
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
