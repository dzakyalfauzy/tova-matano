<?php

namespace App\Filament\Resources\WaterSamplingPoints\Pages;

use App\Filament\Resources\WaterSamplingPoints\WaterSamplingPointResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWaterSamplingPoint extends EditRecord
{
    protected static string $resource = WaterSamplingPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
