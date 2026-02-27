<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return $this->softDeleteHeaderActions();
    }

    protected function afterSave(): void
    {
        $roles = $this->form->getState()['roles'] ?? [];
        $this->record->syncRoles($roles);
    }
}
