<?php

namespace Database\Factories;

use App\Models\Dataset;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValidationLogFactory extends Factory
{
    public function definition(): array
    {
        $totalPoints = $this->faker->numberBetween(2, 20);
        $validPoints = $this->faker->numberBetween(1, $totalPoints);
        
        return [
            'dataset_id' => Dataset::factory(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'calculated_average' => $this->faker->randomFloat(4, 10, 1000),
            'standard_deviation' => $this->faker->randomFloat(4, 0.1, 100),
            'status' => $this->faker->randomElement(['pending', 'verified', 'failed']),
            'outliers' => function (array $attributes) use ($totalPoints, $validPoints) {
                $outlierCount = $totalPoints - $validPoints;
                if ($outlierCount > 0) {
                    $outliers = [];
                    for ($i = 0; $i < $outlierCount; $i++) {
                        $outliers[] = [
                            'id' => $this->faker->randomNumber(),
                            'value' => $this->faker->randomFloat(4, 10, 1000),
                            'provider' => $this->faker->company(),
                        ];
                    }
                    return json_encode($outliers);
                }
                return null;
            },
            'total_points' => $totalPoints,
            'valid_points' => $validPoints,
        ];
    }

    public function verified(): static
    {
        return $this->state([
            'status' => 'verified',
            'valid_points' => fn($attributes) => $attributes['total_points'] ?? $this->faker->numberBetween(2, 20),
        ]);
    }

    public function pending(): static
    {
        return $this->state([
            'status' => 'pending',
        ]);
    }

    public function failed(): static
    {
        return $this->state([
            'status' => 'failed',
            'valid_points' => 0,
        ]);
    }

    public function withOutliers(): static
    {
        return $this->state(function (array $attributes) {
            $totalPoints = $attributes['total_points'] ?? $this->faker->numberBetween(5, 20);
            $validPoints = $this->faker->numberBetween(1, $totalPoints - 1);
            
            $outlierCount = $totalPoints - $validPoints;
            $outliers = [];
            
            for ($i = 0; $i < $outlierCount; $i++) {
                $outliers[] = [
                    'id' => $this->faker->randomNumber(),
                    'value' => $this->faker->randomFloat(4, 10, 1000),
                    'provider' => $this->faker->company(),
                ];
            }
            
            return [
                'total_points' => $totalPoints,
                'valid_points' => $validPoints,
                'outliers' => json_encode($outliers),
                'status' => $validPoints > 0 ? 'verified' : 'failed',
            ];
        });
    }
}
