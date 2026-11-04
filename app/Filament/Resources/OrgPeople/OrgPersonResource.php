<?php

namespace App\Filament\Resources\OrgPeople;

use App\Filament\Resources\OrgPeople\Pages\CreateOrgPerson;
use App\Filament\Resources\OrgPeople\Pages\EditOrgPerson;
use App\Filament\Resources\OrgPeople\Pages\ListOrgPeople;
use App\Filament\Resources\OrgPeople\Schemas\OrgPersonForm;
use App\Filament\Resources\OrgPeople\Tables\OrgPeopleTable;
use App\Models\OrgPerson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrgPersonResource extends Resource
{
    protected static ?string $model = OrgPerson::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?string $navigationLabel = 'Personel Organisasi';

    protected static ?string $modelLabel = 'Personel Organisasi';

    protected static ?string $pluralModelLabel = 'Personel Organisasi';

    protected static ?int $navigationSort = 90;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OrgPersonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrgPeopleTable::configure($table);
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
            'index' => ListOrgPeople::route('/'),
            'create' => CreateOrgPerson::route('/create'),
            'edit' => EditOrgPerson::route('/{record}/edit'),
        ];
    }
}
