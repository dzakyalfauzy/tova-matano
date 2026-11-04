<?php

namespace App\Filament\Resources\Programs\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Programs\ProgramResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProgram extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = ProgramResource::class;
}
