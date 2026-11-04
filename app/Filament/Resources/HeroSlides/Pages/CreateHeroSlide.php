<?php

namespace App\Filament\Resources\HeroSlides\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\HeroSlides\HeroSlideResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHeroSlide extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = HeroSlideResource::class;
}
