<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Services\CalculationEngine;
use App\Services\DataVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DatasetController extends Controller
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
            ->withCount('dataPoints')
            ->latest()
            ->paginate(20);

        return view('statistician.datasets.index', compact('datasets'));
    }

    public function create()
    {
        return view('statistician.datasets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'calculation_rule' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $user = auth()->user();

        $dataset = Dataset::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'unit' => $request->unit,
            'created_by' => $user->id,
            'calculation_rule' => $request->calculation_rule,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('statistician.datasets.index')
            ->with('success', 'Veri seti başarıyla oluşturuldu.');
    }

    public function show(Dataset $dataset)
    {
        $this->authorize('view', $dataset);

        $dataPoints = $dataset->dataPoints()
            ->with('dataProvider')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $validationLogs = $dataset->validationLogs()
            ->orderBy('date', 'desc')
            ->paginate(10);

        $calculationEngine = new CalculationEngine();
        $calculatedValue = $calculationEngine->calculate($dataset);

        // Grafik için veriler
        $chartData = $this->prepareChartData($dataset);

        return view('statistician.datasets.show', compact(
            'dataset',
            'dataPoints',
            'validationLogs',
            'calculatedValue',
            'chartData'
        ));
    }

    public function edit(Dataset $dataset)
    {
        $this->authorize('update', $dataset);
        return view('statistician.datasets.edit', compact('dataset'));
    }

    public function update(Request $request, Dataset $dataset)
    {
        $this->authorize('update', $dataset);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'calculation_rule' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $dataset->update([
            'name' => $request->name,
            'description' => $request->description,
            'unit' => $request->unit,
            'calculation_rule' => $request->calculation_rule,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('statistician.datasets.index')
            ->with('success', 'Veri seti başarıyla güncellendi.');
    }

    public function destroy(Dataset $dataset)
    {
        $this->authorize('delete', $dataset);
        
        $dataset->delete();
        return redirect()->route('statistician.datasets.index')
            ->with('success', 'Veri seti başarıyla silindi.');
    }

    public function verifyData(Dataset $dataset, Request $request)
    {
        $this->authorize('update', $dataset);

        $request->validate([
            'date' => 'required|date',
        ]);

        $service = new DataVerificationService();
        $result = $service->processValidation($dataset, $request->date);

        if ($result) {
            return redirect()->back()
                ->with('success', 'Veriler başarıyla doğrulandı. Ortalama: ' . $result['average']);
        }

        return redirect()->back()
            ->with('error', 'Doğrulama için yeterli veri yok.');
    }

    protected function prepareChartData($dataset)
    {
        $verifiedData = $dataset->dataPoints()
            ->verified()
            ->select('date', 'verified_value')
            ->orderBy('date', 'asc')
            ->get();

        $labels = $verifiedData->pluck('date')->map(function ($date) {
            return $date->format('Y-m-d');
        })->toArray();

        $values = $verifiedData->pluck('verified_value')->toArray();

        return [
            'labels' => $labels,
            'values' => $values,
            'unit' => $dataset->unit,
        ];
    }
}
