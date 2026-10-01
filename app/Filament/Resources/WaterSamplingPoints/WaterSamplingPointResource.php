<?php

namespace App\Filament\Resources\WaterSamplingPoints;

use App\Filament\Resources\WaterSamplingPoints\Pages\CreateWaterSamplingPoint;
use App\Filament\Resources\WaterSamplingPoints\Pages\EditWaterSamplingPoint;
use App\Filament\Resources\WaterSamplingPoints\Pages\ListWaterSamplingPoints;
use App\Filament\Resources\WaterSamplingPoints\Schemas\WaterSamplingPointForm;
use App\Filament\Resources\WaterSamplingPoints\Tables\WaterSamplingPointsTable;
use App\Models\WaterSamplingPoint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WaterSamplingPointResource extends Resource
{
    protected static ?string $model = WaterSamplingPoint::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-map-pin';
    protected static \UnitEnum|string|null $navigationGroup = 'Kualitas Air';
    protected static ?string $modelLabel = 'Titik Sample';
    protected static ?string $pluralModelLabel = 'Titik Sample';

    public static function form(Schema $schema): Schema
    {
        return WaterSamplingPointForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WaterSamplingPointsTable::configure($table);
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
            'index' => ListWaterSamplingPoints::route('/'),
            'create' => CreateWaterSamplingPoint::route('/create'),
            'edit' => EditWaterSamplingPoint::route('/{record}/edit'),
        ];
    }
}
