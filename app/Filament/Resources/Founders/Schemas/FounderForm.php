<?php

namespace App\Filament\Resources\Founders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FounderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('role')
                    ->required(),
                TextInput::make('org_name'),
                FileUpload::make('photo_url')
                    ->image()
                    ->disk('public')
                    ->directory('founders'),
            ]);
    }
}
