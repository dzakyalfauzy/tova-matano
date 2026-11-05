<?php

namespace App\Filament\Resources\WaterQualityResearchReferences\Pages;

use App\Filament\Resources\WaterQualityResearchReferences\WaterQualityResearchReferenceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWaterQualityResearchReference extends EditRecord
{
    protected static string $resource = WaterQualityResearchReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
