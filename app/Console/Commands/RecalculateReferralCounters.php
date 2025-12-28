<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ReferralTree;
use Illuminate\Console\Command;

class RecalculateReferralCounters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:recalculate {--user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate referral counters (direct_referrals_count and total_team_size) for all users or specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user_id');

        if ($userId) {
            $this->recalculateForUser($userId);
        } else {
            $this->recalculateForAllUsers();
        }

        $this->info('✅ Referral counters recalculated successfully!');
    }

    /**
     * Recalculate for a specific user
     */
    private function recalculateForUser(string $userId): void
    {
        $user = User::find($userId);

        if (!$user) {
            $this->error("User not found: {$userId}");
            return;
        }

        $this->updateCountersForUser($user);
        $this->info("Updated counters for: {$user->full_name}");
    }

    /**
     * Recalculate for all users
     */
    private function recalculateForAllUsers(): void
    {
        $this->info('Recalculating referral counters for all users...');

        $users = User::all();
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $this->updateCountersForUser($user);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Update counters for a single user
     */
    private function updateCountersForUser(User $user): void
    {
        // Calculate direct referrals count
        $directCount = User::where('referred_by_id', $user->id)->count();

        // Calculate total team size using referral tree
        $totalTeam = 0;
        $userNode = ReferralTree::where('user_id', $user->id)->first();

        if ($userNode) {
            $totalTeam = ReferralTree::where('left_boundary', '>', $userNode->left_boundary)
                                    ->where('right_boundary', '<', $userNode->right_boundary)
                                    ->count();
        }

        // Update user record
        $user->update([
            'direct_referrals_count' => $directCount,
            'total_team_size' => $totalTeam,
        ]);
    }
}
