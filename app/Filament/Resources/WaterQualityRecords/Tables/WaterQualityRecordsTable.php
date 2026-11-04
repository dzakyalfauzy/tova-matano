<?php

namespace App\Filament\Resources\WaterQualityRecords\Tables;

use App\Filament\Exports\WaterQualityRecordExporter;
use App\Models\WaterSamplingPoint;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WaterQualityRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recorded_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('samplingPoint.name')
                    ->label('Titik Sample')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tss')
                    ->label('TSS (mg/L)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ph')
                    ->label('pH')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tds')
                    ->label('TDS (mg/L)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dissolved_oxygen')
                    ->label('DO (mg/L)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('temperature')
                    ->label('Suhu (°C)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('clarity_cm')
                    ->label('Kejernihan (cm)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('recorded_at', 'desc')
            ->filters([
                SelectFilter::make('water_sampling_point_id')
                    ->label('Titik Pengambilan Sample')
                    ->options(
                        WaterSamplingPoint::where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->placeholder('Semua Titik'),
                Filter::make('recorded_at')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->native(false),
                        DatePicker::make('until')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('recorded_at', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('recorded_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Dari: '.Carbon::parse($data['from'])->format('d M Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Sampai: '.Carbon::parse($data['until'])->format('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(WaterQualityRecordExporter::class),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
