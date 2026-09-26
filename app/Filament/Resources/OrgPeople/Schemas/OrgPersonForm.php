<?php

namespace App\Filament\Resources\OrgPeople\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrgPersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('org_category_id')
                    ->relationship('orgCategory', 'name')
                    ->required(),
                TextInput::make('name'),
                TextInput::make('role_title')
                    ->required(),
                FileUpload::make('photo_url')
                    ->image()
                    ->disk('public')
                    ->directory('org-people'),
                TextInput::make('divisi_name'),
            ]);
    }
}
