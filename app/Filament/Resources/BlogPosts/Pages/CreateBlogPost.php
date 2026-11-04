<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    use RedirectsToResourceIndex;

    protected static string $resource = BlogPostResource::class;
}
