<?php

namespace App\Core\Policies;

use App\Core\Models\FeatureFlag;
use App\Domains\Auth\Models\User;

class FeatureFlagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage feature flags');
    }

    public function view(User $user, FeatureFlag $featureFlag): bool
    {
        return $user->can('manage feature flags');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, FeatureFlag $featureFlag): bool
    {
        return $user->can('manage feature flags');
    }

    public function delete(User $user, FeatureFlag $featureFlag): bool
    {
        return false;
    }

    public function restore(User $user, FeatureFlag $featureFlag): bool
    {
        return false;
    }

    public function forceDelete(User $user, FeatureFlag $featureFlag): bool
    {
        return false;
    }
}
