<?php

namespace App\Filament\Resources\WaterQualityResearchReferences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WaterQualityResearchReferencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source_id')
                    ->label('Source ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parameter')
                    ->label('Parameter')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->sortable(),
                TextColumn::make('authors')
                    ->label('Peneliti / Penulis')
                    ->searchable()
                    ->limit(25),
                TextColumn::make('publication_year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('reported_range')
                    ->label('Rentang Observasi')
                    ->state(fn ($record) => "{$record->reported_minimum} – {$record->reported_maximum} {$record->unit}"),
                TextColumn::make('research_type')
                    ->label('Tipe Riset')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'laboratory' => 'info',
                        'field' => 'success',
                        'remote_sensing' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('verification_status')
                    ->label('Verifikasi')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'verified' => 'success',
                        'provisional' => 'warning',
                        'unverified' => 'danger',
                        default => 'gray',
                    }),
                IconColumn::make('is_active_for_comparison')
                    ->label('Aktif Komparasi')
                    ->boolean(),
            ])
            ->defaultSort('parameter', 'asc')
            ->filters([
                SelectFilter::make('parameter')
                    ->options([
                        'tss' => 'TSS',
                        'ph' => 'pH',
                        'tds' => 'TDS',
                        'dissolved_oxygen' => 'DO',
                        'temperature' => 'Suhu',
                        'clarity_cm' => 'Kejernihan',
                    ]),
                SelectFilter::make('verification_status')
                    ->options([
                        'verified' => 'Verified',
                        'provisional' => 'Provisional',
                        'unverified' => 'Unverified',
                    ]),
                SelectFilter::make('research_type')
                    ->options([
                        'field' => 'Field Measurement',
                        'laboratory' => 'Laboratory',
                        'remote_sensing' => 'Remote Sensing',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
