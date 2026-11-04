<?php

namespace App\Filament\Resources\Founders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FounderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil Pendiri')
                    ->description('Data pendiri yang ditampilkan pada situs.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        TextInput::make('role')
                            ->label('Jabatan')
                            ->required(),
                        TextInput::make('org_name')
                            ->label('Organisasi'),
                        FileUpload::make('photo_url')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('founders')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
