<?php

namespace App\Filament\Resources\OrgCategories\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\OrgCategories\OrgCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrgCategory extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = OrgCategoryResource::class;
}
