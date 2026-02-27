<?php

namespace App\Filament\Widgets;

use App\Domains\Auth\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $total = User::query()->count();
        $newThisWeek = User::query()
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        return [
            Stat::make('Total users', $total),
            Stat::make('New this week', $newThisWeek),
        ];
    }
}
