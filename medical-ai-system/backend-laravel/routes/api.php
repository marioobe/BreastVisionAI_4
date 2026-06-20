<?php

use App\Http\Controllers\Api\AiModelController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PredictionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/predict', [PredictionController::class, 'predict']);
    Route::get('/predictions', [PredictionController::class, 'index']);
    Route::get('/predictions/{prediction}', [PredictionController::class, 'show']);
    Route::delete('/predictions/{prediction}', [PredictionController::class, 'destroy']);

    Route::get('/models', [AiModelController::class, 'index']);
    Route::post('/models', [AiModelController::class, 'store']);
    Route::put('/models/{aiModel}', [AiModelController::class, 'update']);
    Route::delete('/models/{aiModel}', [AiModelController::class, 'destroy'])->name('api.models.destroy');
    Route::post('/models/{aiModel}/activate', [AiModelController::class, 'activate'])->name('api.models.activate');

    Route::get('/dashboard', [DashboardController::class, 'index']);
});
