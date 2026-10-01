<?php

namespace App\Filament\Resources\WaterQualityRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WaterQualityRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recorded_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('samplingPoint.name')
                    ->label('Titik Sample')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ph')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('clarity_cm')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('recorded_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\ExportAction::make()
                    ->exporter(\App\Filament\Exports\WaterQualityRecordExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
