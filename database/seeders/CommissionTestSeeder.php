<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\ReferralTree;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CommissionTestSeeder extends Seeder
{
    /**
     * Create test users with referral structure for commission testing
     *
     * Structure:
     * - Creates main test user (Gabriel/you)
     * - Creates 5 direct referrals (Level 1)
     * - Each Level 1 user refers 3 users (Level 2) = 15 users
     * - Each Level 2 user refers 2 users (Level 3) = 30 users
     * - Some Level 3 users refer 1-2 users (Level 4) = ~20 users
     * - Total: ~70 users in downline tree
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Commission Test Data Seeder...');

        DB::beginTransaction();
        try {
            // Get or create main test user (you)
            $mainUser = User::where('email', 'Angab704@gmail.com')->first();

            if (!$mainUser) {
                $this->command->error('Main user not found!');
                return;
            }

            $this->command->info("✓ Main user: {$mainUser->full_name} ({$mainUser->email})");

            // Get Bronze plan
            $bronzePlan = Plan::where('name', 'Bronze')->first();
            if (!$bronzePlan) {
                $this->command->error('Bronze plan not found!');
                return;
            }

            $totalCreated = 0;

            // Level 1: Create 5 direct referrals
            $this->command->info("\n📍 Creating Level 1 users (5 direct referrals)...");
            $level1Users = [];
            for ($i = 1; $i <= 5; $i++) {
                $user = $this->createTestUser([
                    'full_name' => "Level1 User {$i}",
                    'email' => "level1.user{$i}@test.com",
                    'phone_number' => "+2348" . str_pad($i, 9, '0', STR_PAD_LEFT),
                    'referred_by_id' => $mainUser->id,
                    'plan_id' => $bronzePlan->id,
                ]);
                $level1Users[] = $user;
                $totalCreated++;
            }
            $this->command->info("✓ Created {$totalCreated} Level 1 users");

            // Level 2: Each Level 1 user refers 3 users
            $this->command->info("\n📍 Creating Level 2 users (3 per Level 1 = 15 total)...");
            $level2Users = [];
            foreach ($level1Users as $index => $l1User) {
                for ($i = 1; $i <= 3; $i++) {
                    $user = $this->createTestUser([
                        'full_name' => "Level2 User {$index}-{$i}",
                        'email' => "level2.user{$index}.{$i}@test.com",
                        'phone_number' => "+2349" . str_pad(($index * 10 + $i), 9, '0', STR_PAD_LEFT),
                        'referred_by_id' => $l1User->id,
                        'plan_id' => $bronzePlan->id,
                    ]);
                    $level2Users[] = $user;
                    $totalCreated++;
                }
            }
            $this->command->info("✓ Created " . count($level2Users) . " Level 2 users (Total: {$totalCreated})");

            // Level 3: Each Level 2 user refers 2 users
            $this->command->info("\n📍 Creating Level 3 users (2 per Level 2 = 30 total)...");
            $level3Users = [];
            foreach ($level2Users as $index => $l2User) {
                for ($i = 1; $i <= 2; $i++) {
                    $user = $this->createTestUser([
                        'full_name' => "Level3 User {$index}-{$i}",
                        'email' => "level3.user{$index}.{$i}@test.com",
                        'phone_number' => "+2347" . str_pad(($index * 10 + $i), 9, '0', STR_PAD_LEFT),
                        'referred_by_id' => $l2User->id,
                        'plan_id' => $bronzePlan->id,
                    ]);
                    $level3Users[] = $user;
                    $totalCreated++;
                }
            }
            $this->command->info("✓ Created " . count($level3Users) . " Level 3 users (Total: {$totalCreated})");

            // Level 4: Some Level 3 users refer 1-2 users
            $this->command->info("\n📍 Creating Level 4 users (1-2 per some Level 3 = ~20 total)...");
            $level4Count = 0;
            foreach (array_slice($level3Users, 0, 15) as $index => $l3User) {
                $referralCount = rand(1, 2);
                for ($i = 1; $i <= $referralCount; $i++) {
                    $user = $this->createTestUser([
                        'full_name' => "Level4 User {$index}-{$i}",
                        'email' => "level4.user{$index}.{$i}@test.com",
                        'phone_number' => "+2346" . str_pad(($index * 10 + $i), 9, '0', STR_PAD_LEFT),
                        'referred_by_id' => $l3User->id,
                        'plan_id' => $bronzePlan->id,
                    ]);
                    $level4Count++;
                    $totalCreated++;
                }
            }
            $this->command->info("✓ Created {$level4Count} Level 4 users (Total: {$totalCreated})");

            // Level 5: A few Level 4 users refer 1 user
            $this->command->info("\n📍 Creating Level 5 users (1 per some Level 4 = ~10 total)...");
            $level5Count = 0;
            $level4Users = User::where('email', 'LIKE', 'level4.%')->get();
            foreach (array_slice($level4Users->toArray(), 0, 10) as $index => $l4User) {
                $user = $this->createTestUser([
                    'full_name' => "Level5 User {$index}",
                    'email' => "level5.user{$index}@test.com",
                    'phone_number' => "+2345" . str_pad($index, 9, '0', STR_PAD_LEFT),
                    'referred_by_id' => $l4User['id'],
                    'plan_id' => $bronzePlan->id,
                ]);
                $level5Count++;
                $totalCreated++;
            }
            $this->command->info("✓ Created {$level5Count} Level 5 users (Total: {$totalCreated})");

            DB::commit();

            $this->command->info("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->command->info("✅ Successfully created {$totalCreated} test users!");
            $this->command->info("📊 Referral Structure:");
            $this->command->info("   • Main User: {$mainUser->full_name}");
            $this->command->info("   • Level 1: 5 users");
            $this->command->info("   • Level 2: 15 users");
            $this->command->info("   • Level 3: 30 users");
            $this->command->info("   • Level 4: {$level4Count} users");
            $this->command->info("   • Level 5: {$level5Count} users");
            $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Seeding failed: {$e->getMessage()}");
            $this->command->error($e->getTraceAsString());
        }
    }

    private function createTestUser(array $data): User
    {
        // Check if user already exists
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            return $existingUser;
        }

        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'password' => Hash::make('password123'),
            'referred_by_id' => $data['referred_by_id'] ?? null,
            'plan_id' => $data['plan_id'],
            'status' => 'ACTIVE',
            'phone_verified_at' => now(),
            'activation_date' => now(),
            'activation_amount' => 5000,
            'date_of_birth' => now()->subYears(25),
        ]);

        // Create wallet only if doesn't exist
        if (!Wallet::where('user_id', $user->id)->exists()) {
            Wallet::create([
                'user_id' => $user->id,
                'pending_balance' => 0,
                'withdrawable_balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
                'currency' => 'NGN',
            ]);
        }

        // Assign Bronze rank
        $user->assignBronzeRank();

        return $user;
    }
}
