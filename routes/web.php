<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\PredictionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/map', [DashboardController::class, 'map'])->name('map');

Route::prefix('weather')->name('weather.')->group(function () {
    Route::get('/', [WeatherController::class, 'index'])->name('index');
    Route::get('/{city}', [WeatherController::class, 'show'])->name('show');
    Route::post('/refresh', [WeatherController::class, 'refresh'])->name('refresh');
    Route::get('/{city}/historical', [WeatherController::class, 'historical'])->name('historical');
});

Route::prefix('predictions')->name('predictions.')->group(function () {
    Route::get('/', [PredictionController::class, 'index'])->name('index');
    Route::post('/predict', [PredictionController::class, 'predict'])->name('predict');
    Route::get('/high-risk', [PredictionController::class, 'highRisk'])->name('high-risk');
    Route::get('/city/{city}', [PredictionController::class, 'cityPredictions'])->name('city');
    Route::get('/{id}', [PredictionController::class, 'show'])->name('show');
    Route::post('/predict-all', [PredictionController::class, 'predictAll'])->name('predict-all');
});
