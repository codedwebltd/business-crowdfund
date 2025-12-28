<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReferralTreeSeeder extends Seeder
{
    /**
     * Seed 50 users with deep referral tree structure
     * Parent: a0a45895-02a8-4482-89ec-dcb1df19d11d
     */
    public function run(): void
    {
        $parentUserId = 'a0a45895-02a8-4482-89ec-dcb1df19d11d';
        $parentUser = User::find($parentUserId);

        if (!$parentUser) {
            $this->command->error("Parent user not found with ID: {$parentUserId}");
            return;
        }

        $this->command->info("Starting referral tree seeding with parent: {$parentUser->full_name}");

        // We'll create a tree structure:
        // Level 1: 5 direct referrals to parent
        // Level 2: Each L1 user gets 3 referrals (15 users)
        // Level 3: Each L2 user gets 2 referrals (30 users)
        // Total: 5 + 15 + 30 = 50 users

        $createdUsers = [];
        $userCounter = 1;

        // Level 1: 5 direct referrals to parent
        $this->command->info("Creating Level 1 users (5 direct referrals)...");
        for ($i = 1; $i <= 5; $i++) {
            $user = $this->createUser($userCounter++, $parentUser->referral_code);
            $createdUsers['level1'][] = $user;
            $this->command->info("  Created: {$user->full_name} (Level 1)");
        }

        // Level 2: 3 referrals for each Level 1 user (15 users)
        $this->command->info("Creating Level 2 users (3 per Level 1 = 15 users)...");
        $createdUsers['level2'] = [];
        foreach ($createdUsers['level1'] as $level1User) {
            for ($i = 1; $i <= 3; $i++) {
                $user = $this->createUser($userCounter++, $level1User->referral_code);
                $createdUsers['level2'][] = $user;
                $this->command->info("  Created: {$user->full_name} (Level 2 under {$level1User->full_name})");
            }
        }

        // Level 3: 2 referrals for each Level 2 user (30 users)
        $this->command->info("Creating Level 3 users (2 per Level 2 = 30 users)...");
        $createdUsers['level3'] = [];
        foreach ($createdUsers['level2'] as $level2User) {
            for ($i = 1; $i <= 2; $i++) {
                $user = $this->createUser($userCounter++, $level2User->referral_code);
                $createdUsers['level3'][] = $user;
                $this->command->info("  Created: {$user->full_name} (Level 3 under {$level2User->full_name})");
            }
        }

        $this->command->info("✅ Successfully created 50 users in referral tree!");
        $this->command->info("   Level 1: 5 users");
        $this->command->info("   Level 2: 15 users");
        $this->command->info("   Level 3: 30 users");
    }

    /**
     * Create a single user with referral code
     */
    private function createUser(int $counter, string $referralCode): User
    {
        $firstName = $this->getRandomFirstName();
        $lastName = $this->getRandomLastName();
        $fullName = "{$firstName} {$lastName}";

        // Generate phone number
        $phoneNumber = '+234' . str_pad(rand(8000000000, 8999999999), 10, '0', STR_PAD_LEFT);

        // Generate email
        $email = strtolower($firstName . '.' . $lastName . $counter . '@example.com');

        // Find referrer
        $referrer = User::where('referral_code', $referralCode)->first();

        // Random status (mostly ACTIVE for tree visibility)
        $status = rand(1, 10) <= 8 ? 'ACTIVE' : 'PENDING';

        return User::create([
            'phone_number' => $phoneNumber,
            'email' => $email,
            'full_name' => $fullName,
            'date_of_birth' => now()->subYears(rand(20, 50))->subDays(rand(1, 365)),
            'password' => Hash::make('password123'),
            'referred_by_id' => $referrer?->id,
            'status' => $status,
            'phone_verified_at' => now(),
            'activation_date' => $status === 'ACTIVE' ? now()->subDays(rand(1, 90)) : null,
        ]);
    }

    /**
     * Random first names
     */
    private function getRandomFirstName(): string
    {
        $names = [
            'Adebayo', 'Chinwe', 'Oluwaseun', 'Fatima', 'Ibrahim', 'Ngozi', 'Tunde', 'Amaka',
            'Chukwuma', 'Blessing', 'Emeka', 'Ifeanyi', 'Kemi', 'Musa', 'Nneka', 'Obinna',
            'Chidinma', 'Yusuf', 'Funke', 'Chiamaka', 'Abdullahi', 'Chioma', 'Uche', 'Aisha',
            'Taiwo', 'Kehinde', 'Bukola', 'Segun', 'Tolu', 'Biodun', 'Folake', 'Kunle',
            'Damilola', 'Babatunde', 'Adeola', 'Chinedu', 'Patience', 'Victor', 'Grace', 'Daniel',
        ];
        return $names[array_rand($names)];
    }

    /**
     * Random last names
     */
    private function getRandomLastName(): string
    {
        $names = [
            'Okafor', 'Mohammed', 'Adeleke', 'Nwankwo', 'Hassan', 'Obi', 'Abdullahi', 'Eze',
            'Bello', 'Okoro', 'Adeyemi', 'Musa', 'Onyeka', 'Ibrahim', 'Chukwu', 'Aliyu',
            'Okonkwo', 'Yusuf', 'Ojo', 'Abubakar', 'Adamu', 'Nwosu', 'Sani', 'Ugwu',
            'Balogun', 'Oyedepo', 'Adebayo', 'Okeke', 'Usman', 'Okafor', 'Alabi', 'Nnamdi',
            'Garba', 'Chidi', 'Audu', 'Onwudiwe', 'Lawal', 'Nnadi', 'Suleiman', 'Chibuike',
        ];
        return $names[array_rand($names)];
    }
}
