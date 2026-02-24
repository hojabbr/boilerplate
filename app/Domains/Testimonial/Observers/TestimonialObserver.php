<?php

namespace App\Domains\Testimonial\Observers;

use App\Core\Traits\InvalidatesLocaleListCache;
use App\Domains\Testimonial\Models\Testimonial;

class TestimonialObserver
{
    use InvalidatesLocaleListCache;

    public function saved(Testimonial $testimonial): void
    {
        $this->invalidateListCacheFor($testimonial, Testimonial::class);
    }

    public function deleted(Testimonial $testimonial): void
    {
        $this->invalidateListCacheFor($testimonial, Testimonial::class);
    }

    public function restored(Testimonial $testimonial): void
    {
        $this->invalidateListCacheFor($testimonial, Testimonial::class);
    }

    public function forceDeleted(Testimonial $testimonial): void
    {
        $this->forgetCacheOnForceDeleted($testimonial, Testimonial::class);
    }
}
