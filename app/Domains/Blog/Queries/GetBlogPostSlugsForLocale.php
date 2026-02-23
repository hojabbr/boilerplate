<?php

namespace App\Domains\Blog\Queries;

use App\Domains\Blog\Models\BlogPost;
use Illuminate\Support\Collection;

class GetBlogPostSlugsForLocale
{
    /**
     * Returns title and slug for posts in the given language (for internal linking in generation prompt).
     *
     * @return Collection<int, array{title: string, slug: string}>
     */
    public function handle(int $languageId, int $limit = 80): Collection
    {
        return BlogPost::query()
            ->where('language_id', $languageId)
            ->latest('id')
            ->limit($limit)
            ->get(['id', 'title', 'slug'])
            ->map(fn (BlogPost $post): array => [
                'title' => $post->title ?? '',
                'slug' => $post->slug,
            ])
            ->values();
    }
}
