<?php

namespace App\Domains\Blog\Queries;

use App\Domains\Blog\Models\BlogPost;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Pennant\Feature;

class GetPublishedPosts
{
    public function handle(): LengthAwarePaginator
    {
        if (! Feature::active('blog')) {
            abort(404);
        }

        $perPage = config('blog.posts_per_page', 12);

        return BlogPost::query()
            ->byLocale(app()->getLocale())
            ->published()
            ->orderByDesc('published_at')
            ->paginate($perPage)
            ->through(function (BlogPost $post): array {
                $firstImage = $post->getFirstMedia('gallery');
                $publishedAt = $post->published_at;

                return [
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'published_at' => $publishedAt instanceof \DateTimeInterface ? $publishedAt->format('c') : null,
                    'thumbnail_url' => $firstImage?->getUrl('card'),
                ];
            });
    }
}
