<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Badge>
 */
class BadgeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' Badge',
            'description' => $this->faker->sentence(),
            'required_achievements' => $this->faker->numberBetween(1, 5),
            'cashback_amount' => $this->faker->randomFloat(2, 100, 1000),
            'metadata' => null,
        ];
    }
}
