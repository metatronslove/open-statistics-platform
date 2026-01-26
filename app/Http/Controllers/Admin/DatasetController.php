<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DatasetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $datasets = Dataset::with('creator')->latest()->paginate(20);
        return view('admin.datasets.index', compact('datasets'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['admin', 'statistician'])->get();
        return view('admin.datasets.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'created_by' => 'required|exists:users,id',
            'calculation_rule' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $dataset = Dataset::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'description' => $request->description,
            'unit' => $request->unit,
            'created_by' => $request->created_by,
            'calculation_rule' => $request->calculation_rule,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('admin.datasets.index')
            ->with('success', 'Veri seti başarıyla oluşturuldu.');
    }

    public function show(Dataset $dataset)
    {
        $dataPoints = $dataset->dataPoints()
            ->with('dataProvider')
            ->orderBy('date', 'desc')
            ->paginate(20);
            
        $validationLogs = $dataset->validationLogs()
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('admin.datasets.show', compact('dataset', 'dataPoints', 'validationLogs'));
    }

    public function edit(Dataset $dataset)
    {
        $users = User::whereIn('role', ['admin', 'statistician'])->get();
        return view('admin.datasets.edit', compact('dataset', 'users'));
    }

    public function update(Request $request, Dataset $dataset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'created_by' => 'required|exists:users,id',
            'calculation_rule' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $dataset->update([
            'name' => $request->name,
            'description' => $request->description,
            'unit' => $request->unit,
            'created_by' => $request->created_by,
            'calculation_rule' => $request->calculation_rule,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('admin.datasets.index')
            ->with('success', 'Veri seti başarıyla güncellendi.');
    }

    public function destroy(Dataset $dataset)
    {
        $dataset->delete();
        return redirect()->route('admin.datasets.index')
            ->with('success', 'Veri seti başarıyla silindi.');
    }
}
