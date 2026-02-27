<?php

namespace App\Filament\Widgets;

use App\Domains\Blog\Models\BlogPost;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogPostStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = BlogPost::query()->count();
        $published = BlogPost::query()->whereNotNull('published_at')->count();
        $draft = $total - $published;
        $thisMonth = BlogPost::query()
            ->whereBetween('published_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->count();

        return [
            Stat::make('Total posts', $total),
            Stat::make('Published', $published)
                ->description('Draft: '.$draft),
            Stat::make('Published this month', $thisMonth),
        ];
    }
}
