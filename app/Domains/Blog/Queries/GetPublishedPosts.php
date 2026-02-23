<?php

namespace App\Domains\Blog\Queries;

use App\Core\Models\Setting;
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

        $perPage = Setting::blogPostsPerPage();

        return BlogPost::query()
            ->byLocale(app()->getLocale())
            ->published()
            ->with('tags')
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
                    'tags' => $post->tags->map(fn ($tag) => [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ])->values()->all(),
                ];
            });
    }
}
