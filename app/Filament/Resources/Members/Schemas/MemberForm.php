<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                \Filament\Forms\Components\FileUpload::make('photo_url')
                    ->image()
                    ->disk('public')
                    ->directory('members'),
                TextInput::make('divisi_name'),
                TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('message')
                    ->rows(3)
                    ->columnSpanFull(),
                DateTimePicker::make('joined_at')
                    ->default(now())
                    ->required(),
                Toggle::make('is_featured')
                    ->default(true)
                    ->required(),
            ]);
    }
}
