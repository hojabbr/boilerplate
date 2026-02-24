<?php

namespace App\Domains\Faq\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Faq\Models\Faq;

class FaqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage faq');
    }

    public function view(User $user, Faq $faq): bool
    {
        return $user->can('manage faq');
    }

    public function create(User $user): bool
    {
        return $user->can('manage faq');
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->can('manage faq');
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->can('manage faq');
    }

    public function restore(User $user, Faq $faq): bool
    {
        return $user->can('manage faq');
    }

    public function forceDelete(User $user, Faq $faq): bool
    {
        return $user->can('manage faq');
    }
}
