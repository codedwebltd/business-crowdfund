<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;

class RankSeeder extends Seeder
{
    public function run(): void
    {
        Rank::create([
            'name' => 'bronze',
            'display_name' => 'Bronze',
            'description' => 'Entry-level rank for new members',
            'order' => 1,
            'criteria' => [
                'min_direct_referrals' => 0,
                'min_team_size' => 0,
                'min_monthly_volume' => 0,
            ],
            'benefits' => [
                'withdrawal_min' => 5000,
                'withdrawal_max' => 50000,
                'withdrawals_per_day' => 1,
                'commission_multiplier' => 1.0,
                'processing_hours' => '48-72',
            ],
            'badge_color' => '#CD7F32',
        ]);

        Rank::create([
            'name' => 'silver',
            'display_name' => 'Silver',
            'description' => 'Mid-tier rank with better benefits',
            'order' => 2,
            'criteria' => [
                'min_direct_referrals' => 10,
                'min_team_size' => 50,
                'min_monthly_volume' => 0,
            ],
            'benefits' => [
                'withdrawal_min' => 3000,
                'withdrawal_max' => 100000,
                'withdrawals_per_day' => 2,
                'commission_multiplier' => 1.02,
                'processing_hours' => '24-48',
            ],
            'badge_color' => '#C0C0C0',
        ]);

        Rank::create([
            'name' => 'gold',
            'display_name' => 'Gold',
            'description' => 'Advanced rank with premium benefits',
            'order' => 3,
            'criteria' => [
                'min_direct_referrals' => 30,
                'min_team_size' => 200,
                'min_monthly_volume' => 100000,
            ],
            'benefits' => [
                'withdrawal_min' => 2000,
                'withdrawal_max' => 200000,
                'withdrawals_per_day' => 3,
                'commission_multiplier' => 1.05,
                'processing_hours' => '24',
            ],
            'badge_color' => '#FFD700',
        ]);

        Rank::create([
            'name' => 'diamond',
            'display_name' => 'Diamond',
            'description' => 'Elite rank with maximum benefits',
            'order' => 4,
            'criteria' => [
                'min_direct_referrals' => 100,
                'min_team_size' => 1000,
                'min_monthly_volume' => 500000,
            ],
            'benefits' => [
                'withdrawal_min' => 1000,
                'withdrawal_max' => 500000,
                'withdrawals_per_day' => 5,
                'commission_multiplier' => 1.10,
                'processing_hours' => 'instant',
            ],
            'badge_color' => '#B9F2FF',
        ]);
    }
}
