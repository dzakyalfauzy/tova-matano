<?php

namespace App\Filament\Resources\WaterQualityRecords\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WaterQualityRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengukuran')
                    ->description('Titik sampling dan waktu pengambilan sampel.')
                    ->columns(3)
                    ->schema([
                        Select::make('water_sampling_point_id')
                            ->label('Titik Sampling')
                            ->relationship('samplingPoint', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('recorded_at')
                            ->label('Tanggal Pengukuran')
                            ->default(now())
                            ->required(),
                        TextInput::make('recorded_by')
                            ->label('Diukur Oleh (ID Pengguna)')
                            ->numeric(),
                    ]),

                Section::make('Parameter Kualitas Air')
                    ->description('Kosongkan parameter yang tidak diukur. Catatan historis lama tetap valid tanpa nilai pada parameter baru.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('tss')
                            ->label('TSS (mg/L)')
                            ->helperText('Total Suspended Solids')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.1),
                        TextInput::make('ph')
                            ->label('pH')
                            ->helperText('Rentang valid alat: 0–14')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(14)
                            ->step(0.01),
                        TextInput::make('tds')
                            ->label('TDS (mg/L)')
                            ->helperText('Total Dissolved Solids')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.1),
                        TextInput::make('dissolved_oxygen')
                            ->label('DO (mg/L)')
                            ->helperText('Dissolved Oxygen')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01),
                        TextInput::make('temperature')
                            ->label('Suhu (°C)')
                            ->helperText('Suhu air saat pengukuran')
                            ->numeric()
                            ->minValue(-5)
                            ->maxValue(60)
                            ->step(0.1),
                        TextInput::make('clarity_cm')
                            ->label('Kejernihan (cm)')
                            ->helperText('Kecerahan Secchi; 100 cm = 1 m')
                            ->numeric()
                            ->minValue(0)
                            ->step(1),
                    ]),

                Section::make('Catatan')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
