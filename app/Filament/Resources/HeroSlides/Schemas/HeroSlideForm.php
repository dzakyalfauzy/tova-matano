<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_url')
                    ->image()
                    ->disk('public')
                    ->directory('hero-slides')
                    ->required(),
                TextInput::make('title')
                    ->nullable(),
                TextInput::make('subtitle'),
                TextInput::make('cta_label'),
                TextInput::make('cta_url')
                    ->url(),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
