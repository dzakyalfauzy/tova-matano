<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Members\MemberResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMember extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
