<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupSoftDeletes extends Command
{
    protected $signature = 'cleanup:soft-deletes';
    protected $description = 'Permanently delete soft-deleted users after 30 days (for appeal window)';

    public function handle()
    {
        $this->info('Starting cleanup of soft-deleted accounts...');

        // Find soft-deleted users older than 30 days
        $cutoffTime = Carbon::now()->subDays(30);

        $softDeletedUsers = User::onlyTrashed()
            ->where('deleted_at', '<', $cutoffTime)
            ->get();

        if ($softDeletedUsers->isEmpty()) {
            $this->info('No soft-deleted accounts found for cleanup.');
            return 0;
        }

        $deletedCount = 0;

        foreach ($softDeletedUsers as $user) {
            try {
                // Permanently delete (force delete)
                $email = $user->email;
                $user->forceDelete();

                $this->info("Permanently deleted user: {$email} (Deleted on: {$user->deleted_at->format('M d, Y')})");
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to permanently delete user {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Cleanup completed. {$deletedCount} accounts permanently deleted.");

        return 0;
    }
}
