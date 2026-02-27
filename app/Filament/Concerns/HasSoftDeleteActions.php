<?php

namespace App\Filament\Concerns;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

trait HasSoftDeleteActions
{
    /**
     * Standard header actions for soft-deletable resources: Delete, ForceDelete, Restore.
     *
     * @return array<int, DeleteAction|ForceDeleteAction|RestoreAction>
     */
    protected function softDeleteHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
