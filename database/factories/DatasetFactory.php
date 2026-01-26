<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DatasetFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'description' => $this->faker->paragraph(),
            'unit' => $this->faker->randomElement(['TL', 'USD', 'EUR', 'Adet', 'Litre', 'Kg', '%']),
            'created_by' => User::factory(),
            'calculation_rule' => $this->faker->randomElement([
                'ortalama(deger)',
                'topla(deger) / sayi',
                'max(deger)',
                'min(deger)',
                '(max(deger) - min(deger)) / 2',
                null
            ]),
            'is_public' => $this->faker->boolean(80),
        ];
    }

    public function public(): static
    {
        return $this->state([
            'is_public' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state([
            'is_public' => false,
        ]);
    }

    public function withRule(): static
    {
        return $this->state([
            'calculation_rule' => $this->faker->randomElement([
                'ortalama(deger)',
                'topla(deger) / sayi',
                '(max(deger) - min(deger)) / 2',
            ]),
        ]);
    }

    public function withoutRule(): static
    {
        return $this->state([
            'calculation_rule' => null,
        ]);
    }
}
