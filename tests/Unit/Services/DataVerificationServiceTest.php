<?php

namespace Tests\Unit\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use App\Services\DataVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataVerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DataVerificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataVerificationService();
    }

    public function test_check_and_trigger_validation_with_sufficient_data()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create 2 data points for the same date
        DataPoint::factory()->count(2)->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
        ]);
        
        $result = $this->service->checkAndTriggerValidation($dataset, $date);
        
        $this->assertTrue($result);
    }

    public function test_check_and_trigger_validation_with_insufficient_data()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create only 1 data point
        DataPoint::factory()->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
        ]);
        
        $result = $this->service->checkAndTriggerValidation($dataset, $date);
        
        $this->assertFalse($result);
    }

    public function test_process_validation_with_normal_data()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create 3 data points with similar values
        $dataPoints = DataPoint::factory()->count(3)->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
            'value' => 100, // All same value
        ]);
        
        $result = $this->service->processValidation($dataset, $date);
        
        $this->assertIsArray($result);
        $this->assertEquals(100, $result['average']);
        $this->assertEquals(0, $result['std_dev']);
        $this->assertEquals(3, $result['valid_points']);
        $this->assertEquals(3, $result['total_points']);
        $this->assertEmpty($result['outliers']);
        
        // Check if data points were verified
        foreach ($dataPoints as $dataPoint) {
            $dataPoint->refresh();
            $this->assertTrue($dataPoint->is_verified);
            $this->assertEquals(100, $dataPoint->verified_value);
        }
    }

    public function test_process_validation_with_outliers()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create data points: 2 normal, 1 outlier
        DataPoint::factory()->count(2)->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
            'value' => 100,
        ]);
        
        DataPoint::factory()->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
            'value' => 1000, // This is an outlier
        ]);
        
        $result = $this->service->processValidation($dataset, $date);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result['outliers']); // One outlier detected
        $this->assertEquals(2, $result['valid_points']); // Two valid points
        $this->assertEquals(3, $result['total_points']);
        
        // Check if outlier was marked as unverified
        $outlier = DataPoint::where('value', 1000)->first();
        $this->assertFalse($outlier->is_verified);
        $this->assertNull($outlier->verified_value);
        
        // Check if normal points were verified
        $normalPoints = DataPoint::where('value', 100)->get();
        foreach ($normalPoints as $point) {
            $this->assertTrue($point->is_verified);
            $this->assertEquals(100, $point->verified_value);
        }
    }

    public function test_calculate_average()
    {
        $values = [10, 20, 30, 40, 50];
        $average = $this->invokePrivateMethod($this->service, 'calculateAverage', [$values]);
        
        $this->assertEquals(30, $average);
    }

    public function test_calculate_standard_deviation()
    {
        $values = [10, 20, 30, 40, 50];
        $stdDev = $this->invokePrivateMethod($this->service, 'calculateStandardDeviation', [$values]);
        
        // Manual calculation: sqrt(((10-30)² + (20-30)² + (30-30)² + (40-30)² + (50-30)²) / 5)
        // = sqrt((400 + 100 + 0 + 100 + 400) / 5) = sqrt(1000 / 5) = sqrt(200) = 14.1421...
        $expectedStdDev = sqrt(200);
        
        $this->assertEqualsWithDelta($expectedStdDev, $stdDev, 0.0001);
    }

    public function test_process_validation_returns_false_for_insufficient_data()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        // Create only 1 data point
        DataPoint::factory()->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
        ]);
        
        $result = $this->service->processValidation($dataset, $date);
        
        $this->assertFalse($result);
    }

    public function test_validation_log_is_created()
    {
        $dataset = Dataset::factory()->create();
        $date = now()->format('Y-m-d');
        
        DataPoint::factory()->count(3)->create([
            'dataset_id' => $dataset->id,
            'date' => $date,
            'value' => 100,
        ]);
        
        $this->service->processValidation($dataset, $date);
        
        $this->assertDatabaseHas('validation_logs', [
            'dataset_id' => $dataset->id,
            'date' => $date,
            'status' => 'verified',
            'total_points' => 3,
            'valid_points' => 3,
        ]);
    }

    /**
     * Helper method to invoke private methods
     */
    private function invokePrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($object, $parameters);
    }
}
