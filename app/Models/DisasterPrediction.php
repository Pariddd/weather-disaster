<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisasterPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'weather_data_id',
        'city',
        'latitude',
        'longitude',
        'disaster_predicted',
        'disaster_probability',
        'safe_probability',
        'risk_level',
        'disaster_type',
        'warnings',
        'input_features',
        'predicted_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'disaster_predicted' => 'boolean',
        'disaster_probability' => 'decimal:4',
        'safe_probability' => 'decimal:4',
        'warnings' => 'array',
        'input_features' => 'array',
        'predicted_at' => 'datetime',
    ];

    public function weatherData()
    {
        return $this->belongsTo(WeatherData::class);
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['High', 'Critical']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('predicted_at', today());
    }

    public function isCritical(): bool
    {
        return $this->risk_level === 'Critical';
    }

    public function getRiskColorAttribute(): string
    {
        return match ($this->risk_level) {
            'Low' => 'green',
            'Medium' => 'yellow',
            'High' => 'orange',
            'Critical' => 'red',
            default => 'gray',
        };
    }
}
