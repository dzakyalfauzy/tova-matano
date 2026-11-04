<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Program')
                    ->description('Data program yang ditampilkan pada situs.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->helperText('Slug dibuat otomatis dari nama saat data baru dibuat.')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Alamat unik program pada URL.')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('icon_url')
                            ->label('Ikon')
                            ->image()
                            ->disk('public')
                            ->directory('programs')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
