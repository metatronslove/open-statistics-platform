<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class SampleDatasetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statistician = \App\Models\User::where('email', 'statistician@ose.com')->first();
        $providers = DataProvider::all();

        // Örnek veri setleri
        $datasets = [
            [
                'name' => 'Sigara Fiyatları',
                'slug' => 'sigara-fiyatlari',
                'description' => 'Çeşitli marka sigara fiyatları',
                'unit' => 'TL',
                'created_by' => $statistician->id,
                'calculation_rule' => 'ortalama(deger)',
                'is_public' => true,
            ],
            [
                'name' => 'Ekmek Fiyatları',
                'slug' => 'ekmek-fiyatlari',
                'description' => 'Somun ekmek fiyatları',
                'unit' => 'TL',
                'created_by' => $statistician->id,
                'calculation_rule' => 'topla(deger) / sayi',
                'is_public' => true,
            ],
            [
                'name' => 'Benzin Fiyatları',
                'slug' => 'benzin-fiyatlari',
                'description' => '95 oktan benzin fiyatları',
                'unit' => 'TL/L',
                'created_by' => $statistician->id,
                'calculation_rule' => '(max(deger) - min(deger)) / 2',
                'is_public' => true,
            ],
        ];

        foreach ($datasets as $datasetData) {
            $dataset = Dataset::create($datasetData);

            // Her veri seti için örnek veri noktaları oluştur
            $startDate = Carbon::now()->subDays(30);
            
            foreach ($providers as $provider) {
                $baseValue = match($dataset->slug) {
                    'sigara-fiyatlari' => 45.00,
                    'ekmek-fiyatlari' => 12.50,
                    'benzin-fiyatlari' => 38.50,
                    default => 100.00,
                };

                for ($i = 0; $i < 10; $i++) {
                    $date = $startDate->copy()->addDays($i * 3);
                    $variation = rand(-500, 500) / 1000; // -0.5 ile +0.5 arası değişim
                    $value = $baseValue + ($baseValue * $variation);

                    DataPoint::create([
                        'dataset_id' => $dataset->id,
                        'data_provider_id' => $provider->id,
                        'date' => $date,
                        'value' => $value,
                        'is_verified' => rand(0, 1),
                        'verified_value' => rand(0, 1) ? $value : null,
                    ]);
                }
            }
        }
    }
}
