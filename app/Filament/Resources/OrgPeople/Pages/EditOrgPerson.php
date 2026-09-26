<?php

namespace App\Filament\Resources\OrgPeople\Pages;

use App\Filament\Resources\OrgPeople\OrgPersonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrgPerson extends EditRecord
{
    protected static string $resource = OrgPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
