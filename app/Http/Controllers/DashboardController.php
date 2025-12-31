<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Services\DisasterPredictionService;
use App\Models\City;
use App\Models\DisasterPrediction;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private WeatherService $weatherService,
        private DisasterPredictionService $predictionService
    ) {}

    public function index()
    {
        $cities = City::active()->get();

        $predictions = DisasterPrediction::with('city')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('disaster_predictions')
                    ->groupBy('city');
            })
            ->orderBy('disaster_probability', 'desc')
            ->get();

        $riskSummary = $this->predictionService->getRiskSummary();

        $featuredCities = ['Jakarta', 'Surabaya', 'Medan', 'Bandung', 'Makassar'];
        $weatherData = WeatherData::whereIn('city', $featuredCities)
            ->latestByCity()
            ->get();

        return view('dashboard', compact('cities', 'predictions', 'riskSummary', 'weatherData'));
    }

    public function map()
    {
        $cities = City::active()
            ->with(['latestWeather', 'latestPrediction'])
            ->get();

        $mapData = $cities->map(function ($city) {
            return [
                'name' => $city->name,
                'lat' => $city->latitude,
                'lon' => $city->longitude,
                'risk_level' => $city->latestPrediction?->risk_level ?? 'Low',
                'risk_color' => $city->latestPrediction?->risk_color ?? 'green',
                'probability' => $city->latestPrediction?->disaster_probability ?? 0,
                'temperature' => $city->latestWeather?->temperature ?? 0,
                'rainfall' => $city->latestWeather?->rainfall ?? 0,
            ];
        });

        return view('map', compact('mapData'));
    }
}
