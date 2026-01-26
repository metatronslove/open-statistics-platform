<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataProviderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organization_name' => $this->faker->company(),
            'website' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'trust_score' => $this->faker->randomFloat(2, 50, 100),
            'is_verified' => $this->faker->boolean(70),
        ];
    }

    public function verified(): static
    {
        return $this->state([
            'is_verified' => true,
            'trust_score' => $this->faker->randomFloat(2, 80, 100),
        ]);
    }

    public function unverified(): static
    {
        return $this->state([
            'is_verified' => false,
            'trust_score' => $this->faker->randomFloat(2, 50, 79),
        ]);
    }

    public function highTrust(): static
    {
        return $this->state([
            'trust_score' => $this->faker->randomFloat(2, 90, 100),
        ]);
    }

    public function lowTrust(): static
    {
        return $this->state([
            'trust_score' => $this->faker->randomFloat(2, 50, 69),
        ]);
    }
}
