<?php

namespace Tests\Unit\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use App\Services\CalculationEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculationEngineTest extends TestCase
{
    use RefreshDatabase;

    protected CalculationEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new CalculationEngine();
    }

    public function test_calculate_average()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'ortalama(deger)',
        ]);
        
        // Create verified data points
        DataPoint::factory()->count(5)->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 100,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(100, $result);
    }

    public function test_calculate_sum()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'topla(deger)',
        ]);
        
        DataPoint::factory()->count(3)->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 10,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(30, $result);
    }

    public function test_calculate_sum_divided_by_count()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'topla(deger) / sayi',
        ]);
        
        DataPoint::factory()->count(4)->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 40,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(40, $result); // 160 / 4 = 40
    }

    public function test_calculate_max()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'max(deger)',
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 10,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 50,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 30,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(50, $result);
    }

    public function test_calculate_min()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'min(deger)',
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 100,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 20,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 75,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(20, $result);
    }

    public function test_calculate_complex_expression()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => '(max(deger) - min(deger)) / 2',
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 10,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 30,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(10, $result); // (30 - 10) / 2 = 10
    }

    public function test_returns_null_for_invalid_rule()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'invalid_function(deger)',
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertNull($result);
    }

    public function test_returns_null_for_empty_dataset()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'ortalama(deger)',
        ]);
        
        // No data points
        $result = $this->engine->calculate($dataset);
        
        $this->assertNull($result);
    }

    public function test_returns_null_for_no_calculation_rule()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => null,
        ]);
        
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertNull($result);
    }

    public function test_only_uses_verified_data_points()
    {
        $dataset = Dataset::factory()->create([
            'calculation_rule' => 'ortalama(deger)',
        ]);
        
        // Verified data point
        DataPoint::factory()->verified()->create([
            'dataset_id' => $dataset->id,
            'verified_value' => 100,
        ]);
        
        // Unverified data point (should be ignored)
        DataPoint::factory()->unverified()->create([
            'dataset_id' => $dataset->id,
            'value' => 1000,
            'verified_value' => null,
        ]);
        
        $result = $this->engine->calculate($dataset);
        
        $this->assertEquals(100, $result);
    }
}
