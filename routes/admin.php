<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Admin\ValidationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('providers/{provider}/verify', [UserManagementController::class, 'verifyProvider'])
        ->name('providers.verify');
    
    // Dataset Management
    Route::resource('datasets', DatasetController::class);
    
    // Validation Management
    Route::get('validations', [ValidationController::class, 'index'])->name('validations.index');
    Route::get('validations/{validation}', [ValidationController::class, 'show'])->name('validations.show');
    Route::post('validations/{validation}/retry', [ValidationController::class, 'retry'])->name('validations.retry');
    
    // System Statistics
    Route::get('statistics', [DashboardController::class, 'statistics'])->name('statistics');
});
