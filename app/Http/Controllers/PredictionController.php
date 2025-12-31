<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Services\DisasterPredictionService;
use App\Models\City;
use App\Models\DisasterPrediction;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function __construct(
        private WeatherService $weatherService,
        private DisasterPredictionService $predictionService
    ) {}

    public function index()
    {
        $predictions = DisasterPrediction::with('city')
            ->latest('predicted_at')
            ->paginate(20);

        return view('predictions.index', compact('predictions'));
    }

    public function predict(Request $request)
    {
        $validated = $request->validate([
            'city' => 'required|string|exists:cities,name'
        ]);

        $city = $validated['city'];
        $cityData = City::where('name', $city)->firstOrFail();

        $weather = $this->weatherService->getCurrentWeather(
            $city,
            $cityData->latitude,
            $cityData->longitude
        );

        if (!$weather) {
            return back()->with('error', 'Failed to fetch weather data');
        }

        $weatherData = $this->weatherService->storeWeatherData($weather);

        $historical = $this->weatherService->getHistoricalRainfall($city);

        $prediction = $this->predictionService->predictDisaster($weatherData, $historical);

        if ($prediction) {
            return redirect()
                ->route('predictions.show', $prediction->id)
                ->with('success', 'Prediction generated successfully');
        }

        return back()->with('error', 'Failed to generate prediction');
    }

    public function show($id)
    {
        $prediction = DisasterPrediction::with(['weatherData', 'city'])
            ->findOrFail($id);

        return view('predictions.show', compact('prediction'));
    }

    public function predictAll()
    {
        $predictions = $this->predictionService->getPredictionsForAllCities();

        return response()->json([
            'success' => true,
            'count' => count($predictions),
            'predictions' => $predictions
        ]);
    }

    public function highRisk()
    {
        $predictions = DisasterPrediction::highRisk()
            ->with('city')
            ->latest('predicted_at')
            ->get();

        return view('predictions.high-risk', compact('predictions'));
    }

    public function cityPredictions($city)
    {
        $cityData = City::where('name', $city)->firstOrFail();

        $predictions = DisasterPrediction::where('city', $city)
            ->latest('predicted_at')
            ->paginate(10);

        return view('predictions.city', compact('cityData', 'predictions'));
    }
}
