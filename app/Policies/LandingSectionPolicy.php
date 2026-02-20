<?php

namespace App\Policies;

use App\Models\LandingSection;
use App\Models\User;

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
