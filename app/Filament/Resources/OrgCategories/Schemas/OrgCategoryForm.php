<?php

namespace App\Filament\Resources\OrgCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrgCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('description'),
                Select::make('display_type')
                    ->options(['ROLE_LIST' => 'Role List', 'PERSON_GRID' => 'Person Grid'])
                    ->required(),
            ]);
    }
}
