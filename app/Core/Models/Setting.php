<?php

namespace App\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperSetting
 */
class Setting extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    /**
     * @var list<string>
     */
    public array $translatable = [
        'company_name',
        'tagline',
        'address',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'company_name',
        'tagline',
        'address',
        'email',
        'phone',
        'social_links',
        'blog_posts_per_page',
        'blog_translation_body_chunk_size',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'blog_posts_per_page' => 'integer',
            'blog_translation_body_chunk_size' => 'integer',
        ];
    }

    /**
     * Cache key for the singleton site settings (used by site() and SettingObserver).
     */
    public static function siteCacheKey(): string
    {
        return str_replace('\\', '.', static::class).'.site';
    }

    /**
     * Get the singleton site settings instance (cached).
     * Cache key includes class name so namespace moves invalidate old entries.
     */
    public static function site(): self
    {
        $ttl = config('cache.content_ttl', 86400);

        return Cache::remember(static::siteCacheKey(), $ttl, function (): self {
            $setting = static::where('key', 'site')->first();
            if ($setting === null) {
                $setting = static::create(['key' => 'site']);
            }

            return $setting;
        });
    }

    /**
     * Blog posts per page for the public index (cached via site()). Falls back to config when null or zero.
     */
    public static function blogPostsPerPage(): int
    {
        $value = static::site()->getAttribute('blog_posts_per_page');
        $int = $value !== null ? (int) $value : 0;

        return $int > 0 ? $int : (int) config('blog.posts_per_page', 10);
    }

    /**
     * Max body chunk size (chars) for AI translation (cached via site()). Falls back to config when null or zero.
     */
    public static function translationBodyChunkSize(): int
    {
        $value = static::site()->getAttribute('blog_translation_body_chunk_size');
        $int = $value !== null ? (int) $value : 0;

        return $int > 0 ? $int : (int) config('blog.translation_body_chunk_size', 6000);
    }

    /**
     * Register media collections for branding assets.
     * Each is a single-file public collection so the latest upload replaces the previous one.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('app_logo')->singleFile()->useDisk('public');
        $this->addMediaCollection('favicon')->singleFile()->useDisk('public');
        $this->addMediaCollection('apple_touch_icon')->singleFile()->useDisk('public');
        $this->addMediaCollection('manifest_icon_192')->singleFile()->useDisk('public');
        $this->addMediaCollection('manifest_icon_512')->singleFile()->useDisk('public');
    }
}
