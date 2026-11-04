<?php

namespace App\Filament\Resources\GalleryItems\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\GalleryItems\GalleryItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGalleryItem extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = GalleryItemResource::class;
}
