<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => $this->faker->randomElement(['admin', 'statistician', 'provider']),
            'avatar' => $this->faker->imageUrl(100, 100, 'people'),
            'provider_id' => null,
            'provider_name' => null,
            'remember_token' => Str::random(10),
            'preferences' => json_encode(['theme' => 'light', 'language' => 'tr']),
        ];
    }

    public function admin(): static
    {
        return $this->state([
            'role' => 'admin',
        ]);
    }

    public function statistician(): static
    {
        return $this->state([
            'role' => 'statistician',
        ]);
    }

    public function provider(): static
    {
        return $this->state([
            'role' => 'provider',
        ]);
    }

    public function unverified(): static
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }
}
