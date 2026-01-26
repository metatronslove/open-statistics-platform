<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:provider');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();

        if (!$dataProvider) {
            return redirect()->route('provider.profile')
                ->with('warning', 'Lütfen önce veri sağlayıcı profilinizi tamamlayın.');
        }

        // Bu sağlayıcının veri girebileceği veri setleri
        $availableDatasets = Dataset::where('is_public', true)
            ->with(['dataPoints' => function ($query) use ($dataProvider) {
                $query->where('data_provider_id', $dataProvider->id)
                    ->orderBy('date', 'desc')
                    ->limit(5);
            }])
            ->get();

        // Son eklenen veri noktaları
        $recentDataPoints = DataPoint::where('data_provider_id', $dataProvider->id)
            ->with('dataset')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Doğrulanmamış veri noktaları
        $pendingDataPoints = DataPoint::where('data_provider_id', $dataProvider->id)
            ->where('is_verified', false)
            ->with('dataset')
            ->count();

        // Doğrulanmış veri noktaları
        $verifiedDataPoints = DataPoint::where('data_provider_id', $dataProvider->id)
            ->where('is_verified', true)
            ->count();

        return view('provider.dashboard', compact(
            'dataProvider',
            'availableDatasets',
            'recentDataPoints',
            'pendingDataPoints',
            'verifiedDataPoints'
        ));
    }

    public function profile()
    {
        $user = auth()->user();
        $dataProvider = DataProvider::where('user_id', $user->id)->first();
        
        return view('provider.profile', compact('dataProvider'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'organization_name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $dataProvider = DataProvider::updateOrCreate(
            ['user_id' => $user->id],
            [
                'organization_name' => $request->organization_name,
                'website' => $request->website,
                'description' => $request->description,
            ]
        );

        return redirect()->route('provider.dashboard')
            ->with('success', 'Profil başarıyla güncellendi.');
    }
}
