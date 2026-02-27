<?php

namespace App\Filament\Resources\Languages\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Languages\LanguageResource;
use Filament\Resources\Pages\EditRecord;

class EditLanguage extends EditRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return $this->softDeleteHeaderActions();
    }
}
