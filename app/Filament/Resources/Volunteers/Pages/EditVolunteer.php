<?php

namespace App\Filament\Resources\Volunteers\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Volunteers\VolunteerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVolunteer extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = VolunteerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
