<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Anggota')
                    ->description('Nama, foto, dan divisi anggota.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        TextInput::make('divisi_name')
                            ->label('Divisi'),
                        FileUpload::make('photo_url')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('members')
                            ->columnSpanFull(),
                    ]),

                Section::make('Keanggotaan')
                    ->description('Poin, pesan, tanggal bergabung, dan status unggulan.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('points')
                            ->label('Poin')
                            ->required()
                            ->numeric()
                            ->default(0),
                        DateTimePicker::make('joined_at')
                            ->label('Tanggal Bergabung')
                            ->default(now())
                            ->required(),
                        Textarea::make('message')
                            ->label('Pesan')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_featured')
                            ->label('Unggulan')
                            ->helperText('Aktifkan agar anggota ditampilkan sebagai unggulan.')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}
