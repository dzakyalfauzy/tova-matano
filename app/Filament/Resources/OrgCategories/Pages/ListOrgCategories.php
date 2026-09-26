<?php

namespace App\Filament\Resources\OrgCategories\Pages;

use App\Filament\Resources\OrgCategories\OrgCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrgCategories extends ListRecords
{
    protected static string $resource = OrgCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
