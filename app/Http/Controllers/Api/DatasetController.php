<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function index()
    {
        $datasets = Dataset::where('is_public', true)
            ->withCount('dataPoints')
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'data' => $datasets,
        ]);
    }

    public function show(Dataset $dataset)
    {
        if (!$dataset->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veri seti herkese açık değil.',
            ], 403);
        }
        
        $dataset->load(['creator', 'dataPoints' => function ($query) {
            $query->where('is_verified', true)
                  ->orderBy('date', 'desc')
                  ->limit(100);
        }]);
        
        return response()->json([
            'success' => true,
            'data' => $dataset,
        ]);
    }

    public function dataPoints(Dataset $dataset, Request $request)
    {
        if (!$dataset->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veri seti herkese açık değil.',
            ], 403);
        }
        
        $query = $dataset->dataPoints()->where('is_verified', true);
        
        // Date filter
        if ($request->has('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        
        $dataPoints = $query->orderBy('date', $request->get('order', 'desc'))
            ->paginate($request->get('per_page', 100));
            
        return response()->json([
            'success' => true,
            'data' => $dataPoints,
        ]);
    }
}
