<?php

namespace App\Filament\Resources\Volunteers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VolunteerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('registration_code')
                    ->disabled()
                    ->dehydrated(false)
                    ->hiddenOn('create'),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('address'),
                Textarea::make('motivation')
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(['PENDING' => 'Pending', 'APPROVED' => 'Approved', 'REJECTED' => 'Rejected'])
                    ->default('PENDING')
                    ->required(),
                FileUpload::make('photo_url')
                    ->image()
                    ->disk('public')
                    ->directory('volunteers'),
                DateTimePicker::make('submitted_at')
                    ->default(now())
                    ->required(),
                DateTimePicker::make('reviewed_at'),
                TextInput::make('reviewed_by')
                    ->numeric(),
                DateTimePicker::make('notified_at'),
            ]);
    }
}
