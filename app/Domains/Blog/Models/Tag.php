<?php

namespace App\Domains\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @extends Model<Tag>
 */
class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * @return BelongsToMany<BlogPost, $this>
     */
    public function blogPosts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag');
    }

    protected static function booted(): void
    {
        static::creating(function (Tag $tag): void {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }
}
