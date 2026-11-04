<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Activities\ActivityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = ActivityResource::class;
}
