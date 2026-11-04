<?php

namespace App\Filament\Resources\WaterSamplingPoints\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\WaterSamplingPoints\WaterSamplingPointResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWaterSamplingPoint extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = WaterSamplingPointResource::class;
}
