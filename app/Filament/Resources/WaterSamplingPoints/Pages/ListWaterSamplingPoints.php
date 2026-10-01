<?php

namespace App\Filament\Resources\WaterSamplingPoints\Pages;

use App\Filament\Resources\WaterSamplingPoints\WaterSamplingPointResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWaterSamplingPoints extends ListRecords
{
    protected static string $resource = WaterSamplingPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
