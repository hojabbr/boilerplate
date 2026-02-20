<?php

namespace App\Core\Observers;

use App\Core\Models\Language;

/**
 * Cascades soft delete/restore to related pages when a language is soft-deleted or restored.
 * BlogPosts are cascade-deleted by DB FK when a language is force-deleted.
 * Fires for all CRUD paths (Filament, API, tinker, etc.).
 */
class LanguageObserver
{
    /**
     * Soft delete: cascade soft delete to pages that belong to this language.
     * Only runs when pages table has language_id column.
     */
    public function deleted(Language $language): void
    {
        if ($language->isForceDeleting()) {
            return;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('pages', 'language_id')) {
            $language->pages()->each(fn ($page) => $page->delete());
        }
    }

    /**
     * Restore: restore soft-deleted pages that belong to this language.
     */
    public function restored(Language $language): void
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('pages', 'language_id')) {
            $language->pages()->withTrashed()->each(fn ($page) => $page->restore());
        }
    }
}
