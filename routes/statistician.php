<?php

use App\Http\Controllers\Statistician\DashboardController;
use App\Http\Controllers\Statistician\DatasetController;
use App\Http\Controllers\Statistician\RuleController;
use App\Http\Controllers\Statistician\CalculationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:statistician'])->prefix('statistician')->name('statistician.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Dataset Management
    Route::resource('datasets', DatasetController::class);
    Route::post('datasets/{dataset}/verify', [DatasetController::class, 'verifyData'])->name('datasets.verify');
    Route::get('datasets/{dataset}/chart', [DatasetController::class, 'chartData'])->name('datasets.chart');
    
    // Rule Management
    Route::get('rules', [RuleController::class, 'index'])->name('rules.index');
    Route::get('rules/create', [RuleController::class, 'create'])->name('rules.create');
    Route::post('rules/test', [RuleController::class, 'testRule'])->name('rules.test');
    Route::get('rules/calculate', [RuleController::class, 'calculateAll'])->name('rules.calculate');
    Route::get('rules/{dataset}/edit', [RuleController::class, 'edit'])->name('rules.edit');
    
    // Calculation Management
    Route::get('calculations', [CalculationController::class, 'index'])->name('calculations.index');
    Route::get('calculations/{calculation}', [CalculationController::class, 'show'])->name('calculations.show');
    Route::post('calculations/run-all', [CalculationController::class, 'runAll'])->name('calculations.run-all');
    
    // Widget Management
    Route::post('widgets/reorder', [DashboardController::class, 'reorderWidgets'])->name('widgets.reorder');
    Route::post('widgets/toggle', [DashboardController::class, 'toggleWidget'])->name('widgets.toggle');
});
