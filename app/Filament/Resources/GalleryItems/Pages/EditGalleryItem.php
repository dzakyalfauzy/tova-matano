<?php

namespace App\Filament\Resources\GalleryItems\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\GalleryItems\GalleryItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGalleryItem extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = GalleryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
