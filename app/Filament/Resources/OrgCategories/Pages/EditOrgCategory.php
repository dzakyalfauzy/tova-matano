<?php

namespace App\Filament\Resources\OrgCategories\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\OrgCategories\OrgCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrgCategory extends EditRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = OrgCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
