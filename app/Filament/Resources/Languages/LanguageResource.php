<?php

namespace App\Filament\Resources\Languages;

use App\Core\Models\Language;
use App\Filament\Enums\NavigationGroup;
use App\Filament\Resources\Languages\Pages\AddLanguage;
use App\Filament\Resources\Languages\Pages\EditLanguage;
use App\Filament\Resources\Languages\Pages\ListLanguages;
use App\Filament\Resources\Languages\Pages\ViewLanguage;
use App\Filament\Resources\Languages\RelationManagers\BlogPostsRelationManager;
use App\Filament\Resources\Languages\RelationManagers\FaqsRelationManager;
use App\Filament\Resources\Languages\RelationManagers\TestimonialsRelationManager;
use App\Filament\Resources\Languages\Schemas\LanguageForm;
use App\Filament\Resources\Languages\Tables\LanguagesTable;
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

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LanguageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LanguagesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Language')
                    ->schema([
                        TextEntry::make('code')
                            ->copyable(),
                        TextEntry::make('name'),
                        TextEntry::make('script')
                            ->placeholder('—'),
                        TextEntry::make('regional')
                            ->placeholder('—'),
                        TextEntry::make('direction')
                            ->badge(),
                        TextEntry::make('sort_order')
                            ->label('Sort order'),
                        IconEntry::make('is_default')
                            ->label('Default')
                            ->boolean(),
                        IconEntry::make('is_enabled')
                            ->label('Enabled')
                            ->boolean(),
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
        return ['name', 'code'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Code' => $record->code ?? '—',
        ];
    }

    public static function getRelations(): array
    {
        return [
            BlogPostsRelationManager::class,
            FaqsRelationManager::class,
            TestimonialsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguages::route('/'),
            'create' => AddLanguage::route('/create'),
            'view' => ViewLanguage::route('/{record}'),
            'edit' => EditLanguage::route('/{record}/edit'),
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
