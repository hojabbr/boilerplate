<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Concerns\HasSoftDeleteActions;
use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    use HasSoftDeleteActions;

    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return $this->softDeleteHeaderActions();
    }
}
