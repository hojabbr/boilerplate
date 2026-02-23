<?php

namespace App\Core\Policies;

use App\Core\Models\Language;
use App\Domains\Auth\Models\User;

class LanguagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage languages');
    }

    public function view(User $user, Language $language): bool
    {
        return $user->can('manage languages');
    }

    public function create(User $user): bool
    {
        return $user->can('manage languages');
    }

    public function update(User $user, Language $language): bool
    {
        return $user->can('manage languages');
    }

    public function delete(User $user, Language $language): bool
    {
        return $user->can('manage languages');
    }

    public function restore(User $user, Language $language): bool
    {
        return $user->can('manage languages');
    }

    public function forceDelete(User $user, Language $language): bool
    {
        return $user->can('manage languages');
    }
}
