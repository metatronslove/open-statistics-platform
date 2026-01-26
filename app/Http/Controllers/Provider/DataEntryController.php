<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use App\Services\DataVerificationService;
use Illuminate\Http\Request;

class DataEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:provider');
    }

    public function index()
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();

        if (!$dataProvider) {
            return redirect()->route('provider.profile')
                ->with('warning', 'Lütfen önce veri sağlayıcı profilinizi tamamlayın.');
        }

        $myDataPoints = DataPoint::where('data_provider_id', $dataProvider->id)
            ->with('dataset')
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('provider.data-entry.index', compact('myDataPoints', 'dataProvider'));
    }

    public function create()
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();

        if (!$dataProvider) {
            return redirect()->route('provider.profile');
        }

        $datasets = Dataset::where('is_public', true)->get();
        
        return view('provider.data-entry.create', compact('datasets', 'dataProvider'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->firstOrFail();

        $request->validate([
            'dataset_id' => 'required|exists:datasets,id',
            'date' => 'required|date',
            'value' => 'required|numeric',
            'source_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Aynı tarih ve veri seti için daha önce veri girilmiş mi kontrol et
        $existingData = DataPoint::where('dataset_id', $request->dataset_id)
            ->where('data_provider_id', $dataProvider->id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existingData) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bu tarih için zaten veri girişi yapmışsınız. Lütfen güncelleme yapın.');
        }

        $dataPoint = DataPoint::create([
            'dataset_id' => $request->dataset_id,
            'data_provider_id' => $dataProvider->id,
            'date' => $request->date,
            'value' => $request->value,
            'source_url' => $request->source_url,
            'notes' => $request->notes,
            'is_verified' => false,
        ]);

        // Veri doğrulama servisini tetikle
        $dataset = Dataset::find($request->dataset_id);
        $service = new DataVerificationService();
        $service->checkAndTriggerValidation($dataset, $request->date);

        return redirect()->route('provider.data-entry.index')
            ->with('success', 'Veri başarıyla eklendi. Doğrulama süreci başlatıldı.');
    }

    public function edit(DataPoint $dataPoint)
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->firstOrFail();

        // Sadece kendi verilerini düzenleyebilir
        if ($dataPoint->data_provider_id !== $dataProvider->id) {
            abort(403, 'Bu veriyi düzenleme yetkiniz yok.');
        }

        $datasets = Dataset::where('is_public', true)->get();
        
        return view('provider.data-entry.edit', compact('dataPoint', 'datasets', 'dataProvider'));
    }

    public function update(Request $request, DataPoint $dataPoint)
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->firstOrFail();

        // Sadece kendi verilerini güncelleyebilir
        if ($dataPoint->data_provider_id !== $dataProvider->id) {
            abort(403, 'Bu veriyi güncelleme yetkiniz yok.');
        }

        $request->validate([
            'value' => 'required|numeric',
            'source_url' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $oldDate = $dataPoint->date;
        $oldDatasetId = $dataPoint->dataset_id;

        $dataPoint->update([
            'value' => $request->value,
            'source_url' => $request->source_url,
            'notes' => $request->notes,
            'is_verified' => false, // Güncelleme sonrası tekrar doğrulama gerekir
            'verified_value' => null,
        ]);

        // Veri doğrulama servisini tetikle (eski ve yeni veri seti/tarih için)
        $service = new DataVerificationService();
        
        if ($oldDate) {
            $oldDataset = Dataset::find($oldDatasetId);
            if ($oldDataset) {
                $service->checkAndTriggerValidation($oldDataset, $oldDate);
            }
        }

        if ($dataPoint->dataset) {
            $service->checkAndTriggerValidation($dataPoint->dataset, $dataPoint->date);
        }

        return redirect()->route('provider.data-entry.index')
            ->with('success', 'Veri başarıyla güncellendi. Doğrulama süreci başlatıldı.');
    }

    public function destroy(DataPoint $dataPoint)
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->firstOrFail();

        // Sadece kendi verilerini silebilir
        if ($dataPoint->data_provider_id !== $dataProvider->id) {
            abort(403, 'Bu veriyi silme yetkiniz yok.');
        }

        $dataset = $dataPoint->dataset;
        $date = $dataPoint->date;
        
        $dataPoint->delete();

        // Silme işleminden sonra doğrulama sürecini tekrar başlat
        $service = new DataVerificationService();
        $service->checkAndTriggerValidation($dataset, $date);

        return redirect()->route('provider.data-entry.index')
            ->with('success', 'Veri başarıyla silindi.');
    }
}
