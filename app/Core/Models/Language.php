<?php

namespace App\Core\Models;

use App\Domains\Blog\Models\BlogPost;
use Database\Factories\LanguageFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @extends Model<Language>
 *
 * @mixin IdeHelperLanguage
 */
class Language extends Model
{
    /** @use HasFactory<Language> */
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'script',
        'regional',
        'is_default',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * @return HasMany<BlogPost, $this>
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    /**
     * Pages are row-per-locale when pages.language_id exists. Currently not in schema.
     *
     * @return HasMany<\App\Domains\Page\Models\Page, $this>
     */
    public function pages(): HasMany
    {
        return $this->hasMany(\App\Domains\Page\Models\Page::class);
    }

    /**
     * @return Factory<Language>
     */
    protected static function newFactory(): Factory
    {
        return LanguageFactory::new();
    }
}
