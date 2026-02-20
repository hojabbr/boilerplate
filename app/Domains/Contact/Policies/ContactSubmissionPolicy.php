<?php

namespace App\Domains\Contact\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Contact\Models\ContactSubmission;

class ContactSubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view contact submissions');
    }

    public function view(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can('view contact submissions');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactSubmission $contactSubmission): bool
    {
        return false;
    }

    public function delete(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can('delete contact submissions');
    }

    public function restore(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can('delete contact submissions');
    }

    public function forceDelete(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->can('delete contact submissions');
    }
}
