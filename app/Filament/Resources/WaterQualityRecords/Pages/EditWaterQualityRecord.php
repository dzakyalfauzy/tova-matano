<?php

namespace App\Filament\Resources\WaterQualityRecords\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\WaterQualityRecords\WaterQualityRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWaterQualityRecord extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = WaterQualityRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
