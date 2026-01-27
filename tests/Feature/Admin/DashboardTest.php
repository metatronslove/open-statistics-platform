<?php

namespace Tests\Feature\Admin;

use App\Models\Dataset;
use App\Models\User;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard()
    {
        $admin = $this->createAdminUser();
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Toplam Kullanıcı');
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $statistician = $this->createStatisticianUser();
        $provider = $this->createProviderUser();
        
        // Statistician cannot access
        $response = $this->actingAs($statistician)->get(route('admin.dashboard'));
        $response->assertStatus(403);
        
        // Provider cannot access
        $response = $this->actingAs($provider)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_dashboard_shows_correct_statistics()
    {
        $admin = $this->createAdminUser();
        
        // Create test data
        User::factory()->count(5)->create(['role' => 'provider']);
        User::factory()->count(2)->create(['role' => 'statistician']);
        
        Dataset::factory()->count(3)->create();
        
        DataPoint::factory()->count(10)->create();
        DataPoint::factory()->count(5)->create(['is_verified' => true]);
        
        DataProvider::factory()->count(4)->create();
        DataProvider::factory()->count(2)->create(['is_verified' => true]);
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('8'); // Total users (1 admin + 5 providers + 2 statisticians)
        $response->assertSee('3'); // Total datasets
        $response->assertSee('15'); // Total data points
        $response->assertSee('5'); // Verified data points
        $response->assertSee('6'); // Total providers (4 + 2 from factory)
        $response->assertSee('2'); // Verified providers
    }

    public function test_dashboard_shows_recent_users()
    {
        $admin = $this->createAdminUser();
        
        // Create recent users
        User::factory()->count(10)->create();
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Son Kayıt Olan Kullanıcılar');
        
        // Check if user data is shown
        $users = User::latest()->take(10)->get();
        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
    }

    public function test_dashboard_shows_recent_datasets()
    {
        $admin = $this->createAdminUser();
        
        // Create recent datasets
        Dataset::factory()->count(5)->create();
        
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Son Eklenen Veri Setleri');
        
        // Check if dataset data is shown
        $datasets = Dataset::latest()->take(5)->get();
        foreach ($datasets as $dataset) {
            $response->assertSee($dataset->name);
        }
    }
}
