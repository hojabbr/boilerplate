<?php

namespace App\Domains\Blog\Support;

use App\Domains\Blog\Models\BlogPost;
use Illuminate\Support\Facades\Route;

class ResolveInternalBlogLinks
{
    /**
     * Replace [[slug:xxx]] placeholders in body HTML with proper links: current locale URL,
     * link text = post title (fallback to slug), class for styling.
     */
    public static function resolve(string $body): string
    {
        if ($body === '') {
            return $body;
        }

        $locale = app()->getLocale();

        return (string) preg_replace_callback(
            '/\[\[slug:([^\]]+)\]\]/',
            function (array $m) use ($locale): string {
                $slug = trim($m[1]);
                try {
                    $url = Route::has('blog.show') ? route('blog.show', ['slug' => $slug]) : '#';
                } catch (\Throwable) {
                    $url = '#';
                }

                $title = self::titleForSlugInLocale($slug, $locale);
                $linkText = $title !== '' ? $title : $slug;

                return '<a href="'.e($url).'" class="blog-internal-link">'.e($linkText).'</a>';
            },
            $body
        );
    }

    /**
     * Get the title of the post with the given slug in the given locale, or empty string if not found.
     */
    private static function titleForSlugInLocale(string $slug, string $locale): string
    {
        $post = BlogPost::query()
            ->byLocale($locale)
            ->where('slug', $slug)
            ->first();

        return $post !== null ? ($post->title ?? '') : '';
    }
}
