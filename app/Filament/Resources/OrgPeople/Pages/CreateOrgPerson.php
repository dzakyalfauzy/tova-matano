<?php

namespace App\Filament\Resources\OrgPeople\Pages;

use App\Filament\Resources\OrgPeople\OrgPersonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrgPerson extends CreateRecord
{
    protected static string $resource = OrgPersonResource::class;
}
