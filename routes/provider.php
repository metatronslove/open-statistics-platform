<?php

use App\Http\Controllers\Provider\DashboardController;
use App\Http\Controllers\Provider\DataEntryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Data Entry
    Route::resource('data-entry', DataEntryController::class)->except(['show']);
    
    // Quick Actions
    Route::get('quick-entry', [DataEntryController::class, 'quickEntry'])->name('quick-entry');
    Route::post('quick-entry', [DataEntryController::class, 'storeQuickEntry'])->name('quick-entry.store');
    
    // Data Statistics
    Route::get('statistics', [DashboardController::class, 'statistics'])->name('statistics');
    
    // Verification Status
    Route::get('verification-status', [DataEntryController::class, 'verificationStatus'])->name('verification-status');
});
