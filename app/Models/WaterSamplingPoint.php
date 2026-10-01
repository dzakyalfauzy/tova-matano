<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterSamplingPoint extends Model
{
    protected $fillable = ['name', 'description', 'coordinates', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function records()
    {
        return $this->hasMany(WaterQualityRecord::class);
    }
}
