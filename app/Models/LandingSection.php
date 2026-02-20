<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperLandingSection
 */
class LandingSection extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, SoftDeletes;

    /**
     * @var list<string>
     */
    public array $translatable = [
        'title',
        'subtitle',
        'body',
        'cta_text',
        'cta_url',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'sort_order',
        'is_active',
        'title',
        'subtitle',
        'body',
        'cta_text',
        'cta_url',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections('image')
            ->fit(Fit::Crop, 300, 300)
            ->nonQueued();

        $this->addMediaConversion('full')
            ->performOnCollections('image')
            ->fit(Fit::Max, 1920, 1920)
            ->nonQueued();
    }

    /**
     * @return HasMany<LandingSectionItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(LandingSectionItem::class)->orderBy('sort_order');
    }

    /**
     * @param  Builder<LandingSection>  $query
     * @return Builder<LandingSection>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * @param  Builder<LandingSection>  $query
     * @return Builder<LandingSection>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
