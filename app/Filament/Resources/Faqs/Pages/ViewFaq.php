<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFaq extends ViewRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(
            [EditAction::make()],
            $this->softDeleteHeaderActions(),
        );
    }
}
