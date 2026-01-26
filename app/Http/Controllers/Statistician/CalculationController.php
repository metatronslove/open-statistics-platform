<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Services\CalculationEngine;
use Illuminate\Http\Request;

class CalculationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:statistician');
    }

    public function index()
    {
        $user = auth()->user();
        
        $datasets = Dataset::where('created_by', $user->id)
            ->whereNotNull('calculation_rule')
            ->withCount('dataPoints')
            ->get();
            
        $calculationEngine = new CalculationEngine();
        $calculations = [];
        
        foreach ($datasets as $dataset) {
            $result = $calculationEngine->calculate($dataset);
            $calculations[] = [
                'dataset' => $dataset,
                'result' => $result,
                'formula' => $dataset->calculation_rule,
            ];
        }
        
        return view('statistician.calculations.index', compact('calculations'));
    }

    public function show(Dataset $dataset)
    {
        $this->authorize('view', $dataset);
        
        $calculationEngine = new CalculationEngine();
        $result = $calculationEngine->calculate($dataset);
        
        // Hesaplama geçmişi (son 30 gün)
        $history = $this->getCalculationHistory($dataset);
        
        return view('statistician.calculations.show', compact('dataset', 'result', 'history'));
    }

    public function runAll(Request $request)
    {
        $user = auth()->user();
        
        $datasets = Dataset::where('created_by', $user->id)
            ->whereNotNull('calculation_rule')
            ->get();
            
        $calculationEngine = new CalculationEngine();
        $results = [];
        $successCount = 0;
        
        foreach ($datasets as $dataset) {
            $result = $calculationEngine->calculate($dataset);
            if ($result !== null) {
                $successCount++;
            }
            $results[$dataset->id] = $result;
        }
        
        return redirect()->route('statistician.calculations.index')
            ->with('success', "{$successCount} veri seti başarıyla hesaplandı.");
    }

    protected function getCalculationHistory($dataset)
    {
        // Son 30 günlük veri noktalarını getir
        $dataPoints = $dataset->dataPoints()
            ->verified()
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'asc')
            ->get();
            
        $history = [];
        $calculationEngine = new CalculationEngine();
        
        // Her tarih için hesaplama yap
        $dates = $dataPoints->pluck('date')->unique();
        
        foreach ($dates as $date) {
            $tempDataset = clone $dataset;
            // Bu tarihe kadar olan verilerle hesaplama yap
            $tempData = $dataPoints->where('date', '<=', $date);
            // Basit bir ortalama hesapla (gerçekte daha kompleks olabilir)
            if ($tempData->isNotEmpty()) {
                $history[] = [
                    'date' => $date,
                    'value' => $tempData->avg('verified_value'),
                    'count' => $tempData->count(),
                ];
            }
        }
        
        return $history;
    }
}
