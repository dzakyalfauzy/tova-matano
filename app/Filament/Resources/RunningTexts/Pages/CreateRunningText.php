<?php

namespace App\Filament\Resources\RunningTexts\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\RunningTexts\RunningTextResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRunningText extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = RunningTextResource::class;
}
