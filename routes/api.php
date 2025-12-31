<?php

use App\Http\Controllers\Api\WeatherApiController;
use App\Http\Controllers\Api\PredictionApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
  Route::prefix('weather')->group(function () {
    Route::get('/current', [WeatherApiController::class, 'current']);
    Route::get('/latest', [WeatherApiController::class, 'latest']);
  });

  Route::prefix('predictions')->group(function () {
    Route::get('/summary', [PredictionApiController::class, 'summary']);
    Route::get('/heatmap', [PredictionApiController::class, 'heatmap']);
  });
});
