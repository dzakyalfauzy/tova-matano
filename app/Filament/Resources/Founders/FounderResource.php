<?php

namespace App\Filament\Resources\Founders;

use App\Filament\Resources\Founders\Pages\CreateFounder;
use App\Filament\Resources\Founders\Pages\EditFounder;
use App\Filament\Resources\Founders\Pages\ListFounders;
use App\Filament\Resources\Founders\Schemas\FounderForm;
use App\Filament\Resources\Founders\Tables\FoundersTable;
use App\Models\Founder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FounderResource extends Resource
{
    protected static ?string $model = Founder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?string $navigationLabel = 'Pendiri';

    protected static ?string $modelLabel = 'Pendiri';

    protected static ?string $pluralModelLabel = 'Pendiri';

    protected static ?int $navigationSort = 70;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FounderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FoundersTable::configure($table);
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
            'index' => ListFounders::route('/'),
            'create' => CreateFounder::route('/create'),
            'edit' => EditFounder::route('/{record}/edit'),
        ];
    }
}
