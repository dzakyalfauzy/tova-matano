<?php

namespace App\Filament\Resources\WaterQualityResearchReferences\Pages;

use App\Filament\Resources\WaterQualityResearchReferences\WaterQualityResearchReferenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWaterQualityResearchReferences extends ListRecords
{
    protected static string $resource = WaterQualityResearchReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
