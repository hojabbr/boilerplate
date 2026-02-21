<?php

namespace App\Domains\Blog\Models;

use App\Core\Models\Language;
use Database\Factories\BlogPostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property \Carbon\CarbonImmutable|null $published_at
 *
 * @extends Model<BlogPost>
 *
 * @mixin IdeHelperBlogPost
 */
class BlogPost extends Model implements HasMedia
{
    /** @use HasFactory<BlogPost> */
    use HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'language_id',
        'blog_post_series_id',
        'slug',
        'title',
        'excerpt',
        'body',
        'meta_description',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Language, $this>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * @return BelongsTo<BlogPostSeries, $this>
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(BlogPostSeries::class, 'blog_post_series_id');
    }

    /**
     * @return HasMany<BlogPostChunk, $this>
     */
    public function chunks(): HasMany
    {
        return $this->hasMany(BlogPostChunk::class);
    }

    /**
     * @param  Builder<BlogPost>  $query
     * @return Builder<BlogPost>
     */
    public function scopeByLocale(Builder $query, string $code): Builder
    {
        return $query->whereHas('language', fn ($q) => $q->where('code', $code));
    }

    /**
     * @param  Builder<BlogPost>  $query
     * @return Builder<BlogPost>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

        $this->addMediaCollection('videos')
            ->useDisk('public')
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/ogg']);

        $this->addMediaCollection('documents')
            ->useDisk('public')
            ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

        $this->addMediaCollection('audio')
            ->useDisk('public')
            ->acceptsMimeTypes(['audio/mpeg', 'audio/mp3', 'audio/wav']);
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections('gallery')
            ->fit(Fit::Crop, 300, 300)
            ->nonQueued();

        $this->addMediaConversion('card')
            ->performOnCollections('gallery')
            ->fit(Fit::Crop, 640, 400)
            ->quality(90)
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
        return [
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'meta_description' => $this->meta_description,
            'slug' => $this->slug,
            'language_id' => $this->language_id,
        ];
    }

    /**
     * @return Factory<BlogPost>
     */
    protected static function newFactory(): Factory
    {
        return BlogPostFactory::new();
    }
}
