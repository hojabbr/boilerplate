<?php

namespace App\Filament\Resources\BlogPostSeriesResource\Pages;

use App\Filament\Resources\BlogPostSeriesResource;
use Filament\Resources\Pages\ListRecords;

class ListScheduledSeries extends ListRecords
{
    protected static string $resource = BlogPostSeriesResource::class;
}
