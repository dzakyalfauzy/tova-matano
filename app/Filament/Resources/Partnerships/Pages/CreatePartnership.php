<?php

namespace App\Filament\Resources\Partnerships\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Partnerships\PartnershipResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnership extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = PartnershipResource::class;
}
