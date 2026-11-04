<?php

namespace App\Filament\Resources\RunningTexts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RunningTextForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Teks Berjalan')
                    ->description('Teks yang ditampilkan bergerak pada situs.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('text')
                            ->label('Teks')
                            ->required()
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Aktifkan agar teks berjalan tampil di situs.')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}
