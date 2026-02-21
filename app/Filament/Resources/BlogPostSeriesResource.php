<?php

namespace App\Filament\Resources;

use App\Domains\Blog\Models\BlogPostSeries;
use App\Filament\Resources\BlogPostSeriesResource\Pages\EditScheduledSeries;
use App\Filament\Resources\BlogPostSeriesResource\Pages\ListScheduledSeries;
use App\Filament\Resources\BlogPostSeriesResource\Pages\ViewScheduledSeries;
use App\Filament\Resources\BlogPostSeriesResource\RelationManagers\BlogPostsRelationManager;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BlogPostSeriesResource extends Resource
{
    protected static ?string $model = BlogPostSeries::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = \Filament\Support\Icons\Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Scheduled series';

    protected static ?string $modelLabel = 'Scheduled series';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_active')
                    ->label('')
                    ->boolean()
                    ->trueIcon(\Filament\Support\Icons\Heroicon::OutlinedCheckCircle)
                    ->falseIcon(\Filament\Support\Icons\Heroicon::OutlinedXCircle)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('name')
                    ->label('Name')
                    ->placeholder('—')
                    ->weight(FontWeight::SemiBold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(35)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('days_of_week')
                    ->label('Days')
                    ->formatStateUsing(fn (array|int|null $state) => implode(', ', array_map(fn ($d) => $dayNames[$d] ?? (string) $d, is_array($state) ? $state : ($state !== null ? [$state] : []))))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('run_at_hours')
                    ->label('Hours')
                    ->formatStateUsing(fn (array|int|null $state) => implode(', ', array_map(fn ($h) => sprintf('%02d:00', (int) $h), is_array($state) ? $state : ($state !== null ? [$state] : []))))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('posts_generated')
                    ->label('Posts')
                    ->suffix(fn ($record) => $record->total_posts_limit ? " / {$record->total_posts_limit}" : '')
                    ->sortable(),
                TextColumn::make('last_run_at')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->default('1'),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedNoSymbol)
                    ->color('gray')
                    ->visible(fn (BlogPostSeries $record) => $record->is_active)
                    ->action(fn (BlogPostSeries $record) => $record->update(['is_active' => false]))
                    ->modalHeading('Deactivate scheduled series')
                    ->modalDescription('This will stop the series from running. You can reactivate it from the Edit page.')
                    ->successNotificationTitle('Series deactivated')
                    ->requiresConfirmation(),
            ])
            ->recordUrl(fn (BlogPostSeries $record) => static::getUrl('view', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [
            BlogPostsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScheduledSeries::route('/'),
            'view' => ViewScheduledSeries::route('/{record}'),
            'edit' => EditScheduledSeries::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
