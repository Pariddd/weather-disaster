<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class WeatherApiController extends Controller
{
    public function __construct(private WeatherService $weatherService) {}

    public function current(Request $request)
    {
        $city = $request->input('city');

        $weather = $this->weatherService->getCurrentWeather($city);

        if ($weather) {
            return response()->json([
                'success' => true,
                'data' => $weather
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch weather data'
        ], 500);
    }

    public function latest()
    {
        $latestWeather = WeatherData::latestByCity()
            ->orderBy('city')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $latestWeather
        ]);
    }
}
