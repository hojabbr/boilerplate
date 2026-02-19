<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperPage
 */
class Page extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    public array $translatable = [
        'title',
        'body',
        'meta_title',
        'meta_description',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'title',
        'body',
        'type',
        'meta_title',
        'meta_description',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('documents')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections('gallery')
            ->fit(Fit::Crop, 300, 300)
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->performOnCollections('gallery')
            ->fit(Fit::Max, 800, 800)
            ->nonQueued();

        $this->addMediaConversion('full')
            ->performOnCollections('gallery')
            ->fit(Fit::Max, 1920, 1920)
            ->nonQueued();
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $titles = $this->getTranslations('title');
        $bodies = $this->getTranslations('body');
        $content = implode(' ', array_merge(array_values($titles), array_values($bodies)));

        return [
            'slug' => $this->slug,
            'type' => $this->type,
            'title' => $titles,
            'content' => $content,
        ];
    }
}
