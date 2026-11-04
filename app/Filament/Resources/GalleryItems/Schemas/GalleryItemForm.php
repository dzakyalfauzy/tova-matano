<?php

namespace App\Filament\Resources\GalleryItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class GalleryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Galeri')
                    ->description('Pilih tipe item, lalu isi foto atau URL video sesuai tipenya.')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->label('Tipe')
                            ->options(['PHOTO' => 'Foto', 'VIDEO' => 'Video'])
                            ->helperText('Tipe menentukan media yang perlu diisi.')
                            ->live()
                            ->required(),
                        FileUpload::make('url')
                            ->label('Upload Foto')
                            ->image()
                            ->disk('public')
                            ->directory('gallery')
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('type') === 'PHOTO')
                            ->required(fn (Get $get) => $get('type') === 'PHOTO'),
                        TextInput::make('url')
                            ->label('URL Video')
                            ->url()
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('type') === 'VIDEO')
                            ->required(fn (Get $get) => $get('type') === 'VIDEO'),
                        TextInput::make('caption')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
