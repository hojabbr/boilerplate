<?php

namespace App\Filament\Resources\Pages\Pages\Concerns;

use App\Models\Page;

trait AttachesPageMedia
{
    protected function attachMediaFromFormData(Page $record, array $data): void
    {
        $disk = config('filament.default_filesystem_disk', 'public');

        foreach ($data['gallery'] ?? [] as $path) {
            if (is_string($path)) {
                $record->addMediaFromDisk($path, $disk)->toMediaCollection('gallery');
            }
        }

        foreach ($data['documents'] ?? [] as $path) {
            if (is_string($path)) {
                $record->addMediaFromDisk($path, $disk)->toMediaCollection('documents');
            }
        }
    }
}
