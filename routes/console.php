<?php

use App\Services\WeatherService;
use App\Services\DisasterPredictionService;
use App\Models\City;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::call(function () {
    $weatherService = app(WeatherService::class);
    $predictionService = app(DisasterPredictionService::class);

    $cities = City::active()->get();

    foreach ($cities as $city) {
        try {
            $weather = $weatherService->getCurrentWeather(
                $city->name,
                $city->latitude,
                $city->longitude
            );

            if ($weather) {
                $weatherData = $weatherService->storeWeatherData($weather);

                $historical = $weatherService->getHistoricalRainfall($city->name);

                $predictionService->predictDisaster($weatherData, $historical);
            }
        } catch (\Exception $e) {
            \Log::error("Scheduled task error for {$city->name}: " . $e->getMessage());
        }
    }
})->hourly()->name('fetch-weather-and-predict');


return [

    'openweathermap' => [
        'key' => env('OPENWEATHERMAP_API_KEY'),
        'base_url' => 'https://api.openweathermap.org/data/2.5',
    ],

    'ml_api' => [
        'url' => env('ML_API_URL', 'http://localhost:5000'),
        'timeout' => env('ML_API_TIMEOUT', 10),
    ],
];


return [
    'cities' => [
        'Banda Aceh',
        'Medan',
        'Padang',
        'Pekanbaru',
        'Jambi',
        'Palembang',
        'Bengkulu',
        'Bandar Lampung',
        'Jakarta',
        'Bogor',
        'Bandung',
        'Semarang',
        'Yogyakarta',
        'Surabaya',
        'Pontianak',
        'Banjarmasin',
        'Samarinda',
        'Makassar',
        'Manado',
        'Palu',
        'Denpasar',
        'Mataram',
        'Jayapura',
        'Ambon',
    ],

    'city_coordinates' => [
        'Banda Aceh' => ['lat' => 5.5483, 'lon' => 95.3238],
        'Medan' => ['lat' => 3.5952, 'lon' => 98.6722],
        'Padang' => ['lat' => -0.9471, 'lon' => 100.4172],
        'Pekanbaru' => ['lat' => 0.5071, 'lon' => 101.4478],
        'Jambi' => ['lat' => -1.6101, 'lon' => 103.6131],
        'Palembang' => ['lat' => -2.9761, 'lon' => 104.7754],
        'Bengkulu' => ['lat' => -3.8004, 'lon' => 102.2655],
        'Bandar Lampung' => ['lat' => -5.4292, 'lon' => 105.2625],
        'Jakarta' => ['lat' => -6.2088, 'lon' => 106.8456],
        'Bogor' => ['lat' => -6.5950, 'lon' => 106.7970],
        'Bandung' => ['lat' => -6.9175, 'lon' => 107.6191],
        'Semarang' => ['lat' => -6.9667, 'lon' => 110.4167],
        'Yogyakarta' => ['lat' => -7.7956, 'lon' => 110.3695],
        'Surabaya' => ['lat' => -7.2575, 'lon' => 112.7521],
        'Pontianak' => ['lat' => -0.0263, 'lon' => 109.3425],
        'Banjarmasin' => ['lat' => -3.3194, 'lon' => 114.5908],
        'Samarinda' => ['lat' => -0.5022, 'lon' => 117.1536],
        'Makassar' => ['lat' => -5.1477, 'lon' => 119.4327],
        'Manado' => ['lat' => 1.4748, 'lon' => 124.8421],
        'Palu' => ['lat' => -0.8999, 'lon' => 119.8707],
        'Denpasar' => ['lat' => -8.6705, 'lon' => 115.2126],
        'Mataram' => ['lat' => -8.5833, 'lon' => 116.1167],
        'Jayapura' => ['lat' => -2.5333, 'lon' => 140.7167],
        'Ambon' => ['lat' => -3.6954, 'lon' => 128.1814],
    ],

    'update_interval' => env('WEATHER_UPDATE_INTERVAL', 3600), // in seconds
];
