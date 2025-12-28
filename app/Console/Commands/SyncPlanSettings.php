<?php

namespace App\Console\Commands;

use App\Models\GlobalSetting;
use App\Models\Plan;
use App\Models\Rank;
use Illuminate\Console\Command;

class SyncPlanSettings extends Command
{
    protected $signature = 'settings:sync-plans';
    protected $description = 'Sync withdrawal limits and processing times with current plan/rank IDs';

    public function handle()
    {
        $settings = GlobalSetting::first();

        if (!$settings) {
            $this->error('No global settings found. Run seeder first.');
            return 1;
        }

        $this->info('Syncing plan and rank settings with current database IDs...');

        // Get current plans
        $plans = Plan::all()->keyBy('name');
        $ranks = Rank::all()->keyBy('name');

        // Default limits by plan name
        $planLimits = [
            'basic' => ['min' => 5000, 'max' => 50000, 'per_day' => 1],
            'premium' => ['min' => 3000, 'max' => 100000, 'per_day' => 2],
            'vip' => ['min' => 1000, 'max' => 500000, 'per_day' => 5],
        ];

        $planProcessingTimes = [
            'basic' => '48-72 hours',
            'premium' => '24-48 hours',
            'vip' => '12-24 hours',
        ];

        $rankLimits = [
            'bronze' => ['min' => 5000, 'max' => 50000, 'per_day' => 1],
            'silver' => ['min' => 3000, 'max' => 100000, 'per_day' => 2],
            'gold' => ['min' => 2000, 'max' => 200000, 'per_day' => 3],
            'diamond' => ['min' => 1000, 'max' => 500000, 'per_day' => 5],
        ];

        $rankProcessingTimes = [
            'bronze' => '48-72 hours',
            'silver' => '24-48 hours',
            'gold' => '24 hours',
            'diamond' => 'instant',
        ];

        // Build new arrays with current IDs
        $newPlanLimits = [];
        $newPlanTimes = [];
        foreach ($plans as $name => $plan) {
            if (isset($planLimits[$name])) {
                $newPlanLimits[$plan->id] = $planLimits[$name];
                $newPlanTimes[$plan->id] = $planProcessingTimes[$name];
                $this->info("✓ Synced plan: {$plan->display_name} ({$plan->id})");
            }
        }

        $newRankLimits = [];
        $newRankTimes = [];
        foreach ($ranks as $name => $rank) {
            if (isset($rankLimits[$name])) {
                $newRankLimits[$rank->id] = $rankLimits[$name];
                $newRankTimes[$rank->id] = $rankProcessingTimes[$name];
                $this->info("✓ Synced rank: {$rank->name} ({$rank->id})");
            }
        }

        // Update settings
        $settings->update([
            'withdrawal_limits_by_plan' => $newPlanLimits,
            'withdrawal_processing_times_by_plan' => $newPlanTimes,
            'withdrawal_limits_by_rank' => $newRankLimits,
            'withdrawal_processing_times_by_rank' => $newRankTimes,
        ]);

        $this->info('✅ Settings synced successfully!');
        return 0;
    }
}
