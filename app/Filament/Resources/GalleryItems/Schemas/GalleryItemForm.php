<?php

namespace App\Filament\Resources\GalleryItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Get;

class GalleryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(['PHOTO' => 'Photo', 'VIDEO' => 'Video'])
                    ->live()
                    ->required(),
                FileUpload::make('url')
                    ->image()
                    ->disk('public')
                    ->directory('gallery')
                    ->visible(fn (Get $get) => $get('type') === 'PHOTO')
                    ->required(fn (Get $get) => $get('type') === 'PHOTO'),
                TextInput::make('url')
                    ->url()
                    ->visible(fn (Get $get) => $get('type') === 'VIDEO')
                    ->required(fn (Get $get) => $get('type') === 'VIDEO'),
                TextInput::make('caption'),
            ]);
    }
}
