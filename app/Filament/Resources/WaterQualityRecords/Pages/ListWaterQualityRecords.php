<?php

namespace App\Filament\Resources\WaterQualityRecords\Pages;

use App\Filament\Resources\WaterQualityRecords\WaterQualityRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWaterQualityRecords extends ListRecords
{
    protected static string $resource = WaterQualityRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
