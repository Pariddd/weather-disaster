<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DisasterPredictionService;
use App\Models\DisasterPrediction;
use Illuminate\Http\Request;

class PredictionApiController extends Controller
{
    public function __construct(private DisasterPredictionService $predictionService) {}

    public function summary()
    {
        $summary = $this->predictionService->getRiskSummary();

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    public function heatmap()
    {
        $predictions = DisasterPrediction::with('city')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('disaster_predictions')
                    ->groupBy('city');
            })
            ->get();

        $heatmapData = $predictions->map(function ($prediction) {
            return [
                'lat' => $prediction->latitude,
                'lon' => $prediction->longitude,
                'intensity' => $prediction->disaster_probability,
                'risk_level' => $prediction->risk_level,
                'city' => $prediction->city,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $heatmapData
        ]);
    }
}
