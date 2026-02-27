<?php

namespace App\Filament\Support;

use Filament\Tables\Filters\SelectFilter;

class CommonFilters
{
    /**
     * Standard language filter (relationship, searchable, preloaded).
     */
    public static function languageFilter(): SelectFilter
    {
        return SelectFilter::make('language_id')
            ->relationship('language', 'name', fn ($q) => $q ? $q->orderBy('sort_order') : $q)
            ->label('Language')
            ->searchable()
            ->preload();
    }
}
