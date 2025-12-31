<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Models\City;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(private WeatherService $weatherService) {}

    public function index()
    {
        $cities = City::active()->get();
        return view('weather.index', compact('cities'));
    }

    public function show($city)
    {
        $cityData = City::where('name', $city)->firstOrFail();

        $currentWeather = $this->weatherService->getCurrentWeather(
            $city,
            $cityData->latitude,
            $cityData->longitude
        );

        $forecast = $this->weatherService->getForecast(
            $city,
            $cityData->latitude,
            $cityData->longitude
        );

        $historicalData = WeatherData::where('city', $city)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at', 'asc')
            ->get();

        return view('weather.show', compact('cityData', 'currentWeather', 'forecast', 'historicalData'));
    }

    public function refresh(Request $request)
    {
        $city = $request->input('city');
        $cityData = City::where('name', $city)->first();

        if (!$cityData) {
            return response()->json(['error' => 'City not found'], 404);
        }

        $weather = $this->weatherService->getCurrentWeather(
            $city,
            $cityData->latitude,
            $cityData->longitude
        );

        if ($weather) {
            $weatherData = $this->weatherService->storeWeatherData($weather);
            return response()->json([
                'success' => true,
                'data' => $weatherData
            ]);
        }

        return response()->json(['error' => 'Failed to fetch weather'], 500);
    }

    public function historical(Request $request, $city)
    {
        $days = $request->input('days', 7);

        $data = WeatherData::where('city', $city)
            ->where('recorded_at', '>=', now()->subDays($days))
            ->orderBy('recorded_at', 'asc')
            ->get();

        return response()->json($data);
    }
}
