<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dataset;
use App\Models\DataPoint;
use App\Models\DataProvider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_datasets' => Dataset::count(),
            'total_data_points' => DataPoint::count(),
            'verified_data_points' => DataPoint::where('is_verified', true)->count(),
            'total_providers' => DataProvider::count(),
            'verified_providers' => DataProvider::where('is_verified', true)->count(),
            'pending_verifications' => DataPoint::where('is_verified', false)->count(),
        ];

        $recentUsers = User::latest()->take(10)->get();
        $recentDatasets = Dataset::latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentDatasets'));
    }
}
