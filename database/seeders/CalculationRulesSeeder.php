<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\User;
use Illuminate\Database\Seeder;

class CalculationRulesSeeder extends Seeder
{
    public function run(): void
    {
        $statistician = User::where('email', 'statistician@ose.com')->first();
        
        if (!$statistician) {
            return;
        }

        $datasets = Dataset::where('created_by', $statistician->id)->get();
        
        foreach ($datasets as $dataset) {
            // Rastgele hesaplama kuralları ekle
            $rules = [
                'ortalama(deger)',
                'topla(deger) / sayi',
                'max(deger)',
                'min(deger)',
                '(max(deger) - min(deger)) / 2',
                'ortalama(deger) * 1.18',
            ];
            
            $dataset->update([
                'calculation_rule' => $this->faker->randomElement($rules),
            ]);
        }
    }
}
