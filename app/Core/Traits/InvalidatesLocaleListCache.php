<?php

namespace App\Core\Traits;

use App\Core\Models\Language;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Trait for observers of models that have a per-locale list cache (e.g. Faq, Testimonial).
 * The model must have a static listCacheKey(string $locale): string method and a language relation.
 */
trait InvalidatesLocaleListCache
{
    protected function invalidateListCacheFor(Model $model, string $modelClass): void
    {
        $code = $this->getLanguageCodeFrom($model);
        if ($code !== null) {
            Cache::forget($modelClass::listCacheKey($code));
        }
    }

    protected function forgetCacheOnForceDeleted(Model $model, string $modelClass): void
    {
        $code = $this->getLanguageCodeFrom($model);
        if ($code !== null) {
            Cache::forget($modelClass::listCacheKey($code));

            return;
        }
        $langId = $model->getOriginal('language_id');
        if ($langId !== null) {
            $lang = Language::find($langId);
            if ($lang instanceof Language) {
                Cache::forget($modelClass::listCacheKey($lang->code));
            }
        }
    }

    private function getLanguageCodeFrom(Model $model): ?string
    {
        $language = $model->getRelationValue('language');
        if ($language instanceof Language) {
            return $language->code;
        }

        return null;
    }
}
