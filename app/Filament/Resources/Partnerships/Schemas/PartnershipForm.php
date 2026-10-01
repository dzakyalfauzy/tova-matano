<?php

namespace App\Filament\Resources\Partnerships\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PartnershipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('logo_url')
                    ->image()
                    ->directory('partnerships'),
                TextInput::make('website_url')
                    ->url()
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('type')
                    ->options([
                        'partner' => 'Partner',
                        'sponsor' => 'Sponsor',
                        'media' => 'Media',
                    ])
                    ->required()
                    ->default('partner'),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
