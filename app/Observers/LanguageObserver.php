<?php

namespace App\Observers;

use App\Models\Language;

class LanguageObserver
{
    /**
     * Handle the Language "deleted" event (soft delete): cascade soft delete to pages.
     */
    public function deleted(Language $language): void
    {
        if ($language->isForceDeleting()) {
            return;
        }
        $language->pages()->each(fn ($page) => $page->delete());
    }

    /**
     * Handle the Language "restored" event: restore soft-deleted pages.
     */
    public function restored(Language $language): void
    {
        $language->pages()->withTrashed()->each(fn ($page) => $page->restore());
    }
}
