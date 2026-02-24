<?php

namespace App\Domains\Testimonial\Models;

use App\Core\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

/**
 * @extends Model<Testimonial>
 *
 * @mixin IdeHelperTestimonial
 */
class Testimonial extends Model
{
    use Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'language_id',
        'quote',
        'author',
        'role',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * @return BelongsTo<Language, $this>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * @param  Builder<Testimonial>  $query
     * @return Builder<Testimonial>
     */
    public function scopeByLocale(Builder $query, string $code): Builder
    {
        return $query->whereHas('language', fn ($q) => $q->where('code', $code));
    }

    public function shouldBeSearchable(): bool
    {
        return true;
    }

    /**
     * Only sync index when content attributes change.
     */
    public function searchIndexShouldBeUpdated(): bool
    {
        return $this->isDirty(['quote', 'author', 'role', 'language_id']);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'quote' => $this->quote,
            'author' => $this->author,
            'role' => $this->role,
            'language_id' => $this->language_id,
        ];
    }

    public static function listCacheKey(string $locale): string
    {
        return 'testimonial_list.'.$locale;
    }
}
