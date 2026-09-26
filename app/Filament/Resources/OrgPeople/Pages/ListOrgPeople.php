<?php

namespace App\Filament\Resources\OrgPeople\Pages;

use App\Filament\Resources\OrgPeople\OrgPersonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrgPeople extends ListRecords
{
    protected static string $resource = OrgPersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
