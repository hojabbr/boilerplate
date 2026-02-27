<?php

namespace App\Filament\Widgets;

use App\Domains\Contact\Models\ContactSubmission;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactSubmissionsCount extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $total = ContactSubmission::query()->count();
        $last7Days = collect(range(0, 6))
            ->map(fn (int $daysAgo): int => ContactSubmission::query()
                ->whereDate('created_at', Carbon::today()->subDays($daysAgo))
                ->count())
            ->reverse()
            ->values()
            ->all();

        return [
            Stat::make('Contact submissions', $total)
                ->chart($last7Days)
                ->description('Last 7 days'),
        ];
    }
}
