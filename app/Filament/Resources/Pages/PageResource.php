<?php

namespace App\Filament\Resources\Pages;

use App\Domains\Page\Models\Page;
use App\Filament\Enums\NavigationGroup;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Pages\ViewPage;
use App\Filament\Resources\Pages\Schemas\PageForm;
use App\Filament\Resources\Pages\Tables\PagesTable;
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

class PageResource extends Resource
{
    use Translatable;

    protected static ?string $model = Page::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Content;

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        TextEntry::make('slug')
                            ->copyable(),
                        TextEntry::make('title'),
                        TextEntry::make('type')
                            ->badge(),
                        TextEntry::make('order'),
                        TextEntry::make('meta_title')
                            ->placeholder('—'),
                        TextEntry::make('meta_description')
                            ->placeholder('—'),
                        TextEntry::make('body')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Settings')
                    ->schema([
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        IconEntry::make('show_in_navigation')
                            ->label('Show in navigation')
                            ->boolean(),
                        IconEntry::make('show_in_footer')
                            ->label('Show in footer')
                            ->boolean(),
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
        return ['title', 'slug'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Type' => $record->type ?? '—',
            'Active' => $record->is_active ? 'Yes' : 'No',
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view' => ViewPage::route('/{record}'),
            'edit' => EditPage::route('/{record}/edit'),
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
