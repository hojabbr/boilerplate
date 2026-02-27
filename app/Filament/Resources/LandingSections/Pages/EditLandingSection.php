<?php

namespace App\Filament\Resources\LandingSections\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\LandingSections\LandingSectionResource;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditLandingSection extends EditRecord
{
    use HasSoftDeleteActions;
    use Translatable;

    protected static string $resource = LandingSectionResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(
            [LocaleSwitcher::make()],
            $this->softDeleteHeaderActions(),
        );
    }
}
