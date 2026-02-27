<?php

namespace App\Filament\Resources;

use App\Domains\Blog\Models\BlogPostSeries;
use App\Filament\Enums\NavigationGroup;
use App\Filament\Resources\BlogPostSeriesResource\Pages\EditScheduledSeries;
use App\Filament\Resources\BlogPostSeriesResource\Pages\ListScheduledSeries;
use App\Filament\Resources\BlogPostSeriesResource\Pages\ViewScheduledSeries;
use App\Filament\Resources\BlogPostSeriesResource\RelationManagers\BlogPostsRelationManager;
use App\Filament\Resources\BlogPostSeriesResource\Tables\BlogPostSeriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BlogPostSeriesResource extends Resource
{
    protected static ?string $model = BlogPostSeries::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Blog;

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Scheduled series';

    protected static ?string $modelLabel = 'Scheduled series';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return BlogPostSeriesTable::configure($table);
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
