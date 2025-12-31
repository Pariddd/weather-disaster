<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisasterAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'disaster_prediction_id',
        'city',
        'alert_level',
        'title',
        'message',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function prediction()
    {
        return $this->belongsTo(DisasterPrediction::class, 'disaster_prediction_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getAlertColorAttribute(): string
    {
        return match ($this->alert_level) {
            'Warning' => 'yellow',
            'Alert' => 'orange',
            'Emergency' => 'red',
            default => 'gray',
        };
    }
}
