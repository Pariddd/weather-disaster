<?php

namespace App\Services;

use App\Models\DisasterPrediction;
use App\Models\WeatherData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DisasterPredictionService
{
  private string $mlApiUrl;

  public function __construct()
  {
    $this->mlApiUrl = config('services.ml_api.url', 'http://localhost:5000');
  }

  /**
   * Predict disaster risk
   */
  public function predictDisaster(WeatherData $weatherData, array $historicalData = []): ?DisasterPrediction
  {
    try {
      $payload = $this->preparePredictionPayload($weatherData, $historicalData);

      $response = Http::timeout(10)->post("{$this->mlApiUrl}/predict", $payload);

      if ($response->successful()) {
        $prediction = $response->json();
        return $this->storePrediction($weatherData, $prediction);
      }

      Log::error('ML Prediction API Error', [
        'status' => $response->status(),
        'body' => $response->body()
      ]);

      return null;
    } catch (\Exception $e) {
      Log::error('Prediction Service Exception', [
        'error' => $e->getMessage()
      ]);
      return null;
    }
  }

  /**
   * Prepare payload for ML API
   */
  private function preparePredictionPayload(WeatherData $weatherData, array $historicalData): array
  {
    return [
      'city' => $weatherData->city,
      'rainfall_mm' => $weatherData->rainfall,
      'duration_hours' => $this->estimateDuration($weatherData->rainfall),
      'temperature_c' => $weatherData->temperature,
      'humidity_percent' => $weatherData->humidity,
      'wind_speed_kmh' => $weatherData->wind_speed * 3.6, // m/s to km/h
      'pressure_hpa' => $weatherData->pressure,
      'rainfall_3day_avg' => $historicalData['rainfall_3day_avg'] ?? $weatherData->rainfall,
      'rainfall_7day_avg' => $historicalData['rainfall_7day_avg'] ?? $weatherData->rainfall,
      'rainfall_trend' => $historicalData['rainfall_trend'] ?? 0,
    ];
  }

  /**
   * Estimate rain duration based on intensity
   */
  private function estimateDuration(float $rainfall): float
  {
    if ($rainfall > 100) {
      return rand(4, 12);
    } elseif ($rainfall > 50) {
      return rand(2, 6);
    } elseif ($rainfall > 20) {
      return rand(1, 4);
    }
    return rand(0, 2);
  }

  /**
   * Store prediction to database
   */
  private function storePrediction(WeatherData $weatherData, array $prediction): DisasterPrediction
  {
    return DisasterPrediction::create([
      'weather_data_id' => $weatherData->id,
      'city' => $weatherData->city,
      'latitude' => $weatherData->latitude,
      'longitude' => $weatherData->longitude,
      'disaster_predicted' => $prediction['prediction']['disaster'],
      'disaster_probability' => $prediction['prediction']['probability'],
      'safe_probability' => $prediction['prediction']['safe_probability'],
      'risk_level' => $prediction['prediction']['risk_level'],
      'disaster_type' => $prediction['prediction']['disaster_type'],
      'warnings' => $prediction['warnings'] ?? [],
      'input_features' => $prediction['input'] ?? [],
      'predicted_at' => now(),
    ]);
  }

  /**
   * Get predictions for all cities
   */
  public function getPredictionsForAllCities(): array
  {
    $weatherService = app(WeatherService::class);
    $cities = config('weather.cities', []);
    $predictions = [];

    foreach ($cities as $city) {
      $cityData = $this->getCityData($city);

      // Get current weather
      $weather = $weatherService->getCurrentWeather(
        $city,
        $cityData['lat'],
        $cityData['lon']
      );

      if ($weather) {
        $weatherData = $weatherService->storeWeatherData($weather);
        $historical = $weatherService->getHistoricalRainfall($city);

        $prediction = $this->predictDisaster($weatherData, $historical);

        if ($prediction) {
          $predictions[] = $prediction;
        }
      }
    }

    return $predictions;
  }

  /**
   * Get city data from config
   */
  private function getCityData(string $city): array
  {
    $cities = config('weather.city_coordinates', []);
    return $cities[$city] ?? ['lat' => 0, 'lon' => 0];
  }

  /**
   * Get risk summary for dashboard
   */
  public function getRiskSummary(): array
  {
    $latestPredictions = DisasterPrediction::with('city')
      ->whereIn('id', function ($query) {
        $query->selectRaw('MAX(id)')
          ->from('disaster_predictions')
          ->groupBy('city');
      })
      ->get();

    return [
      'total_cities' => $latestPredictions->count(),
      'low_risk' => $latestPredictions->where('risk_level', 'Low')->count(),
      'medium_risk' => $latestPredictions->where('risk_level', 'Medium')->count(),
      'high_risk' => $latestPredictions->where('risk_level', 'High')->count(),
      'critical_risk' => $latestPredictions->where('risk_level', 'Critical')->count(),
      'active_disasters' => $latestPredictions->where('disaster_predicted', true)->count(),
    ];
  }
}
