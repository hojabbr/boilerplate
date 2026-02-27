<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTestimonial extends ViewRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return array_merge(
            [EditAction::make()],
            $this->softDeleteHeaderActions(),
        );
    }
}
