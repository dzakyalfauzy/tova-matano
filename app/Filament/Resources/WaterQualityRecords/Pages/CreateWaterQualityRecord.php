<?php

namespace App\Filament\Resources\WaterQualityRecords\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\WaterQualityRecords\WaterQualityRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWaterQualityRecord extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = WaterQualityRecordResource::class;
}
