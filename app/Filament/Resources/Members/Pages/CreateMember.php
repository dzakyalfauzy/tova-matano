<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Members\MemberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMember extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = MemberResource::class;
}
