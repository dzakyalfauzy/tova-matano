<?php

namespace App\Filament\Resources\OrgPeople\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\OrgPeople\OrgPersonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrgPerson extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = OrgPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
