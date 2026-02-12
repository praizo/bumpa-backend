<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Database\Seeder;

class LoyaltySeeder extends Seeder
{
    public function run(): void
    {
        // Create Achievements
        Achievement::firstOrCreate(
            ['name' => 'First Purchase'],
            [
                'description' => 'Make your first purchase.',
                'required_purchases' => 1,
            ]
        );

        Achievement::firstOrCreate(
            ['name' => 'Loyal Customer'],
            [
                'description' => 'Make 5 purchases.',
                'required_purchases' => 5,
            ]
        );

        Achievement::firstOrCreate(
            ['name' => 'Big Spender'],
            [
                'description' => 'Make 10 purchases.',
                'required_purchases' => 10,
            ]
        );

        // Create Badges
        Badge::firstOrCreate(
            ['name' => 'Bronze Badge'],
            [
                'description' => 'Unlock 1 achievement.',
                'required_achievements' => 1,
                'cashback_amount' => 100,
            ]
        );

        Badge::firstOrCreate(
            ['name' => 'Silver Badge'],
            [
                'description' => 'Unlock 2 achievements.',
                'required_achievements' => 2,
                'cashback_amount' => 300,
            ]
        );

        Badge::firstOrCreate(
            ['name' => 'Gold Badge'],
            [
                'description' => 'Unlock 3 achievements.',
                'required_achievements' => 3,
                'cashback_amount' => 1000,
            ]
        );
    }
}
