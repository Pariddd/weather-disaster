<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'province',
        'latitude',
        'longitude',
        'elevation',
        'flood_prone_index',
        'landslide_prone_index',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'flood_prone_index' => 'decimal:2',
        'landslide_prone_index' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function weatherData()
    {
        return $this->hasMany(WeatherData::class, 'city', 'name');
    }

    public function predictions()
    {
        return $this->hasMany(DisasterPrediction::class, 'city', 'name');
    }

    public function latestWeather()
    {
        return $this->hasOne(WeatherData::class, 'city', 'name')
            ->latestOfMany('recorded_at');
    }

    public function latestPrediction()
    {
        return $this->hasOne(DisasterPrediction::class, 'city', 'name')
            ->latestOfMany('predicted_at');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isHighRiskArea(): bool
    {
        return $this->flood_prone_index > 0.7 || $this->landslide_prone_index > 0.7;
    }
}
