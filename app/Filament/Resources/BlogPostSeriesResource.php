<?php

namespace App\Filament\Resources;

use App\Domains\Blog\Models\BlogPostSeries;
use App\Filament\Resources\BlogPostSeriesResource\Pages\ListScheduledSeries;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BlogPostSeriesResource extends Resource
{
    protected static ?string $model = BlogPostSeries::class;

    protected static \UnitEnum|string|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Scheduled series';

    protected static ?string $modelLabel = 'Scheduled series';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->placeholder('—'),
                \Filament\Tables\Columns\TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(40),
                \Filament\Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                \Filament\Tables\Columns\TextColumn::make('end_date')
                    ->date(),
                \Filament\Tables\Columns\TextColumn::make('days_of_week')
                    ->label('Days')
                    ->formatStateUsing(fn (array|int|null $state) => implode(', ', array_map(fn ($d) => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$d] ?? (string) $d, is_array($state) ? $state : ($state !== null ? [$state] : [])))),
                \Filament\Tables\Columns\TextColumn::make('run_at_hours')
                    ->label('Hours')
                    ->formatStateUsing(fn (array|int|null $state) => implode(', ', array_map(fn ($h) => sprintf('%02d:00', (int) $h), is_array($state) ? $state : ($state !== null ? [$state] : [])))),
                \Filament\Tables\Columns\TextColumn::make('posts_generated')
                    ->label('Posts')
                    ->suffix(fn ($record) => $record->total_posts_limit ? " / {$record->total_posts_limit}" : ''),
                \Filament\Tables\Columns\TextColumn::make('last_run_at')
                    ->dateTime()
                    ->placeholder('Never'),
            ])
            ->recordActions([
                \Filament\Actions\DeleteAction::make()
                    ->action(fn (BlogPostSeries $record) => $record->update(['is_active' => false]))
                    ->label('Deactivate')
                    ->modalHeading('Deactivate scheduled series')
                    ->modalDescription('This will stop the series from running. You can leave it deactivated or delete the record from the database.')
                    ->successNotificationTitle('Series deactivated'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScheduledSeries::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
