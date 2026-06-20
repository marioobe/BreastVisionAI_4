<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AiModelController;
use App\Http\Controllers\Admin\PredictionController;
use App\Http\Controllers\Api\PredictionController as ApiPredictionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.authenticate');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::get('/pemeriksaan', function () {
    return view('pasien.form');
})->name('pasien.form');

Route::post('/pemeriksaan', [ApiPredictionController::class, 'predictFromWeb'])->name('pasien.predict');

Route::get('/hasil/{prediction}', function (\App\Models\Prediction $prediction) {
    return view('pasien.hasil', compact('prediction'));
})->name('pasien.hasil');

Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('admin')->group(function () {
        Route::get('/models', [AiModelController::class, 'index'])->name('admin.models');
        Route::post('/models', [AiModelController::class, 'store'])->name('admin.models.store');
        Route::get('/models/{aiModel}/edit', [AiModelController::class, 'edit'])->name('admin.models.edit');
        Route::put('/models/{aiModel}', [AiModelController::class, 'update'])->name('admin.models.update');
        Route::delete('/models/{aiModel}', [AiModelController::class, 'destroy'])->name('admin.models.destroy');
        Route::post('/models/{aiModel}/activate', [AiModelController::class, 'activate'])->name('admin.models.activate');

        Route::get('/predictions', [PredictionController::class, 'index'])->name('admin.predictions');
        Route::get('/predictions/{prediction}', [PredictionController::class, 'show'])->name('admin.predictions.show');
        Route::delete('/predictions/{prediction}', [PredictionController::class, 'destroy'])->name('admin.predictions.destroy');
    });
});
