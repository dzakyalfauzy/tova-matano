<?php

namespace App\Filament\Resources\RunningTexts;

use App\Filament\Resources\RunningTexts\Pages\CreateRunningText;
use App\Filament\Resources\RunningTexts\Pages\EditRunningText;
use App\Filament\Resources\RunningTexts\Pages\ListRunningTexts;
use App\Filament\Resources\RunningTexts\Schemas\RunningTextForm;
use App\Filament\Resources\RunningTexts\Tables\RunningTextsTable;
use App\Models\RunningText;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RunningTextResource extends Resource
{
    protected static ?string $model = RunningText::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'text';

    public static function form(Schema $schema): Schema
    {
        return RunningTextForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RunningTextsTable::configure($table);
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
            'index' => ListRunningTexts::route('/'),
            'create' => CreateRunningText::route('/create'),
            'edit' => EditRunningText::route('/{record}/edit'),
        ];
    }
}
