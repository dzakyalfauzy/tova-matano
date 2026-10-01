<?php

namespace App\Filament\Resources\WaterQualityRecords;

use App\Filament\Resources\WaterQualityRecords\Pages\CreateWaterQualityRecord;
use App\Filament\Resources\WaterQualityRecords\Pages\EditWaterQualityRecord;
use App\Filament\Resources\WaterQualityRecords\Pages\ListWaterQualityRecords;
use App\Filament\Resources\WaterQualityRecords\Schemas\WaterQualityRecordForm;
use App\Filament\Resources\WaterQualityRecords\Tables\WaterQualityRecordsTable;
use App\Models\WaterQualityRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WaterQualityRecordResource extends Resource
{
    protected static ?string $model = WaterQualityRecord::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-beaker';
    protected static \UnitEnum|string|null $navigationGroup = 'Kualitas Air';
    protected static ?string $modelLabel = 'Catatan Kualitas Air';
    protected static ?string $pluralModelLabel = 'Catatan Kualitas Air';

    public static function form(Schema $schema): Schema
    {
        return WaterQualityRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WaterQualityRecordsTable::configure($table);
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
            'index' => ListWaterQualityRecords::route('/'),
            'create' => CreateWaterQualityRecord::route('/create'),
            'edit' => EditWaterQualityRecord::route('/{record}/edit'),
        ];
    }
}
