<?php

namespace App\Domains\Blog\Queries;

use App\Domains\Blog\Models\BlogPost;
use Illuminate\Support\Collection;

class GetLastBlogPostTitles
{
    /**
     * Last N blog posts (any locale) for AI context. Returns title and slug.
     *
     * @return Collection<int, array{title: string, slug: string}>
     */
    public function handle(int $limit = 100): Collection
    {
        return BlogPost::query()
            ->latest('id')
            ->limit($limit)
            ->get(['id', 'title', 'slug'])
            ->map(fn (BlogPost $post): array => [
                'title' => $post->title,
                'slug' => $post->slug,
            ])
            ->values();
    }
}
