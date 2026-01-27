<?php

namespace Tests\Feature\Statistician;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\ValidationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatasetTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistician_can_view_datasets_index()
    {
        $statistician = $this->createStatisticianUser();
        
        // Create datasets for this statistician
        Dataset::factory()->count(3)->create(['created_by' => $statistician->id]);
        
        $response = $this->actingAs($statistician)->get(route('statistician.datasets.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Veri Setlerim');
        $response->assertSee('Yeni Veri Seti');
    }

    public function test_statistician_can_create_dataset()
    {
        $statistician = $this->createStatisticianUser();
        
        $response = $this->actingAs($statistician)->get(route('statistician.datasets.create'));
        $response->assertStatus(200);
        $response->assertSee('Yeni Veri Seti Oluştur');
        
        // Submit form
        $response = $this->actingAs($statistician)->post(route('statistician.datasets.store'), [
            'name' => 'Test Dataset',
            'description' => 'Test dataset description',
            'unit' => 'TL',
            'calculation_rule' => 'ortalama(deger)',
            'is_public' => true,
        ]);
        
        $response->assertRedirect(route('statistician.datasets.index'));
        $response->assertSessionHas('success');
        
        // Check if dataset was created
        $this->assertDatabaseHas('datasets', [
            'name' => 'Test Dataset',
            'created_by' => $statistician->id,
        ]);
    }

    public function test_statistician_can_view_own_dataset()
    {
        $statistician = $this->createStatisticianUser();
        $dataset = Dataset::factory()->create(['created_by' => $statistician->id]);
        
        // Add some data points
        DataPoint::factory()->count(5)->create(['dataset_id' => $dataset->id]);
        ValidationLog::factory()->create(['dataset_id' => $dataset->id]);
        
        $response = $this->actingAs($statistician)->get(route('statistician.datasets.show', $dataset));
        
        $response->assertStatus(200);
        $response->assertSee($dataset->name);
        $response->assertSee('Veri Grafiği');
        $response->assertSee('Veri Noktaları');
    }

    public function test_statistician_cannot_view_other_statisticians_dataset()
    {
        $statistician1 = $this->createStatisticianUser();
        $statistician2 = $this->createStatisticianUser(['email' => 'statistician2@test.com']);
        
        $dataset = Dataset::factory()->create(['created_by' => $statistician1->id]);
        
        $response = $this->actingAs($statistician2)->get(route('statistician.datasets.show', $dataset));
        
        $response->assertStatus(403);
    }

    public function test_statistician_can_update_dataset()
    {
        $statistician = $this->createStatisticianUser();
        $dataset = Dataset::factory()->create(['created_by' => $statistician->id]);
        
        $response = $this->actingAs($statistician)->get(route('statistician.datasets.edit', $dataset));
        $response->assertStatus(200);
        
        // Update dataset
        $response = $this->actingAs($statistician)->put(route('statistician.datasets.update', $dataset), [
            'name' => 'Updated Dataset Name',
            'description' => 'Updated description',
            'unit' => 'USD',
            'calculation_rule' => 'topla(deger) / sayi',
            'is_public' => false,
        ]);
        
        $response->assertRedirect(route('statistician.datasets.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('datasets', [
            'id' => $dataset->id,
            'name' => 'Updated Dataset Name',
            'unit' => 'USD',
        ]);
    }

    public function test_statistician_can_delete_dataset()
    {
        $statistician = $this->createStatisticianUser();
        $dataset = Dataset::factory()->create(['created_by' => $statistician->id]);
        
        $response = $this->actingAs($statistician)->delete(route('statistician.datasets.destroy', $dataset));
        
        $response->assertRedirect(route('statistician.datasets.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('datasets', ['id' => $dataset->id]);
    }

    public function test_statistician_can_verify_data_manually()
    {
        $statistician = $this->createStatisticianUser();
        $dataset = Dataset::factory()->create(['created_by' => $statistician->id]);
        
        // Add data points for verification
        DataPoint::factory()->count(3)->create([
            'dataset_id' => $dataset->id,
            'date' => '2024-01-01',
        ]);
        
        $response = $this->actingAs($statistician)->post(route('statistician.datasets.verify', $dataset), [
            'date' => '2024-01-01',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Check if validation log was created
        $this->assertDatabaseHas('validation_logs', [
            'dataset_id' => $dataset->id,
            'date' => '2024-01-01',
        ]);
    }
}
