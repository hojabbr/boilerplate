<?php

namespace App\Domains\Blog\Policies;

use App\Domains\Auth\Models\User;
use App\Domains\Blog\Models\BlogPostSeries;

class BlogPostSeriesPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage blog');
    }

    public function view(User $user, BlogPostSeries $blogPostSeries): bool
    {
        return $user->can('manage blog') && $blogPostSeries->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('manage blog');
    }

    public function update(User $user, BlogPostSeries $blogPostSeries): bool
    {
        return $user->can('manage blog') && $blogPostSeries->user_id === $user->id;
    }

    public function delete(User $user, BlogPostSeries $blogPostSeries): bool
    {
        return $user->can('manage blog') && $blogPostSeries->user_id === $user->id;
    }

    public function restore(User $user, BlogPostSeries $blogPostSeries): bool
    {
        return $user->can('manage blog') && $blogPostSeries->user_id === $user->id;
    }

    public function forceDelete(User $user, BlogPostSeries $blogPostSeries): bool
    {
        return $user->can('manage blog') && $blogPostSeries->user_id === $user->id;
    }
}
