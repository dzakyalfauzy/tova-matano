<?php

namespace App\Filament\Resources\DonationChannels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonationChannelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Saluran Donasi')
                    ->description('Informasi rekening bank untuk penerimaan donasi.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->required(),
                        TextInput::make('account_no')
                            ->label('Nomor Rekening')
                            ->required(),
                        TextInput::make('account_name')
                            ->label('Nama Pemilik')
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Aktifkan agar saluran donasi tampil di situs.')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}
