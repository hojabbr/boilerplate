<?php

namespace App\Filament\Resources\LanguageLines;

use App\Filament\Enums\NavigationGroup;
use App\Filament\Resources\LanguageLines\Pages\CreateLanguageLine;
use App\Filament\Resources\LanguageLines\Pages\EditLanguageLine;
use App\Filament\Resources\LanguageLines\Pages\FillMissingTranslations;
use App\Filament\Resources\LanguageLines\Pages\ListLanguageLines;
use App\Filament\Resources\LanguageLines\Schemas\LanguageLineForm;
use App\Filament\Resources\LanguageLines\Tables\LanguageLinesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLineResource extends Resource
{
    protected static ?string $model = LanguageLine::class;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $recordTitleAttribute = 'key';

    public static function getModelLabel(): string
    {
        return __('Translation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Translations');
    }

    public static function getNavigationLabel(): string
    {
        return __('Translation Manager');
    }

    public static function form(Schema $schema): Schema
    {
        return LanguageLineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LanguageLinesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguageLines::route('/'),
            'create' => CreateLanguageLine::route('/create'),
            'fill-missing' => FillMissingTranslations::route('/fill-missing'),
            'edit' => EditLanguageLine::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canViewAny(): bool
    {
        return Gate::allows('use-translation-manager');
    }
}
