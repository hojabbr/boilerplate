<?php

namespace App\Domains\Testimonial\Observers;

use App\Core\Models\Language;
use App\Domains\Testimonial\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

class TestimonialObserver
{
    public function saved(Testimonial $testimonial): void
    {
        $this->invalidateListCache($testimonial);
    }

    public function deleted(Testimonial $testimonial): void
    {
        $this->invalidateListCache($testimonial);
    }

    public function restored(Testimonial $testimonial): void
    {
        $this->invalidateListCache($testimonial);
    }

    public function forceDeleted(Testimonial $testimonial): void
    {
        $code = $this->getLanguageCode($testimonial);
        if ($code === null) {
            $langId = $testimonial->getOriginal('language_id');
            if ($langId !== null) {
                $lang = Language::find($langId);
                if ($lang instanceof Language) {
                    Cache::forget(Testimonial::listCacheKey($lang->code));
                }
            }
        } else {
            Cache::forget(Testimonial::listCacheKey($code));
        }
    }

    private function invalidateListCache(Testimonial $testimonial): void
    {
        $code = $this->getLanguageCode($testimonial);
        if ($code !== null) {
            Cache::forget(Testimonial::listCacheKey($code));
        }
    }

    private function getLanguageCode(Testimonial $testimonial): ?string
    {
        $language = $testimonial->getRelationValue('language');
        if ($language instanceof Language) {
            return $language->code;
        }

        return null;
    }
}
