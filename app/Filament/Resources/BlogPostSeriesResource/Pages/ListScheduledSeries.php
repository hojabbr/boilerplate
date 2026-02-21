<?php

namespace App\Filament\Resources\BlogPostSeriesResource\Pages;

use App\Filament\Resources\BlogPostSeriesResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListScheduledSeries extends ListRecords
{
    protected static string $resource = BlogPostSeriesResource::class;

    protected function getTableQuery(): ?Builder
    {
        return parent::getTableQuery()->where('user_id', auth()->id())->where('is_active', true);
    }
}
