<?php

namespace App\Filament\Resources\Partnerships;

use App\Filament\Resources\Partnerships\Pages\CreatePartnership;
use App\Filament\Resources\Partnerships\Pages\EditPartnership;
use App\Filament\Resources\Partnerships\Pages\ListPartnerships;
use App\Filament\Resources\Partnerships\Schemas\PartnershipForm;
use App\Filament\Resources\Partnerships\Tables\PartnershipsTable;
use App\Models\Partnership;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PartnershipResource extends Resource
{
    protected static ?string $model = Partnership::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-hand-raised';
    
    protected static \UnitEnum|string|null $navigationGroup = 'Konten Website';

    public static function form(Schema $schema): Schema
    {
        return PartnershipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnershipsTable::configure($table);
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
            'index' => ListPartnerships::route('/'),
            'create' => CreatePartnership::route('/create'),
            'edit' => EditPartnership::route('/{record}/edit'),
        ];
    }
}
