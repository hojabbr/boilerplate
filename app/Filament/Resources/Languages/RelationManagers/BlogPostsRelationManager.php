<?php

namespace App\Filament\Resources\Languages\RelationManagers;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlogPostsRelationManager extends RelationManager
{
    protected static string $relationship = 'blogPosts';

    protected static ?string $title = 'Blog posts';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->placeholder('Draft')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->label('Edit post')
                    ->url(fn ($record) => BlogPostResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
