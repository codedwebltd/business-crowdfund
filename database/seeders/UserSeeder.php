<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test user with plan
        $user = User::create([
            'phone_number' => '+2348012345678',
            'full_name' => 'Admin',
            'email' => 'dakingeorge58@gmail.com',
            'date_of_birth' => '1995-01-01',
            'password' => Hash::make('password123'),
            'phone_verified_at' => now(),
            'role' => 1,
            'status' => 'ACTIVE',
            'plan_id' => \App\Models\Plan::where('is_active', true)->first()?->id,
            'activation_amount' => 15000,
            'activation_date' => now(),
            'country' => 'NGA',
        ]);

        // Assign Bronze rank
        $user->assignBronzeRank();

        echo "✅ User created: {$user->full_name} ({$user->email})\n";
        echo "📱 Phone: {$user->phone_number}\n";
        echo "🔑 Password: password123\n";
        echo "🎯 Referral Code: {$user->referral_code}\n";
    }
}
