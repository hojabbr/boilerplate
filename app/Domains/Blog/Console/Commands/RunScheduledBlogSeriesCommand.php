<?php

namespace App\Domains\Blog\Console\Commands;

use App\Domains\Blog\Jobs\GenerateBlogPostsJob;
use App\Domains\Blog\Models\BlogPostSeries;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunScheduledBlogSeriesCommand extends Command
{
    protected $signature = 'blog:run-scheduled-series';

    protected $description = 'Run due scheduled blog series (dispatches generation job for each due series). Call hourly from the scheduler.';

    public function handle(): int
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentHour = (int) $now->format('G');
        $currentDayOfWeek = (int) $now->format('w'); // 0 = Sunday

        $due = BlogPostSeries::query()
            ->active()
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereJsonContains('days_of_week', $currentDayOfWeek)
            ->whereJsonContains('run_at_hours', $currentHour)
            ->where(function ($q): void {
                $q->whereNull('total_posts_limit')
                    ->orWhereColumn('posts_generated', '<', 'total_posts_limit');
            })
            ->where(function ($q) use ($now): void {
                $q->whereNull('last_run_at')
                    ->orWhere('last_run_at', '<', $now->copy()->startOfHour());
            })
            ->get();

        foreach ($due as $series) {
            $data = [
                'topic_source' => 'series',
                'series_id' => $series->id,
                'series_purpose' => $series->purpose ?? '',
                'series_objective' => $series->objective ?? '',
                'series_topics' => $series->topics ?? '',
                'length' => $series->length,
                'provider' => $series->provider,
                'language_ids' => $series->language_ids,
                'generate_image' => $series->generate_image,
                'generate_audio' => $series->generate_audio,
                'publish_immediately' => $series->publish_immediately,
            ];

            GenerateBlogPostsJob::dispatch($data, $series->user_id);

            $series->increment('posts_generated');
            $series->update(['last_run_at' => $now]);
            $series->refresh();

            if ($series->total_posts_limit !== null && $series->posts_generated >= $series->total_posts_limit) {
                $series->update(['is_active' => false]);
            }
        }

        return self::SUCCESS;
    }
}
