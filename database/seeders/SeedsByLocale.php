<?php

namespace Database\Seeders;

use App\Core\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

trait SeedsByLocale
{
    /**
     * Languages ordered by sort_order, optionally filtered to enabled only when is_enabled column exists.
     *
     * @return Collection<int, Language>
     */
    protected function languages(): Collection
    {
        $query = Language::query()->orderBy('sort_order');

        if (Schema::hasColumn((new Language)->getTable(), 'is_enabled')) {
            $query->where('is_enabled', true);
        }

        return $query->get();
    }
}
