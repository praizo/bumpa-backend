<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Database\Seeder;

class LoyaltySeeder extends Seeder
{
    public function run(): void
    {
        // Create Achievements (Amount Based)
        Achievement::firstOrCreate([
            'name' => 'Big Spender I',
            'description' => 'Spend over 10,000 NGN.',
            'required_spend' => 10000,
        ]);

        Achievement::firstOrCreate([
            'name' => 'Big Spender II',
            'description' => 'Spend over 30,000 NGN.',
            'required_spend' => 30000,
        ]);

        // Create Badges (Achievement Count Based)
        Badge::firstOrCreate([
            'name' => 'Gold Badge',
            'description' => 'Unlock 2 achievements.',
            'required_achievements' => 2,
            'cashback_amount' => 300,
        ]);
    }
}
