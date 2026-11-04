<?php

namespace App\Filament\Resources\DonationChannels;

use App\Filament\Resources\DonationChannels\Pages\CreateDonationChannel;
use App\Filament\Resources\DonationChannels\Pages\EditDonationChannel;
use App\Filament\Resources\DonationChannels\Pages\ListDonationChannels;
use App\Filament\Resources\DonationChannels\Schemas\DonationChannelForm;
use App\Filament\Resources\DonationChannels\Tables\DonationChannelsTable;
use App\Models\DonationChannel;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DonationChannelResource extends Resource
{
    protected static ?string $model = DonationChannel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Keanggotaan & Donasi';

    protected static ?string $navigationLabel = 'Saluran Donasi';

    protected static ?string $modelLabel = 'Saluran Donasi';

    protected static ?string $pluralModelLabel = 'Saluran Donasi';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'bank_name';

    public static function form(Schema $schema): Schema
    {
        return DonationChannelForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DonationChannelsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonationChannels::route('/'),
            'create' => CreateDonationChannel::route('/create'),
            'edit' => EditDonationChannel::route('/{record}/edit'),
        ];
    }
}
