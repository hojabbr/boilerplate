<?php

namespace App\Domains\Pages\Models;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @extends Model<Page>
 *
 * @mixin IdeHelperPage
 */
class Page extends Model implements HasMedia
{
    /** @use HasFactory<Page> */
    use HasFactory, HasTranslations, InteractsWithMedia, Searchable, SoftDeletes;

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
        'is_active',
        'show_in_navigation',
        'show_in_footer',
        'order',
        'meta_title',
        'meta_description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_navigation' => 'boolean',
            'show_in_footer' => 'boolean',
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Page>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Page>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Cache key for a page by slug (used by GetPageBySlug and PageObserver).
     */
    public static function slugCacheKey(string $slug): string
    {
        return str_replace('\\', '.', static::class).'.slug.'.$slug;
    }

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

    /**
     * @return Factory<Page>
     */
    protected static function newFactory(): Factory
    {
        return PageFactory::new();
    }
}
