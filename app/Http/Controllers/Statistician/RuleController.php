<?php

namespace App\Http\Controllers\Statistician;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\ValidationLog;
use App\Services\CalculationEngine;
use Illuminate\Http\Request;

class RuleController extends Controller
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
            ->latest()
            ->paginate(20);

        $calculationEngine = new CalculationEngine();
        $results = [];

        foreach ($datasets as $dataset) {
            $results[$dataset->id] = $calculationEngine->calculate($dataset);
        }

        return view('statistician.rules.index', compact('datasets', 'results'));
    }

    public function create()
    {
        $user = auth()->user();
        $datasets = Dataset::where('created_by', $user->id)->get();
        
        $exampleRules = [
            'Ortalama Hesaplama' => 'ortalama(deger)',
            'Toplam ve Bölme' => 'topla(deger) / sayi',
            'Maksimum ve Minimum Fark' => '(max(deger) - min(deger)) / 2',
            'Standart Sapma Hesaplama' => 'sqrt(topla((deger - ortalama(deger))^2) / sayi)',
            'Değişim Oranı' => '(son_deger - ilk_deger) / ilk_deger * 100',
        ];

        return view('statistician.rules.create', compact('datasets', 'exampleRules'));
    }

    public function testRule(Request $request)
    {
        $request->validate([
            'dataset_id' => 'required|exists:datasets,id',
            'rule' => 'required|string',
        ]);

        $dataset = Dataset::findOrFail($request->dataset_id);
        $this->authorize('view', $dataset);

        // Geçici olarak kuralı değiştirip test et
        $originalRule = $dataset->calculation_rule;
        $dataset->calculation_rule = $request->rule;

        $calculationEngine = new CalculationEngine();
        $result = $calculationEngine->calculate($dataset);

        // Orijinal kuralı geri yükle
        $dataset->calculation_rule = $originalRule;

        return response()->json([
            'success' => true,
            'result' => $result,
            'message' => 'Kural test edildi.',
        ]);
    }

    public function calculateAll()
    {
        $user = auth()->user();
        
        $datasets = Dataset::where('created_by', $user->id)
            ->whereNotNull('calculation_rule')
            ->get();

        $calculationEngine = new CalculationEngine();
        $results = [];

        foreach ($datasets as $dataset) {
            $results[$dataset->id] = [
                'name' => $dataset->name,
                'value' => $calculationEngine->calculate($dataset),
                'unit' => $dataset->unit,
                'rule' => $dataset->calculation_rule,
            ];
        }

        return view('statistician.rules.calculations', compact('results'));
    }
}
