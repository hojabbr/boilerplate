<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

/**
 * @mixin IdeHelperSetting
 */
class Setting extends Model
{
    use HasTranslations;

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
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }

    /**
     * Get the singleton site settings instance (cached).
     */
    public static function site(): self
    {
        $ttl = config('cache.content_ttl', 86400);

        return Cache::remember('setting.site', $ttl, function (): self {
            $setting = static::where('key', 'site')->first();
            if ($setting === null) {
                $setting = static::create(['key' => 'site']);
            }

            return $setting;
        });
    }
}
