<?php

namespace App\Domains\Blog\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $purpose
 * @property string|null $objective
 * @property string|null $topics
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property array<int, int> $days_of_week
 * @property array<int, int> $run_at_hours
 * @property int $posts_per_run
 * @property int|null $total_posts_limit
 * @property string $provider
 * @property string $length
 * @property array<int, int> $language_ids
 * @property bool $generate_image
 * @property bool $generate_audio
 * @property bool $publish_immediately
 * @property \Carbon\Carbon|null $last_run_at
 * @property int $posts_generated
 * @property bool $is_active
 *
 * @extends Model<BlogPostSeries>
 */
class BlogPostSeries extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'purpose',
        'objective',
        'topics',
        'start_date',
        'end_date',
        'days_of_week',
        'run_at_hours',
        'posts_per_run',
        'total_posts_limit',
        'provider',
        'length',
        'language_ids',
        'generate_image',
        'generate_audio',
        'publish_immediately',
        'last_run_at',
        'posts_generated',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'days_of_week' => 'array',
            'run_at_hours' => 'array',
            'language_ids' => 'array',
            'generate_image' => 'boolean',
            'generate_audio' => 'boolean',
            'publish_immediately' => 'boolean',
            'last_run_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<BlogPost, $this>
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'blog_post_series_id');
    }

    /**
     * @param  Builder<BlogPostSeries>  $query
     * @return Builder<BlogPostSeries>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
