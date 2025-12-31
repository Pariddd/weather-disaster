<?php

namespace App\Services;

use App\Models\WeatherData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
  private string $apiKey;
  private string $baseUrl = 'https://api.openweathermap.org/data/2.5';

  public function __construct()
  {
    $this->apiKey = config('services.openweathermap.key');
  }

  public function getCurrentWeather(string $city, float $lat = null, float $lon = null): ?array
  {
    $cacheKey = "weather_{$city}_" . now()->format('YmdHi');

    return Cache::remember($cacheKey, 600, function () use ($city, $lat, $lon) {
      try {
        $params = [
          'appid' => $this->apiKey,
          'units' => 'metric',
          'lang' => 'id',
        ];

        if ($lat && $lon) {
          $params['lat'] = $lat;
          $params['lon'] = $lon;
        } else {
          $params['q'] = $city . ',ID';
        }

        $response = Http::get("{$this->baseUrl}/weather", $params);

        if ($response->successful()) {
          return $this->formatWeatherData($response->json());
        }

        Log::error('Weather API Error', [
          'city' => $city,
          'status' => $response->status(),
          'body' => $response->body()
        ]);

        return null;
      } catch (\Exception $e) {
        Log::error('Weather Service Exception', [
          'city' => $city,
          'error' => $e->getMessage()
        ]);
        return null;
      }
    });
  }

  public function getForecast(string $city, float $lat = null, float $lon = null): ?array
  {
    try {
      $params = [
        'appid' => $this->apiKey,
        'units' => 'metric',
        'lang' => 'id',
      ];

      if ($lat && $lon) {
        $params['lat'] = $lat;
        $params['lon'] = $lon;
      } else {
        $params['q'] = $city . ',ID';
      }

      $response = Http::get("{$this->baseUrl}/forecast", $params);

      if ($response->successful()) {
        return $response->json();
      }

      return null;
    } catch (\Exception $e) {
      Log::error('Forecast Service Exception', [
        'city' => $city,
        'error' => $e->getMessage()
      ]);
      return null;
    }
  }

  private function formatWeatherData(array $data): array
  {
    return [
      'city' => $data['name'],
      'latitude' => $data['coord']['lat'],
      'longitude' => $data['coord']['lon'],
      'temperature' => $data['main']['temp'],
      'feels_like' => $data['main']['feels_like'],
      'humidity' => $data['main']['humidity'],
      'pressure' => $data['main']['pressure'],
      'wind_speed' => $data['wind']['speed'] ?? 0,
      'wind_deg' => $data['wind']['deg'] ?? null,
      'rainfall' => $data['rain']['1h'] ?? 0,
      'clouds' => $data['clouds']['all'] ?? 0,
      'weather_main' => $data['weather'][0]['main'] ?? null,
      'weather_description' => $data['weather'][0]['description'] ?? null,
      'weather_icon' => $data['weather'][0]['icon'] ?? null,
      'recorded_at' => now(),
    ];
  }

  public function storeWeatherData(array $weatherData): WeatherData
  {
    return WeatherData::create($weatherData);
  }


  public function getHistoricalRainfall(string $city, int $days = 7): array
  {
    $from = now()->subDays($days);

    $data = WeatherData::where('city', $city)
      ->where('recorded_at', '>=', $from)
      ->orderBy('recorded_at', 'desc')
      ->get(['rainfall', 'recorded_at']);

    return [
      'rainfall_3day_avg' => $data->take(72)->avg('rainfall'), // 3 days * 24 hours
      'rainfall_7day_avg' => $data->avg('rainfall'),
      'rainfall_trend' => $this->calculateTrend($data),
    ];
  }

  private function calculateTrend($data): float
  {
    if ($data->count() < 2) {
      return 0;
    }

    $latest = $data->first()->rainfall;
    $previous = $data->skip(1)->first()->rainfall;

    return $latest - $previous;
  }
}
