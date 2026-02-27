<?php

namespace App\Filament\Resources\LandingSections;

use App\Domains\Landing\Models\LandingSection;
use App\Filament\Enums\NavigationGroup;
use App\Filament\Resources\LandingSections\Pages\CreateLandingSection;
use App\Filament\Resources\LandingSections\Pages\EditLandingSection;
use App\Filament\Resources\LandingSections\Pages\ListLandingSections;
use App\Filament\Resources\LandingSections\Pages\ViewLandingSection;
use App\Filament\Resources\LandingSections\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\LandingSections\Schemas\LandingSectionForm;
use App\Filament\Resources\LandingSections\Tables\LandingSectionsTable;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class LandingSectionResource extends Resource
{
    use Translatable;

    protected static ?string $model = LandingSection::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Content;

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return LandingSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingSectionsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        TextEntry::make('type')
                            ->badge(),
                        TextEntry::make('sort_order')
                            ->label('Sort order'),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        TextEntry::make('title')
                            ->placeholder('—'),
                        TextEntry::make('subtitle')
                            ->placeholder('—'),
                        TextEntry::make('cta_text')
                            ->label('CTA text')
                            ->placeholder('—'),
                        TextEntry::make('cta_url')
                            ->label('CTA URL')
                            ->url(fn (?string $state): ?string => $state)
                            ->placeholder('—'),
                        TextEntry::make('body')
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ])
                    ->columns(2),
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->placeholder('—'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'type'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Type' => $record->type ?? '—',
        ];
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingSections::route('/'),
            'create' => CreateLandingSection::route('/create'),
            'view' => ViewLandingSection::route('/{record}'),
            'edit' => EditLandingSection::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
