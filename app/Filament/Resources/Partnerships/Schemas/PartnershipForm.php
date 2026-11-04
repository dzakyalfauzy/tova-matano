<?php

namespace App\Filament\Resources\Partnerships\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Mitra')
                    ->description('Data mitra, logo, dan pengaturan tampilannya.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'partner' => 'Partner',
                                'sponsor' => 'Sponsor',
                                'media' => 'Media',
                            ])
                            ->required()
                            ->default('partner'),
                        FileUpload::make('logo_url')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('partnerships')
                            ->columnSpanFull(),
                        TextInput::make('website_url')
                            ->label('Situs Web')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('order')
                            ->label('Urutan')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Aktifkan agar mitra tampil di situs.')
                            ->required()
                            ->default(true),
                    ]),
            ]);
    }
}
