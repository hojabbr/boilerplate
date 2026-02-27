<?php

namespace App\Filament\Resources\ContactSubmissions\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContactSubmission extends ViewRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return $this->softDeleteHeaderActions();
    }
}
