<?php

namespace App\Observers;

use App\Models\Language;

/**
 * Cascades soft delete/restore to related pages when a language is soft-deleted or restored.
 * BlogPosts are cascade-deleted by DB FK when a language is force-deleted.
 * Fires for all CRUD paths (Filament, API, tinker, etc.).
 */
class LanguageObserver
{
    /**
     * Soft delete: cascade soft delete to pages that belong to this language.
     */
    public function deleted(Language $language): void
    {
        if ($language->isForceDeleting()) {
            return;
        }
        $language->pages()->each(fn ($page) => $page->delete());
    }

    /**
     * Restore: restore soft-deleted pages that belong to this language.
     */
    public function restored(Language $language): void
    {
        $language->pages()->withTrashed()->each(fn ($page) => $page->restore());
    }
}
