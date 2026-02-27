<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Pages\PageResource;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditPage extends EditRecord
{
    use HasSoftDeleteActions;
    use Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(
            [LocaleSwitcher::make()],
            $this->softDeleteHeaderActions(),
        );
    }
}
