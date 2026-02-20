<?php

namespace App\Domains\Landing\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Landing\Models\LandingSection;

class LandingSectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage landing sections');
    }

    public function view(User $user, LandingSection $landingSection): bool
    {
        return $user->can('manage landing sections');
    }

    public function create(User $user): bool
    {
        return $user->can('manage landing sections');
    }

    public function update(User $user, LandingSection $landingSection): bool
    {
        return $user->can('manage landing sections');
    }

    public function delete(User $user, LandingSection $landingSection): bool
    {
        return $user->can('manage landing sections');
    }

    public function restore(User $user, LandingSection $landingSection): bool
    {
        return $user->can('manage landing sections');
    }

    public function forceDelete(User $user, LandingSection $landingSection): bool
    {
        return $user->can('manage landing sections');
    }
}
