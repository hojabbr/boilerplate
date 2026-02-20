<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperLandingSectionItem
 */
class LandingSectionItem extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia, SoftDeletes;

    /**
     * @var list<string>
     */
    public array $translatable = [
        'title',
        'description',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'landing_section_id',
        'sort_order',
        'title',
        'description',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections('icon')
            ->fit(Fit::Crop, 96, 96)
            ->nonQueued();

        // Card image for feature section (h-40 ≈ 160px, 2x = 320; width 800 for sharp display)
        $this->addMediaConversion('card')
            ->performOnCollections('icon')
            ->fit(Fit::Crop, 800, 320)
            ->nonQueued();
    }

    /**
     * @return BelongsTo<LandingSection, $this>
     */
    public function landingSection(): BelongsTo
    {
        return $this->belongsTo(LandingSection::class);
    }
}
