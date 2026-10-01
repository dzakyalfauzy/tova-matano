<?php

namespace App\Filament\Exports;

use App\Models\WaterQualityRecord;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Str;

class WaterQualityRecordExporter extends Exporter
{
    protected static ?string $model = WaterQualityRecord::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('recorded_at'),
            ExportColumn::make('samplingPoint.name')->label('Titik Sample'),
            ExportColumn::make('ph'),
            ExportColumn::make('clarity_cm')->label('Kejernihan (cm)'),
            ExportColumn::make('notes')->label('Catatan'),
            ExportColumn::make('recorder.name')->label('Diukur Oleh'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your water quality record export has completed and ' . Str::of('row')->counted($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Str::of('row')->counted($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
