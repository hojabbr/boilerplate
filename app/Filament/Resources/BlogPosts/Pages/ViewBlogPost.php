<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBlogPost extends ViewRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(
            [EditAction::make()],
            $this->softDeleteHeaderActions(),
        );
    }
}
