<?php

namespace App\Domains\Pages\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Pages\Models\Page;

class PagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage pages');
    }

    public function view(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }

    public function create(User $user): bool
    {
        return $user->can('manage pages');
    }

    public function update(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }

    public function restore(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }

    public function forceDelete(User $user, Page $page): bool
    {
        return $user->can('manage pages');
    }
}
