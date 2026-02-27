<?php

namespace App\Filament\Resources\Testimonials;

use App\Domains\Testimonial\Models\Testimonial;
use App\Filament\Enums\NavigationGroup;
use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Testimonials\Pages\ViewTestimonial;
use App\Filament\Resources\Testimonials\Schemas\TestimonialForm;
use App\Filament\Resources\Testimonials\Tables\TestimonialsTable;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Content;

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $recordTitleAttribute = 'author';

    public static function form(Schema $schema): Schema
    {
        return TestimonialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TestimonialsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Testimonial')
                    ->schema([
                        TextEntry::make('language.name')
                            ->label('Language')
                            ->badge(),
                        TextEntry::make('sort_order')
                            ->label('Sort order'),
                        TextEntry::make('author'),
                        TextEntry::make('role')
                            ->placeholder('—'),
                        TextEntry::make('quote')
                            ->columnSpanFull(),
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
        return ['author', 'quote'];
    }

    /**
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Language' => $record->language?->name ?? '—',
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
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'view' => ViewTestimonial::route('/{record}'),
            'edit' => EditTestimonial::route('/{record}/edit'),
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
