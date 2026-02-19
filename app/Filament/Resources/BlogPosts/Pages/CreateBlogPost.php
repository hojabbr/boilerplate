<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\BlogPosts\Pages\Concerns\AttachesBlogPostMedia;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    use AttachesBlogPostMedia;

    protected static string $resource = BlogPostResource::class;

    protected function afterCreate(): void
    {
        $this->attachMediaFromFormData($this->getRecord(), $this->data);
    }
}
