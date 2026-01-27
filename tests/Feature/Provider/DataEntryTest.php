<?php

namespace Tests\Feature\Provider;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_view_data_entry_index()
    {
        $provider = $this->createProviderUser();
        
        // Create some data points for this provider
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        DataPoint::factory()->count(3)->create(['data_provider_id' => $dataProvider->id]);
        
        $response = $this->actingAs($provider)->get(route('provider.data-entry.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Veri Girişlerim');
        $response->assertSee('Yeni Veri Ekle');
    }

    public function test_provider_cannot_view_data_entry_without_profile()
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'provider',
            'email' => 'noprofile@test.com',
        ]);
        
        $response = $this->actingAs($user)->get(route('provider.data-entry.index'));
        
        $response->assertRedirect(route('provider.profile'));
        $response->assertSessionHas('warning');
    }

    public function test_provider_can_create_data_point()
    {
        $provider = $this->createProviderUser();
        $dataset = Dataset::factory()->create(['is_public' => true]);
        
        $response = $this->actingAs($provider)->get(route('provider.data-entry.create'));
        $response->assertStatus(200);
        $response->assertSee('Yeni Veri Ekle');
        
        // Submit form
        $response = $this->actingAs($provider)->post(route('provider.data-entry.store'), [
            'dataset_id' => $dataset->id,
            'date' => date('Y-m-d'),
            'value' => 123.45,
            'source_url' => 'https://example.com',
            'notes' => 'Test data point',
        ]);
        
        $response->assertRedirect(route('provider.data-entry.index'));
        $response->assertSessionHas('success');
        
        // Check if data point was created
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        $this->assertDatabaseHas('data_points', [
            'dataset_id' => $dataset->id,
            'data_provider_id' => $dataProvider->id,
            'value' => 123.45,
        ]);
    }

    public function test_provider_cannot_create_duplicate_data_point()
    {
        $provider = $this->createProviderUser();
        $dataset = Dataset::factory()->create(['is_public' => true]);
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        
        // Create first data point
        DataPoint::factory()->create([
            'dataset_id' => $dataset->id,
            'data_provider_id' => $dataProvider->id,
            'date' => '2024-01-01',
        ]);
        
        // Try to create duplicate
        $response = $this->actingAs($provider)->post(route('provider.data-entry.store'), [
            'dataset_id' => $dataset->id,
            'date' => '2024-01-01',
            'value' => 999.99,
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
        
        // Should have only one data point
        $count = DataPoint::where('dataset_id', $dataset->id)
            ->where('data_provider_id', $dataProvider->id)
            ->whereDate('date', '2024-01-01')
            ->count();
        
        $this->assertEquals(1, $count);
    }

    public function test_provider_can_update_own_data_point()
    {
        $provider = $this->createProviderUser();
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        
        $dataPoint = DataPoint::factory()->create([
            'data_provider_id' => $dataProvider->id,
        ]);
        
        $response = $this->actingAs($provider)->get(route('provider.data-entry.edit', $dataPoint));
        $response->assertStatus(200);
        
        // Update data point
        $response = $this->actingAs($provider)->put(route('provider.data-entry.update', $dataPoint), [
            'value' => 999.99,
            'source_url' => 'https://updated.com',
            'notes' => 'Updated notes',
        ]);
        
        $response->assertRedirect(route('provider.data-entry.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('data_points', [
            'id' => $dataPoint->id,
            'value' => 999.99,
            'is_verified' => false, // Should be reset after update
        ]);
    }

    public function test_provider_cannot_update_other_providers_data_point()
    {
        $provider1 = $this->createProviderUser();
        $provider2 = $this->createProviderUser(['email' => 'provider2@test.com']);
        
        $dataProvider2 = DataProvider::where('user_id', $provider2->id)->first();
        $dataPoint = DataPoint::factory()->create([
            'data_provider_id' => $dataProvider2->id,
        ]);
        
        $response = $this->actingAs($provider1)->get(route('provider.data-entry.edit', $dataPoint));
        $response->assertStatus(403);
    }

    public function test_provider_can_delete_own_data_point()
    {
        $provider = $this->createProviderUser();
        $dataProvider = DataProvider::where('user_id', $provider->id)->first();
        
        $dataPoint = DataPoint::factory()->create([
            'data_provider_id' => $dataProvider->id,
        ]);
        
        $response = $this->actingAs($provider)->delete(route('provider.data-entry.destroy', $dataPoint));
        
        $response->assertRedirect(route('provider.data-entry.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('data_points', ['id' => $dataPoint->id]);
    }

    public function test_provider_cannot_access_private_datasets()
    {
        $provider = $this->createProviderUser();
        $dataset = Dataset::factory()->create(['is_public' => false]);
        
        $response = $this->actingAs($provider)->post(route('provider.data-entry.store'), [
            'dataset_id' => $dataset->id,
            'date' => date('Y-m-d'),
            'value' => 123.45,
        ]);
        
        // Should fail because dataset is private
        $response->assertStatus(403);
    }
}
