<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\PerformanceCalculationService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Usage:
     * php artisan db:seed --class=UserPerformanceSeeder
     * php artisan db:seed --class=UserPerformanceSeeder --email=angab704@gmail.com
     * php artisan db:seed --class=UserPerformanceSeeder --email=user1@test.com,user2@test.com
     */
    public function run(): void
    {
        $performanceService = new PerformanceCalculationService();

        // Get email parameter from command line
        $emailParam = $this->command->option('email') ?? 'angab704@gmail.com';

        // Split emails by comma
        $emails = array_map('trim', explode(',', $emailParam));

        $this->command->info("Seeding performance for " . count($emails) . " user(s)...");

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->command->warn("User not found: {$email}");
                continue;
            }

            $performance = $performanceService->calculateUserPerformance($user);

            $this->command->info("✓ {$user->email}: {$performance->star_rating} stars (Priority: {$performance->priority_level})");
        }

        $this->command->info("Performance seeding completed!");
    }
}
