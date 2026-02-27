<?php

namespace App\Filament\Resources\BlogPosts;

use App\Domains\Blog\Models\BlogPost;
use App\Filament\Enums\NavigationGroup;
use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\GenerateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Filament\Resources\BlogPosts\Pages\ViewBlogPost;
use App\Filament\Resources\BlogPosts\RelationManagers\TagsRelationManager;
use App\Filament\Resources\BlogPosts\Schemas\BlogPostForm;
use App\Filament\Resources\BlogPosts\Tables\BlogPostsTable;
use BackedEnum;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Blog;

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return BlogPostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogPostsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Content')
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make('slug')
                                    ->copyable(),
                                TextEntry::make('language.name')
                                    ->label('Language')
                                    ->badge(),
                                TextEntry::make('published_at')
                                    ->label('Published')
                                    ->dateTime()
                                    ->placeholder('Draft'),
                                TextEntry::make('excerpt')
                                    ->columnSpanFull(),
                                TextEntry::make('body')
                                    ->html()
                                    ->columnSpanFull(),
                                TextEntry::make('meta_description')
                                    ->columnSpanFull(),
                                TextEntry::make('tags.name')
                                    ->label('Tags')
                                    ->badge(),
                            ])
                            ->columns(2),
                        Section::make('Gallery')
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('media')
                                    ->collection('gallery')
                                    ->conversion('card')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TagsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'generate' => GenerateBlogPost::route('/generate'),
            'view' => ViewBlogPost::route('/{record}'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
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
            'Language' => $record->language?->name ?? '—',
            'Published' => $record->published_at?->format('M j, Y') ?? 'Draft',
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
