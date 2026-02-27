<?php

namespace App\Filament\Resources\Languages\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Languages\LanguageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLanguage extends ViewRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(
            [EditAction::make()],
            $this->softDeleteHeaderActions(),
        );
    }
}
