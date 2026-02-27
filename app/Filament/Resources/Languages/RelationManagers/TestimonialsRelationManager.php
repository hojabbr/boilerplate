<?php

namespace App\Filament\Resources\Languages\RelationManagers;

use App\Filament\Resources\Testimonials\TestimonialResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialsRelationManager extends RelationManager
{
    protected static string $relationship = 'testimonials';

    protected static ?string $title = 'Testimonials';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('author')
            ->columns([
                TextColumn::make('author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quote')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->label('Edit testimonial')
                    ->url(fn ($record) => TestimonialResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
