<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DataProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@ose.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Statistician User
        $statistician = User::create([
            'name' => 'Statistician User',
            'email' => 'statistician@ose.com',
            'password' => Hash::make('password'),
            'role' => 'statistician',
            'email_verified_at' => now(),
        ]);

        // Provider User
        $provider = User::create([
            'name' => 'Provider User',
            'email' => 'provider@ose.com',
            'password' => Hash::make('password'),
            'role' => 'provider',
            'email_verified_at' => now(),
        ]);

        DataProvider::create([
            'user_id' => $provider->id,
            'organization_name' => 'Test Veri Sağlayıcı',
            'website' => 'https://example.com',
            'description' => 'Test veri sağlayıcı açıklaması',
            'trust_score' => 85.00,
            'is_verified' => true,
        ]);

        // Additional test providers
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Test Provider $i",
                'email' => "provider$i@ose.com",
                'password' => Hash::make('password'),
                'role' => 'provider',
                'email_verified_at' => now(),
            ]);

            DataProvider::create([
                'user_id' => $user->id,
                'organization_name' => "Test Kuruluş $i",
                'trust_score' => rand(60, 95),
                'is_verified' => rand(0, 1),
            ]);
        }
    }
}
