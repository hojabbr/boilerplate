<?php

namespace App\Domains\Blog\Jobs;

use App\Domains\Auth\Models\User;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Blog\Services\BlogPostGenerationService;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class FinalizeBlogPostGenerationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    /**
     * @param  array<string, mixed>  $sourceStructured  From AI response (for suggested_tags).
     * @param  array{generate_image?: bool, image_style?: string, provider?: string}  $options
     */
    public function __construct(
        protected int $sourcePostId,
        protected array $sourceStructured,
        protected array $options,
        protected int $userId,
    ) {
        $this->onQueue('blog');
    }

    public function handle(BlogPostGenerationService $service): void
    {
        $service->finalizeGeneration($this->sourcePostId, $this->sourceStructured, $this->options);

        $this->clearUserGenerationFlag();
        $this->sendSuccessNotification();
    }

    public function failed(?\Throwable $exception = null): void
    {
        $this->clearUserGenerationFlag();

        $user = User::find($this->userId);
        if ($user) {
            Notification::make()
                ->title('Blog post finalization failed: '.($exception ? $exception->getMessage() : 'Unknown error'))
                ->danger()
                ->sendToDatabase($user);
        }
    }

    private function clearUserGenerationFlag(): void
    {
        Cache::forget('blog_generate_'.$this->userId);
    }

    private function sendSuccessNotification(): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            return;
        }

        $sourcePost = BlogPost::find($this->sourcePostId);
        if (! $sourcePost) {
            return;
        }

        $posts = BlogPost::query()->where('slug', $sourcePost->slug)->get();
        $count = $posts->count();
        $first = $posts->first();
        if (! $first instanceof BlogPost) {
            return;
        }

        $editUrl = BlogPostResource::getUrl('edit', ['record' => $first]);

        Notification::make()
            ->title($count === 1 ? 'Blog post created as draft.' : "{$count} blog posts created as draft.")
            ->body('You can edit and publish them from the blog posts list.')
            ->success()
            ->actions([
                Action::make('view')
                    ->label('View first post')
                    ->url($editUrl),
            ])
            ->sendToDatabase($user);
    }
}
