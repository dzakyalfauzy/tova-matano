<?php

namespace App\Filament\Resources\OrgCategories;

use App\Filament\Resources\OrgCategories\Pages\CreateOrgCategory;
use App\Filament\Resources\OrgCategories\Pages\EditOrgCategory;
use App\Filament\Resources\OrgCategories\Pages\ListOrgCategories;
use App\Filament\Resources\OrgCategories\Schemas\OrgCategoryForm;
use App\Filament\Resources\OrgCategories\Tables\OrgCategoriesTable;
use App\Models\OrgCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrgCategoryResource extends Resource
{
    protected static ?string $model = OrgCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?string $navigationLabel = 'Kategori Organisasi';

    protected static ?string $modelLabel = 'Kategori Organisasi';

    protected static ?string $pluralModelLabel = 'Kategori Organisasi';

    protected static ?int $navigationSort = 80;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrgCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrgCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrgCategories::route('/'),
            'create' => CreateOrgCategory::route('/create'),
            'edit' => EditOrgCategory::route('/{record}/edit'),
        ];
    }
}
