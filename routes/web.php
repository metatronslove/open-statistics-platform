<?php

use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Ana Sayfa
Route::get('/', [HomeController::class, 'index'])->name('home');

// OAuth Routes
Route::get('/auth/google', [OAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [OAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/github', [OAuthController::class, 'redirectToGithub'])->name('auth.github');
Route::get('/auth/github/callback', [OAuthController::class, 'handleGithubCallback']);
Route::get('/auth/facebook', [OAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [OAuthController::class, 'handleFacebookCallback']);

// Authentication Routes (Laravel Breeze/Jetstream will add these)
require __DIR__.'/auth.php';

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class);
    Route::post('/providers/{provider}/verify', [\App\Http\Controllers\Admin\UserManagementController::class, 'verifyProvider'])
        ->name('providers.verify');
    
    // Dataset Management
    Route::resource('datasets', \App\Http\Controllers\Admin\DatasetController::class);
});

// Statistician Routes
Route::middleware(['auth', 'role:statistician'])->prefix('statistician')->name('statistician.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Statistician\DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Dataset Management
    Route::resource('datasets', \App\Http\Controllers\Statistician\DatasetController::class);
    Route::post('/datasets/{dataset}/verify', [\App\Http\Controllers\Statistician\DatasetController::class, 'verifyData'])
        ->name('datasets.verify');
    
    // Rule Management
    Route::get('/rules', [\App\Http\Controllers\Statistician\RuleController::class, 'index'])->name('rules.index');
    Route::get('/rules/create', [\App\Http\Controllers\Statistician\RuleController::class, 'create'])->name('rules.create');
    Route::post('/rules/test', [\App\Http\Controllers\Statistician\RuleController::class, 'testRule'])->name('rules.test');
    Route::get('/rules/calculate', [\App\Http\Controllers\Statistician\RuleController::class, 'calculateAll'])->name('rules.calculate');
});

// Provider Routes
Route::middleware(['auth', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Provider\DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\Provider\DashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Provider\DashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Data Entry
    Route::resource('data-entry', \App\Http\Controllers\Provider\DataEntryController::class)->except(['show']);
});
