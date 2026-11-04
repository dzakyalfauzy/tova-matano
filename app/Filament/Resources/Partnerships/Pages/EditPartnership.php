<?php

namespace App\Filament\Resources\Partnerships\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Partnerships\PartnershipResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPartnership extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = PartnershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
