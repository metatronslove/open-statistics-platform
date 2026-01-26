<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\User;
use App\Models\DataProvider;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Sistem geneli istatistikleri getir
     */
    public function getSystemStatistics()
    {
        return [
            'users' => [
                'total' => User::count(),
                'admin' => User::where('role', 'admin')->count(),
                'statistician' => User::where('role', 'statistician')->count(),
                'provider' => User::where('role', 'provider')->count(),
                'active_today' => User::whereDate('last_login_at', today())->count(),
            ],
            'datasets' => [
                'total' => Dataset::count(),
                'public' => Dataset::where('is_public', true)->count(),
                'private' => Dataset::where('is_public', false)->count(),
                'with_rules' => Dataset::whereNotNull('calculation_rule')->count(),
            ],
            'data' => [
                'total_points' => DataPoint::count(),
                'verified_points' => DataPoint::where('is_verified', true)->count(),
                'pending_points' => DataPoint::where('is_verified', false)->count(),
                'today_points' => DataPoint::whereDate('created_at', today())->count(),
            ],
            'providers' => [
                'total' => DataProvider::count(),
                'verified' => DataProvider::where('is_verified', true)->count(),
                'avg_trust_score' => DataProvider::avg('trust_score'),
            ],
        ];
    }

    /**
     * Dataset için istatistikleri getir
     */
    public function getDatasetStatistics($datasetId)
    {
        $dataset = Dataset::withCount(['dataPoints', 'validationLogs'])->findOrFail($datasetId);
        
        $pointsByDate = DataPoint::where('dataset_id', $datasetId)
            ->select(DB::raw('DATE(date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
            
        $pointsByProvider = DataPoint::where('dataset_id', $datasetId)
            ->join('data_providers', 'data_points.data_provider_id', '=', 'data_providers.id')
            ->select('data_providers.organization_name', DB::raw('COUNT(*) as count'))
            ->groupBy('data_providers.organization_name')
            ->orderBy('count', 'desc')
            ->get();
            
        $verificationRate = $dataset->data_points_count > 0 
            ? ($dataset->dataPoints()->where('is_verified', true)->count() / $dataset->data_points_count) * 100
            : 0;
            
        return [
            'dataset' => $dataset,
            'points_by_date' => $pointsByDate,
            'points_by_provider' => $pointsByProvider,
            'verification_rate' => round($verificationRate, 2),
            'date_range' => [
                'first' => $dataset->dataPoints()->min('date'),
                'last' => $dataset->dataPoints()->max('date'),
            ],
        ];
    }

    /**
     * Provider için istatistikleri getir
     */
    public function getProviderStatistics($providerId)
    {
        $provider = DataProvider::withCount('dataPoints')->findOrFail($providerId);
        
        $pointsByDataset = DataPoint::where('data_provider_id', $providerId)
            ->join('datasets', 'data_points.dataset_id', '=', 'datasets.id')
            ->select('datasets.name', DB::raw('COUNT(*) as count'))
            ->groupBy('datasets.name')
            ->orderBy('count', 'desc')
            ->get();
            
        $pointsByMonth = DataPoint::where('data_provider_id', $providerId)
            ->select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
            
        $verificationStats = [
            'total' => $provider->data_points_count,
            'verified' => $provider->dataPoints()->where('is_verified', true)->count(),
            'pending' => $provider->dataPoints()->where('is_verified', false)->count(),
            'rate' => $provider->data_points_count > 0 
                ? ($provider->dataPoints()->where('is_verified', true)->count() / $provider->data_points_count) * 100
                : 0,
        ];
        
        return [
            'provider' => $provider,
            'points_by_dataset' => $pointsByDataset,
            'points_by_month' => $pointsByMonth,
            'verification_stats' => $verificationStats,
        ];
    }

    /**
     * Trend analizi yap
     */
    public function analyzeTrend($datasetId, $days = 30)
    {
        $dataPoints = DataPoint::where('dataset_id', $datasetId)
            ->where('is_verified', true)
            ->where('date', '>=', now()->subDays($days))
            ->orderBy('date', 'asc')
            ->get();
            
        if ($dataPoints->count() < 2) {
            return null;
        }
        
        $values = $dataPoints->pluck('verified_value')->toArray();
        $dates = $dataPoints->pluck('date')->toArray();
        
        // Basit lineer regresyon
        $n = count($values);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
        
        foreach ($values as $i => $value) {
            $x = $i; // Zaman indeksi
            $y = $value;
            
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        // Trend yönü
        $trend = $slope > 0.01 ? 'up' : ($slope < -0.01 ? 'down' : 'stable');
        
        // Volatilite (standart sapma)
        $mean = array_sum($values) / $n;
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        $volatility = sqrt($variance / $n);
        
        return [
            'trend' => $trend,
            'slope' => $slope,
            'volatility' => $volatility,
            'mean' => $mean,
            'min' => min($values),
            'max' => max($values),
            'data_points' => $n,
            'period' => $days,
        ];
    }
}
