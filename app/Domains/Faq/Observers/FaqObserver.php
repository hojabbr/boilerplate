<?php

namespace App\Domains\Faq\Observers;

use App\Core\Models\Language;
use App\Domains\Faq\Models\Faq;
use Illuminate\Support\Facades\Cache;

class FaqObserver
{
    public function saved(Faq $faq): void
    {
        $this->invalidateListCache($faq);
    }

    public function deleted(Faq $faq): void
    {
        $this->invalidateListCache($faq);
    }

    public function restored(Faq $faq): void
    {
        $this->invalidateListCache($faq);
    }

    public function forceDeleted(Faq $faq): void
    {
        $code = $this->getLanguageCode($faq);
        if ($code === null) {
            $langId = $faq->getOriginal('language_id');
            if ($langId !== null) {
                $lang = Language::find($langId);
                if ($lang instanceof Language) {
                    Cache::forget(Faq::listCacheKey($lang->code));
                }
            }
        } else {
            Cache::forget(Faq::listCacheKey($code));
        }
    }

    private function invalidateListCache(Faq $faq): void
    {
        $code = $this->getLanguageCode($faq);
        if ($code !== null) {
            Cache::forget(Faq::listCacheKey($code));
        }
    }

    private function getLanguageCode(Faq $faq): ?string
    {
        $language = $faq->getRelationValue('language');
        if ($language instanceof Language) {
            return $language->code;
        }

        return null;
    }
}
