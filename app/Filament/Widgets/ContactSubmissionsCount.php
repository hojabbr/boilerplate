<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactSubmissionsCount extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Contact submissions', ContactSubmission::count()),
        ];
    }
}
