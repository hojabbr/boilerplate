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
use Illuminate\Support\Collection;

class GenerateBlogPostsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 600;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        protected array $data,
        protected int $userId,
    ) {
        $this->onQueue('blog');
    }

    public function handle(BlogPostGenerationService $service): void
    {
        $user = User::find($this->userId);
        if (! $user) {
            return;
        }

        try {
            $posts = $service->run($this->data);
            $this->sendSuccessNotification($user, $posts);
        } catch (\Throwable $e) {
            report($e);
            $this->sendFailureNotification($user, $e->getMessage());
        }
    }

    /**
     * @param  Collection<int, BlogPost>  $posts
     */
    private function sendSuccessNotification(User $user, Collection $posts): void
    {
        if ($posts->isEmpty()) {
            Notification::make()
                ->title('Blog post generation produced no content.')
                ->danger()
                ->sendToDatabase($user);

            return;
        }

        $first = $posts->first();
        $editUrl = BlogPostResource::getUrl('edit', ['record' => $first]);
        $count = $posts->count();

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

    private function sendFailureNotification(User $user, string $message): void
    {
        $isModelUnavailable = str_contains($message, 'NOT_FOUND')
            || str_contains($message, 'no longer available')
            || str_contains($message, '404');
        $title = $isModelUnavailable
            ? 'Provider or model is no longer available. Try another provider or update the Laravel AI SDK.'
            : 'Generation failed: '.$message;

        Notification::make()
            ->title($title)
            ->danger()
            ->sendToDatabase($user);
    }
}
