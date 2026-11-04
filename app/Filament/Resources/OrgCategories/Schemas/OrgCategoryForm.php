<?php

namespace App\Filament\Resources\OrgCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrgCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori Organisasi')
                    ->description('Nama, deskripsi, dan tipe tampilan kategori organisasi.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        Select::make('display_type')
                            ->label('Tipe Tampilan')
                            ->options(['ROLE_LIST' => 'Daftar Jabatan', 'PERSON_GRID' => 'Grid Personel'])
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
