<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterQualityRecord extends Model
{
    protected $fillable = ['recorded_at', 'ph', 'clarity_cm', 'notes', 'recorded_by', 'water_sampling_point_id'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function samplingPoint(): BelongsTo
    {
        return $this->belongsTo(WaterSamplingPoint::class, 'water_sampling_point_id');
    }
}
