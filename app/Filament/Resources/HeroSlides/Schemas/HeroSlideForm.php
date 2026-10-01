<?php

namespace App\Filament\Resources\HeroSlides\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Tabs;

class HeroSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_url')
                    ->image()
                    ->disk('public')
                    ->directory('hero-slides')
                    ->required(),
                TextInput::make('cta_url')
                    ->url(),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                
                Tabs::make('Translations')
                    ->tabs([
                        Tabs\Tab::make('Indonesian (ID)')
                            ->schema([
                                TextInput::make('id.title')->label('Title (ID)'),
                                TextInput::make('id.subtitle')->label('Subtitle (ID)'),
                                TextInput::make('id.cta_label')->label('CTA Label (ID)'),
                            ]),
                        Tabs\Tab::make('English (EN)')
                            ->schema([
                                TextInput::make('en.title')->label('Title (EN)'),
                                TextInput::make('en.subtitle')->label('Subtitle (EN)'),
                                TextInput::make('en.cta_label')->label('CTA Label (EN)'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
