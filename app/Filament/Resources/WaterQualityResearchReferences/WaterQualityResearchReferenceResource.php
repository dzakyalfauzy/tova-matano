<?php

namespace App\Filament\Resources\WaterQualityResearchReferences;

use App\Filament\Resources\WaterQualityResearchReferences\Pages\CreateWaterQualityResearchReference;
use App\Filament\Resources\WaterQualityResearchReferences\Pages\EditWaterQualityResearchReference;
use App\Filament\Resources\WaterQualityResearchReferences\Pages\ListWaterQualityResearchReferences;
use App\Filament\Resources\WaterQualityResearchReferences\Schemas\WaterQualityResearchReferenceForm;
use App\Filament\Resources\WaterQualityResearchReferences\Tables\WaterQualityResearchReferencesTable;
use App\Models\WaterQualityResearchReference;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WaterQualityResearchReferenceResource extends Resource
{
    protected static ?string $model = WaterQualityResearchReference::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|\UnitEnum|null $navigationGroup = 'Lingkungan & Monitoring';

    protected static ?string $navigationLabel = 'Referensi Riset Kualitas Air';

    protected static ?string $modelLabel = 'Referensi Riset';

    protected static ?string $pluralModelLabel = 'Referensi Riset Kualitas Air';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return WaterQualityResearchReferenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WaterQualityResearchReferencesTable::configure($table);
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
            'index' => ListWaterQualityResearchReferences::route('/'),
            'create' => CreateWaterQualityResearchReference::route('/create'),
            'edit' => EditWaterQualityResearchReference::route('/{record}/edit'),
        ];
    }
}
