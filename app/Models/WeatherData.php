<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'latitude',
        'longitude',
        'temperature',
        'feels_like',
        'humidity',
        'pressure',
        'wind_speed',
        'wind_deg',
        'rainfall',
        'clouds',
        'weather_main',
        'weather_description',
        'weather_icon',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'temperature' => 'decimal:2',
        'feels_like' => 'decimal:2',
        'wind_speed' => 'decimal:2',
        'rainfall' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function predictions()
    {
        return $this->hasMany(DisasterPrediction::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city', 'name');
    }

    public function scopeLatestByCity($query)
    {
        return $query->whereIn('id', function ($subquery) {
            $subquery->selectRaw('MAX(id)')
                ->from('weather_data')
                ->groupBy('city');
        });
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('recorded_at', [$from, $to]);
    }
}
