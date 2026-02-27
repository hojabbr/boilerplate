<?php

namespace App\Filament\Resources\ContactSubmissions;

use App\Domains\Contact\Models\ContactSubmission;
use App\Filament\Enums\NavigationGroup;
use App\Filament\Resources\ContactSubmissions\Pages\ListContactSubmissions;
use App\Filament\Resources\ContactSubmissions\Pages\ViewContactSubmission;
use App\Filament\Resources\ContactSubmissions\Schemas\ContactSubmissionForm;
use App\Filament\Resources\ContactSubmissions\Tables\ContactSubmissionsTable;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Content;

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $recordTitleAttribute = 'email';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ContactSubmissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactSubmissionsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Submission')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->copyable(),
                        TextEntry::make('subject')
                            ->placeholder('—'),
                        TextEntry::make('locale')
                            ->badge(),
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('message')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
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
            'index' => ListContactSubmissions::route('/'),
            'view' => ViewContactSubmission::route('/{record}'),
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
