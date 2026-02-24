<?php

namespace App\Domains\Faq\Observers;

use App\Core\Traits\InvalidatesLocaleListCache;
use App\Domains\Faq\Models\Faq;

class FaqObserver
{
    use InvalidatesLocaleListCache;

    public function saved(Faq $faq): void
    {
        $this->invalidateListCacheFor($faq, Faq::class);
    }

    public function deleted(Faq $faq): void
    {
        $this->invalidateListCacheFor($faq, Faq::class);
    }

    public function restored(Faq $faq): void
    {
        $this->invalidateListCacheFor($faq, Faq::class);
    }

    public function forceDeleted(Faq $faq): void
    {
        $this->forgetCacheOnForceDeleted($faq, Faq::class);
    }
}
