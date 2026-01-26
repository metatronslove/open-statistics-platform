<?php

namespace Database\Factories;

use App\Models\Dataset;
use App\Models\DataProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataPointFactory extends Factory
{
    public function definition(): array
    {
        $value = $this->faker->randomFloat(4, 10, 1000);
        
        return [
            'dataset_id' => Dataset::factory(),
            'data_provider_id' => DataProvider::factory(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'value' => $value,
            'source_url' => $this->faker->boolean(30) ? $this->faker->url() : null,
            'is_verified' => $this->faker->boolean(60),
            'verified_value' => function (array $attributes) {
                return $attributes['is_verified'] ? $attributes['value'] : null;
            },
            'notes' => $this->faker->boolean(20) ? $this->faker->sentence() : null,
        ];
    }

    public function verified(): static
    {
        return $this->state(function (array $attributes) {
            $value = $attributes['value'] ?? $this->faker->randomFloat(4, 10, 1000);
            
            return [
                'is_verified' => true,
                'verified_value' => $value,
            ];
        });
    }

    public function unverified(): static
    {
        return $this->state([
            'is_verified' => false,
            'verified_value' => null,
        ]);
    }

    public function outlier(): static
    {
        return $this->state(function (array $attributes) {
            $baseValue = $attributes['value'] ?? $this->faker->randomFloat(4, 10, 1000);
            $outlierValue = $baseValue * $this->faker->randomElement([0.1, 0.5, 2, 5, 10]);
            
            return [
                'value' => $outlierValue,
                'is_verified' => false,
                'verified_value' => null,
            ];
        });
    }

    public function recent(): static
    {
        return $this->state([
            'date' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function forDate($date): static
    {
        return $this->state([
            'date' => $date,
        ]);
    }
}
