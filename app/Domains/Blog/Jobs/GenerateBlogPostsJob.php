<?php

namespace App\Domains\Blog\Jobs;

use App\Core\Models\Language;
use App\Domains\Auth\Models\User;
use App\Domains\Blog\Services\BlogPostGenerationService;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class GenerateBlogPostsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     * Only generates the source post; translations run in separate jobs.
     */
    public int $timeout = 300;

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
            $result = $service->generateSourceOnly($this->data);
            $sourcePost = $result['post'];
            $sourceStructured = $result['structured'];

            if ($sourcePost === null || $sourceStructured === []) {
                $this->clearUserGenerationFlag();

                Notification::make()
                    ->title('Blog post generation produced no content.')
                    ->danger()
                    ->sendToDatabase($user);

                return;
            }

            $languageIds = $this->data['language_ids'] ?? [];
            $languages = Language::query()
                ->whereIn('id', $languageIds)
                ->orderBy('sort_order')
                ->get();
            $sourceLanguage = $languages->firstWhere('code', 'en') ?? $languages->first();
            $remainingLanguages = $languages->filter(fn ($l) => $l->id !== $sourceLanguage->id)->values();

            if ($remainingLanguages->isEmpty()) {
                FinalizeBlogPostGenerationJob::dispatch(
                    $sourcePost->id,
                    $sourceStructured,
                    [
                        'generate_image' => $this->data['generate_image'] ?? false,
                        'image_style' => $this->data['image_style'] ?? 'editorial',
                        'provider' => $this->data['provider'] ?? config('ai.default'),
                    ],
                    $this->userId
                );

                return;
            }

            $translationJobs = $remainingLanguages->map(
                fn ($language) => new TranslateBlogPostToLanguageJob($sourcePost->id, $language->id, $this->data)
            )->all();

            $userId = $this->userId;
            $sourcePostId = $sourcePost->id;
            $finalizeOptions = [
                'generate_image' => $this->data['generate_image'] ?? false,
                'image_style' => $this->data['image_style'] ?? 'editorial',
                'provider' => $this->data['provider'] ?? config('ai.default'),
            ];

            Bus::batch($translationJobs)
                ->name('blog-post-translations-'.$sourcePostId)
                ->allowFailures()
                ->then(function (Batch $batch) use ($sourcePostId, $sourceStructured, $finalizeOptions, $userId): void {
                    FinalizeBlogPostGenerationJob::dispatch($sourcePostId, $sourceStructured, $finalizeOptions, $userId);
                })
                ->finally(function () use ($userId): void {
                    Cache::forget('blog_generate_'.$userId);
                })
                ->dispatch();
        } catch (\Throwable $e) {
            report($e);
            $this->clearUserGenerationFlag();
            $this->sendFailureNotification($user, $e->getMessage());
        }
    }

    public function failed(?\Throwable $exception = null): void
    {
        $this->clearUserGenerationFlag();
    }

    private function clearUserGenerationFlag(): void
    {
        Cache::forget('blog_generate_'.$this->userId);
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
