<?php

namespace App\Filament\Resources\LandingSections\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\LandingSections\LandingSectionResource;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;

class ViewLandingSection extends ViewRecord
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
