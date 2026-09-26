<?php

namespace App\Filament\Resources\WaterQualityRecords\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WaterQualityRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('recorded_at')
                    ->default(now())
                    ->required(),
                TextInput::make('ph')
                    ->required()
                    ->numeric()
                    ->step(0.1),
                TextInput::make('clarity_cm')
                    ->numeric(),
                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('recorded_by')
                    ->numeric(),
            ]);
    }
}
