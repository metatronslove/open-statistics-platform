<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\OAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    // OAuth Routes
    Route::get('/auth/google', [OAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [OAuthController::class, 'handleGoogleCallback']);
    
    Route::get('/auth/github', [OAuthController::class, 'redirectToGithub'])->name('auth.github');
    Route::get('/auth/github/callback', [OAuthController::class, 'handleGithubCallback']);
    
    Route::get('/auth/facebook', [OAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/auth/facebook/callback', [OAuthController::class, 'handleFacebookCallback']);
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('admin.dashboard');
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);
    Route::post('users/{provider}/verify', [\App\Http\Controllers\Admin\UserManagementController::class, 'verifyProvider'])->name('admin.users.verify');
    
    // Dataset Management
    Route::resource('datasets', \App\Http\Controllers\Admin\DatasetController::class);
    
    // Validation Management
    Route::get('validations', [\App\Http\Controllers\Admin\ValidationController::class, 'index'])->name('admin.validations.index');
    Route::get('validations/{validation}', [\App\Http\Controllers\Admin\ValidationController::class, 'show'])->name('admin.validations.show');
    Route::post('validations/{validation}/retry', [\App\Http\Controllers\Admin\ValidationController::class, 'retry'])->name('admin.validations.retry');
});

// Statistician routes
Route::prefix('statistician')->middleware(['auth', 'role:statistician'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Statistician\DashboardController::class, 'dashboard'])->name('statistician.dashboard');
    
    // Dataset Management
    Route::resource('datasets', \App\Http\Controllers\Statistician\DatasetController::class);
    Route::post('datasets/{dataset}/verify', [\App\Http\Controllers\Statistician\DatasetController::class, 'verifyData'])->name('statistician.datasets.verify');
    
    // Calculation Rules
    Route::get('rules', [\App\Http\Controllers\Statistician\RuleController::class, 'index'])->name('statistician.rules.index');
    Route::get('rules/create', [\App\Http\Controllers\Statistician\RuleController::class, 'create'])->name('statistician.rules.create');
    Route::post('rules/test', [\App\Http\Controllers\Statistician\RuleController::class, 'testRule'])->name('statistician.rules.test');
    Route::post('rules/calculate-all', [\App\Http\Controllers\Statistician\RuleController::class, 'calculateAll'])->name('statistician.rules.calculate-all');
    
    // Calculations
    Route::get('calculations', [\App\Http\Controllers\Statistician\CalculationController::class, 'index'])->name('statistician.calculations.index');
    Route::get('calculations/{dataset}', [\App\Http\Controllers\Statistician\CalculationController::class, 'show'])->name('statistician.calculations.show');
    Route::post('calculations/run-all', [\App\Http\Controllers\Statistician\CalculationController::class, 'runAll'])->name('statistician.calculations.run-all');
});

// Provider routes
Route::prefix('provider')->middleware(['auth', 'role:provider'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Provider\DashboardController::class, 'dashboard'])->name('provider.dashboard');
    
    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\Provider\DashboardController::class, 'profile'])->name('provider.profile');
    Route::post('/profile', [\App\Http\Controllers\Provider\DashboardController::class, 'updateProfile'])->name('provider.profile.update');
    
    // Data Entry
    Route::resource('data-entry', \App\Http\Controllers\Provider\DataEntryController::class)->except(['show']);
});

// API routes for data providers
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('datasets', \App\Http\Controllers\Api\DatasetController::class)->only(['index', 'show']);
    Route::get('datasets/{dataset}/data-points', [\App\Http\Controllers\Api\DatasetController::class, 'dataPoints'])->name('api.datasets.data-points');
    
    Route::apiResource('data-points', \App\Http\Controllers\Api\DataPointController::class)->only(['store', 'update', 'destroy']);
});

// Authentication routes (Laravel Breeze)
require __DIR__.'/auth.php';
