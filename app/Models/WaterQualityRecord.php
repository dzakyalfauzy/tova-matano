<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterQualityRecord extends Model
{
    protected $fillable = ['recorded_at', 'ph', 'clarity_cm', 'notes', 'recorded_by'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
