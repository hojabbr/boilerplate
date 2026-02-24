<?php

namespace App\Domains\Testimonial\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Testimonial\Models\Testimonial;

class TestimonialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage testimonials');
    }

    public function view(User $user, Testimonial $testimonial): bool
    {
        return $user->can('manage testimonials');
    }

    public function create(User $user): bool
    {
        return $user->can('manage testimonials');
    }

    public function update(User $user, Testimonial $testimonial): bool
    {
        return $user->can('manage testimonials');
    }

    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->can('manage testimonials');
    }

    public function restore(User $user, Testimonial $testimonial): bool
    {
        return $user->can('manage testimonials');
    }

    public function forceDelete(User $user, Testimonial $testimonial): bool
    {
        return $user->can('manage testimonials');
    }
}
