<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes
Route::get('/datasets', [\App\Http\Controllers\Api\DatasetController::class, 'index']);
Route::get('/datasets/{dataset}', [\App\Http\Controllers\Api\DatasetController::class, 'show']);
Route::get('/datasets/{dataset}/data', [\App\Http\Controllers\Api\DatasetController::class, 'dataPoints']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/data-points', [\App\Http\Controllers\Api\DataPointController::class, 'store']);
    Route::put('/data-points/{dataPoint}', [\App\Http\Controllers\Api\DataPointController::class, 'update']);
    Route::delete('/data-points/{dataPoint}', [\App\Http\Controllers\Api\DataPointController::class, 'destroy']);
    
    Route::post('/calculations', [\App\Http\Controllers\Api\CalculationController::class, 'calculate']);
});
