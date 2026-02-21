<?php

namespace App\Domains\Blog\Observers;

use App\Domains\Blog\Models\BlogPost;
use Illuminate\Support\Facades\Schema;
use Laravel\Ai\Embeddings;

/**
 * Syncs blog post content to blog_post_chunks with embeddings when Postgres + pgvector is available.
 */
class BlogPostObserver
{
    private const EMBEDDING_DIMENSIONS = 1536;

    private const CONTENT_MAX_LENGTH = 8000;

    public function saved(BlogPost $blogPost): void
    {
        if (! $this->shouldSyncChunks()) {
            return;
        }

        $this->syncChunks($blogPost);
    }

    private function shouldSyncChunks(): bool
    {
        if (! config('ai.blog.use_pgvector', true)) {
            return false;
        }

        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return false;
        }

        if (! Schema::hasTable('blog_post_chunks')) {
            return false;
        }

        return true;
    }

    private function syncChunks(BlogPost $blogPost): void
    {
        $blogPost->chunks()->delete();

        $content = $this->buildContent($blogPost);
        if ($content === '') {
            return;
        }

        try {
            $response = Embeddings::for([$content])
                ->dimensions(self::EMBEDDING_DIMENSIONS)
                ->generate();

            $embedding = $response->first();

            $blogPost->chunks()->create([
                'content' => $content,
                'embedding' => $embedding,
            ]);
        } catch (\Throwable) {
            // If embeddings API fails, skip chunk creation (e.g. no key, rate limit).
        }
    }

    private function buildContent(BlogPost $blogPost): string
    {
        $parts = array_filter([
            $blogPost->title,
            $blogPost->excerpt,
            $blogPost->body ? mb_substr(strip_tags($blogPost->body), 0, self::CONTENT_MAX_LENGTH) : null,
        ]);

        return implode("\n\n", $parts);
    }
}
