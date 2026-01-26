<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\ValidationLog;
use App\Services\CalculationEngine;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:statistician');
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        $myDatasets = Dataset::where('created_by', $user->id)
            ->withCount(['dataPoints', 'validationLogs'])
            ->latest()
            ->take(5)
            ->get();

        $pendingValidations = ValidationLog::where('status', 'pending')
            ->with('dataset')
            ->latest()
            ->take(5)
            ->get();

        $recentDataPoints = DataPoint::whereHas('dataset', function ($query) use ($user) {
                $query->where('created_by', $user->id);
            })
            ->with(['dataset', 'dataProvider'])
            ->latest()
            ->take(10)
            ->get();

        $calculationEngine = new CalculationEngine();
        $calculatedValues = [];
        
        foreach ($myDatasets as $dataset) {
            if ($dataset->calculation_rule) {
                $calculatedValues[$dataset->id] = [
                    'name' => $dataset->name,
                    'value' => $calculationEngine->calculate($dataset),
                    'unit' => $dataset->unit,
                ];
            }
        }

        return view('statistician.dashboard', compact(
            'myDatasets',
            'pendingValidations',
            'recentDataPoints',
            'calculatedValues'
        ));
    }
}
