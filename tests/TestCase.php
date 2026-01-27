<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Test veritabanını temizle
        if (config('database.default') === 'sqlite') {
            $databasePath = database_path('database.sqlite');
            if (!file_exists($databasePath)) {
                file_put_contents($databasePath, '');
            }
        }
    }
    
    /**
     * Helper: Admin kullanıcısı oluştur
     */
    protected function createAdminUser($attributes = [])
    {
        return \App\Models\User::factory()->create(array_merge([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ], $attributes));
    }
    
    /**
     * Helper: İstatistikçi kullanıcısı oluştur
     */
    protected function createStatisticianUser($attributes = [])
    {
        return \App\Models\User::factory()->create(array_merge([
            'role' => 'statistician',
            'email' => 'statistician@test.com',
        ], $attributes));
    }
    
    /**
     * Helper: Veri sağlayıcı kullanıcısı oluştur
     */
    protected function createProviderUser($attributes = [])
    {
        $user = \App\Models\User::factory()->create(array_merge([
            'role' => 'provider',
            'email' => 'provider@test.com',
        ], $attributes));
        
        \App\Models\DataProvider::factory()->create([
            'user_id' => $user->id,
            'organization_name' => 'Test Provider',
            'is_verified' => true,
        ]);
        
        return $user;
    }
    
    /**
     * Helper: Test veri seti oluştur
     */
    protected function createDataset($attributes = [])
    {
        return \App\Models\Dataset::factory()->create($attributes);
    }
    
    /**
     * Helper: Test veri noktası oluştur
     */
    protected function createDataPoint($attributes = [])
    {
        return \App\Models\DataPoint::factory()->create($attributes);
    }
}
