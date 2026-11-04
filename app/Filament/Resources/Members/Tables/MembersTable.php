<?php

namespace App\Filament\Resources\Members\Tables;

use App\Filament\Exports\MemberExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('divisi_name')
                    ->label('Divisi')
                    ->searchable(),
                TextColumn::make('points')
                    ->label('Poin')
                    ->badge()
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
            ])
            ->defaultSort('points', 'desc')
            ->filters([
                TernaryFilter::make('is_featured')
                    ->label('Anggota Unggulan')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(MemberExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
