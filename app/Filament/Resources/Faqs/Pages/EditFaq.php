<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends EditRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return $this->softDeleteHeaderActions();
    }
}
