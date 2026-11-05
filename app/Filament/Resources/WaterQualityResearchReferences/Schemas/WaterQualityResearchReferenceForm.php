<?php

namespace App\Filament\Resources\WaterQualityResearchReferences\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WaterQualityResearchReferenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Publikasi & Riset')
                    ->description('Data bibliografis dan identitas penelitian ilmiah Danau Matano.')
                    ->columns(3)
                    ->schema([
                        TextInput::make('source_id')
                            ->label('Source ID')
                            ->helperText('Kode unik rujukan, misal: REF-PH-SENTOSA-2017')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('title')
                            ->label('Judul Publikasi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        TextInput::make('authors')
                            ->label('Penulis / Peneliti')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('publication_year')
                            ->label('Tahun Publikasi')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(2100),
                        TextInput::make('journal_or_institution')
                            ->label('Jurnal / Institusi')
                            ->maxLength(255),
                    ]),

                Section::make('Parameter & Rentang Nilai Observasi')
                    ->description('Nilai minimum dan maksimum yang diamati dalam studi.')
                    ->columns(4)
                    ->schema([
                        Select::make('parameter')
                            ->label('Parameter')
                            ->options([
                                'tss' => 'TSS (Total Suspended Solids)',
                                'ph' => 'pH (Potential of Hydrogen)',
                                'tds' => 'TDS (Total Dissolved Solids)',
                                'dissolved_oxygen' => 'DO (Dissolved Oxygen)',
                                'temperature' => 'Suhu Air (Temperature)',
                                'clarity_cm' => 'Kejernihan (Secchi Disk)',
                            ])
                            ->required(),
                        TextInput::make('reported_minimum')
                            ->label('Nilai Minimum')
                            ->numeric()
                            ->step(0.001),
                        TextInput::make('reported_maximum')
                            ->label('Nilai Maksimum')
                            ->numeric()
                            ->step(0.001),
                        TextInput::make('unit')
                            ->label('Satuan')
                            ->required()
                            ->helperText('Contoh: mg/L, pH, °C, cm, mg/m³')
                            ->maxLength(50),
                    ]),

                Section::make('Konteks Pengambilan Sampel & Metodologi')
                    ->description('Detail metodologi agar tidak terjadi perbandingan sembarangan.')
                    ->columns(3)
                    ->schema([
                        Select::make('research_type')
                            ->label('Tipe Penelitian')
                            ->options([
                                'field' => 'Field Measurement (Insitu Lapangan)',
                                'laboratory' => 'Laboratory (Uji Lab)',
                                'remote_sensing' => 'Remote Sensing (Satelit / Citra)',
                            ])
                            ->required()
                            ->default('field'),
                        TextInput::make('sampling_location')
                            ->label('Lokasi Pengambilan Sampel')
                            ->maxLength(255),
                        TextInput::make('sampling_period')
                            ->label('Periode Pengambilan Sampel')
                            ->maxLength(255),
                        TextInput::make('sampling_depth')
                            ->label('Kedalaman Sampel')
                            ->helperText('Misal: Lapisan permukaan (0–5m), kedalaman 100m, atau Tidak spesifik')
                            ->maxLength(255),
                        TextInput::make('measurement_method')
                            ->label('Metode Pengukuran / Alat')
                            ->maxLength(255),
                        TextInput::make('source_url_or_doi')
                            ->label('URL Sumber / DOI')
                            ->url()
                            ->maxLength(500),
                    ]),

                Section::make('Status Verifikasi & Penilaian Otomatis')
                    ->columns(2)
                    ->schema([
                        Select::make('verification_status')
                            ->label('Status Verifikasi')
                            ->options([
                                'verified' => 'Verified (Telah diverifikasi dari publikasi asli)',
                                'provisional' => 'Provisional (Data awal / belum verifikasi penuh)',
                                'unverified' => 'Unverified (Belum diverifikasi)',
                            ])
                            ->required()
                            ->default('provisional')
                            ->helperText('Hanya referensi "Verified" yang diikutsertakan dalam perbandingan otomatis publik.'),
                        Toggle::make('is_active_for_comparison')
                            ->label('Aktifkan untuk Perbandingan Otomatis')
                            ->helperText('Jika nonaktif, referensi ini hanya ditampilkan sebagai informasi referensi.')
                            ->default(false),
                        Textarea::make('interpretation_notes')
                            ->label('Catatan Interpretasi Ilmiah')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
