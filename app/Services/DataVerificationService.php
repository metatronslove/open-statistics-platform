<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\ValidationLog;
use App\Jobs\ProcessValidationJob;
use Illuminate\Support\Facades\DB;

class DataVerificationService
{
    public function checkAndTriggerValidation($dataset, $date)
    {
        // Aynı tarih ve veri seti için veri noktalarını say
        $dataPointsCount = DataPoint::where('dataset_id', $dataset->id)
            ->whereDate('date', $date)
            ->count();

        // Eğer 2 veya daha fazla veri noktası varsa doğrulama job'ını tetikle
        if ($dataPointsCount >= 2) {
            ProcessValidationJob::dispatch($dataset->id, $date->format('Y-m-d'));
            return true;
        }

        return false;
    }

    public function processValidation($dataset, $date)
    {
        // İlgili tarihteki tüm veri noktalarını getir
        $dataPoints = DataPoint::where('dataset_id', $dataset->id)
            ->whereDate('date', $date)
            ->get();

        if ($dataPoints->count() < 2) {
            return false;
        }

        // Değerleri array olarak al
        $values = $dataPoints->pluck('value')->toArray();

        // Ortalama ve standart sapma hesapla
        $average = $this->calculateAverage($values);
        $stdDev = $this->calculateStandardDeviation($values, $average);

        // Aykırı değerleri tespit et (ortalama ± 2*standart sapma)
        $lowerBound = $average - (2 * $stdDev);
        $upperBound = $average + (2 * $stdDev);

        $outliers = [];
        $validPoints = 0;

        foreach ($dataPoints as $dataPoint) {
            $value = $dataPoint->value;
            
            if ($value >= $lowerBound && $value <= $upperBound) {
                // Geçerli aralıkta, doğrula
                $dataPoint->update([
                    'is_verified' => true,
                    'verified_value' => $value,
                ]);
                $validPoints++;
            } else {
                // Aykırı değer
                $dataPoint->update([
                    'is_verified' => false,
                    'verified_value' => null,
                ]);
                $outliers[] = [
                    'id' => $dataPoint->id,
                    'value' => $value,
                    'provider' => $dataPoint->dataProvider->organization_name,
                ];
            }
        }

        // Doğrulama logunu kaydet
        ValidationLog::updateOrCreate(
            [
                'dataset_id' => $dataset->id,
                'date' => $date,
            ],
            [
                'calculated_average' => $average,
                'standard_deviation' => $stdDev,
                'status' => $validPoints > 0 ? 'verified' : 'failed',
                'outliers' => $outliers,
                'total_points' => $dataPoints->count(),
                'valid_points' => $validPoints,
            ]
        );

        return [
            'average' => $average,
            'std_dev' => $stdDev,
            'valid_points' => $validPoints,
            'total_points' => $dataPoints->count(),
            'outliers' => $outliers,
        ];
    }

    protected function calculateAverage(array $values)
    {
        return array_sum($values) / count($values);
    }

    protected function calculateStandardDeviation(array $values, $average = null)
    {
        if ($average === null) {
            $average = $this->calculateAverage($values);
        }

        $sumOfSquares = 0;
        foreach ($values as $value) {
            $sumOfSquares += pow($value - $average, 2);
        }

        return sqrt($sumOfSquares / count($values));
    }
}
