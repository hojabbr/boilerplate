<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return $this->softDeleteHeaderActions();
    }
}
