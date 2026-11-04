<?php

namespace App\Filament\Resources\Volunteers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VolunteerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pendaftar')
                    ->description('Identitas dan kontak calon relawan.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('registration_code')
                            ->label('Kode Pendaftaran')
                            ->helperText('Dibuat otomatis oleh sistem.')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create'),
                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->required(),
                        TextInput::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Textarea::make('motivation')
                            ->label('Motivasi')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('photo_url')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('volunteers')
                            ->columnSpanFull(),
                    ]),

                Section::make('Kutipan Relawan')
                    ->description('Kutipan atau pesan personal yang berpotensi ditampilkan pada Running Text website.')
                    ->columns(1)
                    ->schema([
                        Textarea::make('quote')
                            ->label('Kutipan / Quotes')
                            ->rows(3)
                            ->maxLength(300)
                            ->helperText('Maksimal 300 karakter. Digunakan untuk materi publikasi atau Running Text TMCC.')
                            ->placeholder('Tuliskan kutipan, pesan, atau harapan untuk pelestarian Danau Matano...'),
                        Toggle::make('quote_consent')
                            ->label('Persetujuan Publikasi Kutipan')
                            ->helperText('Menandakan calon relawan telah menyetujui kutipannya ditampilkan secara publik pada website TMCC.')
                            ->default(false),
                    ]),

                Section::make('Status Pendaftaran')
                    ->description('Status dan riwayat peninjauan pendaftaran.')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(['PENDING' => 'Menunggu', 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak'])
                            ->default('PENDING')
                            ->required(),
                        DateTimePicker::make('submitted_at')
                            ->label('Tanggal Dikirim')
                            ->default(now())
                            ->required(),
                        DateTimePicker::make('reviewed_at')
                            ->label('Tanggal Ditinjau'),
                        TextInput::make('reviewed_by')
                            ->label('Ditinjau Oleh')
                            ->numeric(),
                        DateTimePicker::make('notified_at')
                            ->label('Tanggal Diberitahu'),
                    ]),
            ]);
    }
}
