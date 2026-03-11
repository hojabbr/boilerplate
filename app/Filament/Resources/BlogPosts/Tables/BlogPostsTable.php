<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use App\Filament\Resources\BlogPostSeriesResource;
use App\Filament\Support\CommonColumns;
use App\Filament\Support\CommonFilters;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('language.name')->label('Language'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('series.name')
                    ->label('Series')
                    ->placeholder('—')
                    ->url(fn ($record) => $record->blog_post_series_id ? BlogPostSeriesResource::getUrl('view', ['record' => $record->series]) : null)
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('published_at')->dateTime()->sortable(),
                CommonColumns::deletedAtColumn(),
                ...CommonColumns::timestampColumns(),
            ])
            ->filters([
                CommonFilters::languageFilter(),
                SelectFilter::make('blog_post_series_id')
                    ->label('Series')
                    ->relationship('series', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
