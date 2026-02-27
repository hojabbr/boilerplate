<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return $this->softDeleteHeaderActions();
    }
}
