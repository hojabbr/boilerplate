<?php

namespace App\Filament\Resources\BlogPosts\Pages\Concerns;

use App\Models\BlogPost;

trait AttachesBlogPostMedia
{
    protected function attachMediaFromFormData(BlogPost $record, array $data): void
    {
        $disk = config('filament.default_filesystem_disk', 'public');

        foreach ($data['gallery'] ?? [] as $path) {
            if (is_string($path)) {
                $record->addMediaFromDisk($path, $disk)->toMediaCollection('gallery');
            }
        }

        foreach ($data['videos'] ?? [] as $path) {
            if (is_string($path)) {
                $record->addMediaFromDisk($path, $disk)->toMediaCollection('videos');
            }
        }

        foreach ($data['documents'] ?? [] as $path) {
            if (is_string($path)) {
                $record->addMediaFromDisk($path, $disk)->toMediaCollection('documents');
            }
        }
    }
}
