<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengaturan Slide')
                    ->description('Gambar, tautan tombol, dan status tampil slide beranda.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image_url')
                            ->label('Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('hero-slides')
                            ->columnSpanFull()
                            ->required(),
                        TextInput::make('cta_url')
                            ->label('URL Tombol CTA')
                            ->url(),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Aktifkan agar slide tampil di beranda.')
                            ->default(true)
                            ->required(),
                    ]),

                Tabs::make('Translations')
                    ->tabs([
                        Tabs\Tab::make('Bahasa Indonesia (ID)')
                            ->schema([
                                TextInput::make('id.title')->label('Judul (ID)'),
                                TextInput::make('id.subtitle')->label('Subjudul (ID)'),
                                TextInput::make('id.cta_label')->label('Label Tombol CTA (ID)'),
                            ]),
                        Tabs\Tab::make('English (EN)')
                            ->schema([
                                TextInput::make('en.title')->label('Judul (EN)'),
                                TextInput::make('en.subtitle')->label('Subjudul (EN)'),
                                TextInput::make('en.cta_label')->label('Label Tombol CTA (EN)'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
