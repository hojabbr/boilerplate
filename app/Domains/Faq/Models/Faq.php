<?php

namespace App\Domains\Faq\Models;

use App\Core\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

/**
 * @extends Model<Faq>
 *
 * @mixin IdeHelperFaq
 */
class Faq extends Model
{
    use Searchable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'language_id',
        'question',
        'answer',
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
     * @param  Builder<Faq>  $query
     * @return Builder<Faq>
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
        return $this->isDirty(['question', 'answer', 'language_id']);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'question' => $this->question,
            'answer' => $this->answer,
            'language_id' => $this->language_id,
        ];
    }

    public static function listCacheKey(string $locale): string
    {
        return 'faq_list.'.$locale;
    }
}
