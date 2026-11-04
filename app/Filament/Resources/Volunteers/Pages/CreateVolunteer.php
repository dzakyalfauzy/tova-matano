<?php

namespace App\Filament\Resources\Volunteers\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Volunteers\VolunteerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVolunteer extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = VolunteerResource::class;
}
