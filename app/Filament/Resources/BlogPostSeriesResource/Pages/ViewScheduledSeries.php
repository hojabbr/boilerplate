<?php

namespace App\Filament\Resources\BlogPostSeriesResource\Pages;

use App\Filament\Resources\BlogPostSeriesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScheduledSeries extends ViewRecord
{
    protected static string $resource = BlogPostSeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
