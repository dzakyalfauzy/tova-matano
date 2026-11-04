<?php

namespace App\Filament\Resources\DonationChannels\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\DonationChannels\DonationChannelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDonationChannel extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = DonationChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
