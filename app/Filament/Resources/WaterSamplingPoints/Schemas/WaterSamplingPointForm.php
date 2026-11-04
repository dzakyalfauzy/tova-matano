<?php

namespace App\Filament\Resources\WaterSamplingPoints\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WaterSamplingPointForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Titik Sampling')
                    ->description('Lokasi pengambilan sampel kualitas air.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('coordinates')
                            ->label('Koordinat')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Aktifkan agar titik sampling dapat dipilih.')
                            ->required()
                            ->default(true),
                    ]),
            ]);
    }
}
