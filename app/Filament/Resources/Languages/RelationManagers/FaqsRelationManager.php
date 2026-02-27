<?php

namespace App\Filament\Resources\Languages\RelationManagers;

use App\Filament\Resources\Faqs\FaqResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FaqsRelationManager extends RelationManager
{
    protected static string $relationship = 'faqs';

    protected static ?string $title = 'FAQs';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                TextColumn::make('question')
                    ->limit(60)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->label('Edit FAQ')
                    ->url(fn ($record) => FaqResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
