<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use App\Models\User;
use App\Models\ValidationLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ValidationLog::truncate();
        DataPoint::truncate();
        Dataset::truncate();
        DataProvider::truncate();
        User::where('id', '>', 3)->delete(); // Keep default users
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get default users
        $admin = User::where('email', 'admin@ose.com')->first();
        $statistician = User::where('email', 'statistician@ose.com')->first();
        $provider = User::where('email', 'provider@ose.com')->first();
        
        if (!$admin || !$statistician || !$provider) {
            $this->call([AdminUserSeeder::class]);
            $admin = User::where('email', 'admin@ose.com')->first();
            $statistician = User::where('email', 'statistician@ose.com')->first();
            $provider = User::where('email', 'provider@ose.com')->first();
        }

        // Create additional providers
        $providers = [$provider];
        for ($i = 1; $i <= 5; $i++) {
            $newProvider = User::create([
                'name' => "Ek Sağlayıcı $i",
                'email' => "extra_provider$i@ose.com",
                'password' => bcrypt('password'),
                'role' => 'provider',
                'email_verified_at' => now(),
            ]);

            DataProvider::create([
                'user_id' => $newProvider->id,
                'organization_name' => "Ek Kuruluş $i",
                'trust_score' => rand(60, 95),
                'is_verified' => rand(0, 1),
            ]);

            $providers[] = $newProvider;
        }

        // Create sample datasets
        $datasets = [
            [
                'name' => 'Sigara Fiyatları',
                'slug' => 'sigara-fiyatlari',
                'description' => 'Çeşitli marka sigara fiyatları (paket)',
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
            [
                'name' => 'Dolar Kuru',
                'slug' => 'dolar-kuru',
                'description' => 'USD/TRY döviz kuru',
                'unit' => 'TRY',
                'created_by' => $statistician->id,
                'calculation_rule' => null,
                'is_public' => true,
            ],
            [
                'name' => 'Asgari Ücret',
                'slug' => 'asgari-ucret',
                'description' => 'Net asgari ücret',
                'unit' => 'TL',
                'created_by' => $admin->id,
                'calculation_rule' => 'ortalama(deger)',
                'is_public' => false,
            ],
        ];

        $createdDatasets = [];
        foreach ($datasets as $datasetData) {
            $dataset = Dataset::create($datasetData);
            $createdDatasets[$dataset->slug] = $dataset;
        }

        // Generate sample data points for last 30 days
        $startDate = Carbon::now()->subDays(30);
        
        foreach ($createdDatasets as $slug => $dataset) {
            $baseValue = match($slug) {
                'sigara-fiyatlari' => 45.00,
                'ekmek-fiyatlari' => 12.50,
                'benzin-fiyatlari' => 38.50,
                'dolar-kuru' => 28.50,
                'asgari-ucret' => 17002.00,
                default => 100.00,
            };

            $dailyChange = match($slug) {
                'sigara-fiyatlari' => 0.02,  // ±2%
                'ekmek-fiyatlari' => 0.01,   // ±1%
                'benzin-fiyatlari' => 0.015, // ±1.5%
                'dolar-kuru' => 0.005,       // ±0.5%
                'asgari-ucret' => 0,         // No change
                default => 0.01,
            };

            // For each provider, create data points
            foreach ($providers as $providerUser) {
                $dataProvider = DataProvider::where('user_id', $providerUser->id)->first();
                
                if (!$dataProvider) continue;

                $currentValue = $baseValue * (0.9 + (rand(0, 20) / 100)); // ±10% variation between providers

                for ($day = 0; $day < 30; $day++) {
                    $date = $startDate->copy()->addDays($day);
                    
                    // Skip some days randomly (providers don't always submit data)
                    if (rand(1, 10) > 7) continue;

                    // Add daily variation
                    $variation = (rand(-100, 100) / 100) * $dailyChange;
                    $value = $currentValue * (1 + $variation);
                    
                    // Round to appropriate decimals
                    $value = round($value, $slug === 'asgari-ucret' ? 0 : 2);

                    DataPoint::create([
                        'dataset_id' => $dataset->id,
                        'data_provider_id' => $dataProvider->id,
                        'date' => $date,
                        'value' => $value,
                        'source_url' => rand(1, 10) > 7 ? 'https://example.com/source' . rand(1, 100) : null,
                        'is_verified' => rand(1, 10) > 2, // 80% verified
                        'verified_value' => rand(1, 10) > 2 ? $value : null,
                        'notes' => rand(1, 10) > 8 ? 'Sample note for ' . $date->format('Y-m-d') : null,
                        'created_at' => $date->copy()->addHours(rand(8, 18)),
                    ]);

                    $currentValue = $value;
                }
            }
        }

        // Create some validation logs
        foreach ($createdDatasets as $dataset) {
            for ($day = 0; $day < 10; $day++) {
                $date = $startDate->copy()->addDays($day * 3);
                
                $dataPoints = DataPoint::where('dataset_id', $dataset->id)
                    ->whereDate('date', $date)
                    ->get();
                
                if ($dataPoints->count() >= 2) {
                    $values = $dataPoints->pluck('value')->toArray();
                    $average = array_sum($values) / count($values);
                    
                    // Calculate standard deviation
                    $sum = 0;
                    foreach ($values as $value) {
                        $sum += pow($value - $average, 2);
                    }
                    $stdDev = sqrt($sum / count($values));
                    
                    // Determine outliers (2 sigma rule)
                    $lowerBound = $average - (2 * $stdDev);
                    $upperBound = $average + (2 * $stdDev);
                    
                    $outliers = [];
                    $validPoints = 0;
                    
                    foreach ($dataPoints as $dataPoint) {
                        if ($dataPoint->value >= $lowerBound && $dataPoint->value <= $upperBound) {
                            $validPoints++;
                        } else {
                            $outliers[] = [
                                'id' => $dataPoint->id,
                                'value' => $dataPoint->value,
                                'provider' => $dataPoint->dataProvider->organization_name,
                            ];
                        }
                    }
                    
                    ValidationLog::create([
                        'dataset_id' => $dataset->id,
                        'date' => $date,
                        'calculated_average' => $average,
                        'standard_deviation' => $stdDev,
                        'status' => $validPoints > 0 ? 'verified' : 'failed',
                        'outliers' => !empty($outliers) ? json_encode($outliers) : null,
                        'total_points' => $dataPoints->count(),
                        'valid_points' => $validPoints,
                    ]);
                }
            }
        }

        $this->command->info('Sample data created successfully!');
        $this->command->info('Default login credentials:');
        $this->command->info('Admin: admin@ose.com / password');
        $this->command->info('Statistician: statistician@ose.com / password');
        $this->command->info('Provider: provider@ose.com / password');
    }
}
