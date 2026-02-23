<?php

namespace App\Domains\Blog\Jobs;

use App\Domains\Blog\Services\BlogPostGenerationService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslateBlogPostToLanguageJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    /**
     * @param  array<string, mixed>  $data  Snapshot of form data (provider, publish_immediately, series_id).
     */
    public function __construct(
        protected int $sourcePostId,
        protected int $targetLanguageId,
        protected array $data,
    ) {
        $this->onQueue('blog');
    }

    public function handle(BlogPostGenerationService $service): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $service->translateAndCreatePost($this->sourcePostId, $this->targetLanguageId, $this->data);
    }

    public function retryUntil(): \DateTimeInterface
    {
        return now()->addHours(2);
    }
}
