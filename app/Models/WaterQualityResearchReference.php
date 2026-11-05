<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterQualityResearchReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'title',
        'authors',
        'publication_year',
        'journal_or_institution',
        'parameter',
        'reported_minimum',
        'reported_maximum',
        'unit',
        'sampling_location',
        'sampling_period',
        'sampling_depth',
        'measurement_method',
        'research_type',
        'source_url_or_doi',
        'verification_status',
        'is_active_for_comparison',
        'interpretation_notes',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'reported_minimum' => 'float',
        'reported_maximum' => 'float',
        'is_active_for_comparison' => 'boolean',
    ];

    /**
     * Scope for references available for automated public comparison.
     */
    public function scopeActiveForComparison(Builder $query): Builder
    {
        return $query
            ->where('is_active_for_comparison', true)
            ->where('verification_status', 'verified');
    }

    /**
     * Scope for a specific parameter.
     */
    public function scopeForParameter(Builder $query, string $parameter): Builder
    {
        return $query->where('parameter', $parameter);
    }
}
