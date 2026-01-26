<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ValidationLog;
use App\Models\Dataset;
use App\Services\DataVerificationService;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $validationLogs = ValidationLog::with('dataset')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $statusStats = [
            'pending' => ValidationLog::where('status', 'pending')->count(),
            'verified' => ValidationLog::where('status', 'verified')->count(),
            'failed' => ValidationLog::where('status', 'failed')->count(),
        ];
        
        return view('admin.validations.index', compact('validationLogs', 'statusStats'));
    }

    public function show(ValidationLog $validation)
    {
        $validation->load(['dataset', 'dataset.dataPoints' => function ($query) use ($validation) {
            $query->whereDate('date', $validation->date)
                  ->with('dataProvider');
        }]);
        
        return view('admin.validations.show', compact('validation'));
    }

    public function retry(ValidationLog $validation)
    {
        $dataset = Dataset::find($validation->dataset_id);
        
        if (!$dataset) {
            return redirect()->back()
                ->with('error', 'Veri seti bulunamadı.');
        }
        
        $service = new DataVerificationService();
        $result = $service->processValidation($dataset, $validation->date);
        
        if ($result) {
            return redirect()->back()
                ->with('success', 'Doğrulama işlemi başarıyla tekrarlandı.');
        }
        
        return redirect()->back()
            ->with('error', 'Doğrulama işlemi tekrarlanamadı.');
    }
}
