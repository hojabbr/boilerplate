<?php

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage blog');
    }

    public function view(User $user, BlogPost $blogPost): bool
    {
        return $user->can('manage blog');
    }

    public function create(User $user): bool
    {
        return $user->can('manage blog');
    }

    public function update(User $user, BlogPost $blogPost): bool
    {
        return $user->can('manage blog');
    }

    public function delete(User $user, BlogPost $blogPost): bool
    {
        return $user->can('manage blog');
    }

    public function restore(User $user, BlogPost $blogPost): bool
    {
        return $user->can('manage blog');
    }

    public function forceDelete(User $user, BlogPost $blogPost): bool
    {
        return $user->can('manage blog');
    }
}
