<?php

namespace App\Domains\Blog\Queries;

use App\Domains\Blog\Models\BlogPost;
use Laravel\Pennant\Feature;

class GetPostBySlug
{
    /**
     * @return array{post: array<string, mixed>, gallery: array<int, array<string, mixed>>, videos: array<int, array<string, mixed>>, documents: array<int, array<string, mixed>>, og_image: string|null}
     */
    public function handle(string $slug): array
    {
        if (! Feature::active('blog')) {
            abort(404);
        }

        $post = BlogPost::query()
            ->byLocale(app()->getLocale())
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $gallery = $post->getMedia('gallery')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'full_url' => $media->getUrl('full'),
            'thumb_url' => $media->getUrl('thumb'),
            'card_url' => $media->getUrl('card'),
            'type' => 'image',
            'alt' => $media->getCustomProperty('alt'),
            'title' => $media->getCustomProperty('title') ?? $media->name,
        ])->values()->all();

        $videos = $post->getMedia('videos')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'type' => 'video',
            'mime_type' => $media->mime_type,
        ])->values()->all();

        $documents = $post->getMedia('documents')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'file_name' => $media->file_name,
            'type' => 'file',
        ])->values()->all();

        $firstImage = $post->getFirstMedia('gallery');
        $ogImage = $firstImage?->getUrl('full');

        return [
            'post' => [
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'body' => $post->body,
                'meta_description' => $post->meta_description,
                'published_at' => $post->published_at instanceof \DateTimeInterface ? $post->published_at->format('c') : null,
                'gallery' => $gallery,
                'videos' => $videos,
                'documents' => $documents,
            ],
            'gallery' => $gallery,
            'videos' => $videos,
            'documents' => $documents,
            'og_image' => $ogImage,
        ];
    }
}
