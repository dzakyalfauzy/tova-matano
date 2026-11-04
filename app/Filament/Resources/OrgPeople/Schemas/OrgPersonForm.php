<?php

namespace App\Filament\Resources\OrgPeople\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrgPersonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personel Organisasi')
                    ->description('Data personel beserta kategori dan jabatannya.')
                    ->columns(2)
                    ->schema([
                        Select::make('org_category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->required(),
                        TextInput::make('name')
                            ->label('Nama'),
                        TextInput::make('role_title')
                            ->label('Jabatan')
                            ->required(),
                        TextInput::make('divisi_name')
                            ->label('Divisi'),
                        FileUpload::make('photo_url')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('org-people')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
