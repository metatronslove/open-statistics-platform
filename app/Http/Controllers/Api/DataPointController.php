<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataPoint;
use App\Models\Dataset;
use App\Models\DataProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataPointController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'provider') {
            return response()->json([
                'success' => false,
                'message' => 'Sadece veri sağlayıcılar veri girebilir.',
            ], 403);
        }
        
        $dataProvider = DataProvider::where('user_id', $user->id)->first();
        
        if (!$dataProvider) {
            return response()->json([
                'success' => false,
                'message' => 'Önce veri sağlayıcı profilinizi tamamlayın.',
            ], 400);
        }
        
        $request->validate([
            'dataset_id' => 'required|exists:datasets,id',
            'date' => 'required|date',
            'value' => 'required|numeric',
            'source_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);
        
        $dataset = Dataset::find($request->dataset_id);
        
        if (!$dataset->is_public) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veri seti kapalı.',
            ], 403);
        }
        
        // Check for duplicate entry
        $existing = DataPoint::where('dataset_id', $request->dataset_id)
            ->where('data_provider_id', $dataProvider->id)
            ->whereDate('date', $request->date)
            ->first();
            
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Bu tarih için zaten veri girişi yapmışsınız.',
            ], 400);
        }
        
        $dataPoint = DataPoint::create([
            'dataset_id' => $request->dataset_id,
            'data_provider_id' => $dataProvider->id,
            'date' => $request->date,
            'value' => $request->value,
            'source_url' => $request->source_url,
            'notes' => $request->notes,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Veri başarıyla eklendi.',
            'data' => $dataPoint,
        ], 201);
    }

    public function update(Request $request, DataPoint $dataPoint)
    {
        $user = Auth::user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();
        
        if (!$dataProvider || $dataPoint->data_provider_id !== $dataProvider->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veriyi güncelleme yetkiniz yok.',
            ], 403);
        }
        
        $request->validate([
            'value' => 'required|numeric',
            'source_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);
        
        $dataPoint->update([
            'value' => $request->value,
            'source_url' => $request->source_url,
            'notes' => $request->notes,
            'is_verified' => false,
            'verified_value' => null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Veri başarıyla güncellendi.',
            'data' => $dataPoint,
        ]);
    }

    public function destroy(DataPoint $dataPoint)
    {
        $user = Auth::user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();
        
        if (!$dataProvider || $dataPoint->data_provider_id !== $dataProvider->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu veriyi silme yetkiniz yok.',
            ], 403);
        }
        
        $dataPoint->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Veri başarıyla silindi.',
        ]);
    }
}
