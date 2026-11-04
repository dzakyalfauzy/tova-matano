<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kegiatan')
                    ->description('Data utama kegiatan yang ditampilkan pada situs.')
                    ->columns(2)
                    ->schema([
                        Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->required(),
                        TextInput::make('title')
                            ->label('Judul')
                            ->helperText('Slug dibuat otomatis dari judul saat data baru dibuat.')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Alamat unik kegiatan pada URL.')
                            ->required()
                            ->unique(ignoreRecord: true),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->default(now())
                            ->required(),
                        Toggle::make('is_published')
                            ->label('Status Terbit')
                            ->helperText('Aktifkan agar kegiatan tampil di situs.')
                            ->default(true)
                            ->required(),
                    ]),

                Section::make('Media & Konten')
                    ->description('Gambar, tautan media sosial, dan isi kegiatan.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('cover_image_url')
                            ->label('Gambar Sampul')
                            ->image()
                            ->disk('public')
                            ->directory('activities')
                            ->columnSpanFull()
                            ->required(),
                        RichEditor::make('content')
                            ->label('Konten')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('youtube_url')
                            ->label('URL YouTube')
                            ->url(),
                        TextInput::make('instagram_url')
                            ->label('URL Instagram')
                            ->url(),
                    ]),
            ]);
    }
}
