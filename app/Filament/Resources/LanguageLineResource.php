<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanguageLineResource\Pages\CreateLanguageLine;
use App\Filament\Resources\LanguageLineResource\Pages\EditLanguageLine;
use App\Filament\Resources\LanguageLineResource\Pages\ListLanguageLines;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLineResource extends Resource
{
    protected static ?string $model = LanguageLine::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 60;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedLanguage;

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
        $locales = array_keys(config('laravellocalization.supportedLocales', []));

        $components = [
            \Filament\Forms\Components\TextInput::make('group')
                ->label(__('Group'))
                ->required()
                ->maxLength(255)
                ->default('*'),
            \Filament\Forms\Components\TextInput::make('key')
                ->label(__('Key'))
                ->required()
                ->maxLength(255),
        ];

        foreach ($locales as $locale) {
            $components[] = \Filament\Forms\Components\TextInput::make('text.'.$locale)
                ->label(__('Translation').' ('.$locale.')')
                ->maxLength(65535);
        }

        return $schema->components($components);
    }

    public static function table(Table $table): Table
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        $localeOptions = array_combine($locales, $locales);

        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('group')
                    ->label(__('Group'))
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('locales')
                    ->label(__('Locales'))
                    ->badge()
                    ->separator(',')
                    ->getStateUsing(function (LanguageLine $record): array {
                        $text = $record->text ?? [];

                        return array_keys(array_filter($text, fn ($v) => $v !== null && (string) $v !== ''));
                    }),
                \Filament\Tables\Columns\TextColumn::make('text_preview')
                    ->label(__('Preview'))
                    ->getStateUsing(function (LanguageLine $record): string {
                        $fallback = config('app.fallback_locale', 'en');
                        $text = $record->text ?? [];
                        $value = $text[$fallback] ?? (is_array($text) ? (string) reset($text) : '');

                        return \Illuminate\Support\Str::limit((string) $value, 50);
                    }),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(fn (): array => LanguageLine::query()->distinct()->pluck('group', 'group')->all()),
                Filter::make('missing_in_locale')
                    ->form([
                        Select::make('locale')
                            ->label(__('Missing in locale'))
                            ->options($localeOptions)
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $locale = $data['locale'] ?? null;
                        if ($locale === null || $locale === '') {
                            return;
                        }
                        $driver = $query->getConnection()->getDriverName();
                        if ($driver === 'mysql') {
                            $query->where(function (Builder $q) use ($locale): void {
                                $q->whereNull("text->{$locale}")
                                    ->orWhere("text->{$locale}", '');
                            });
                        } elseif ($driver === 'pgsql') {
                            $query->where(function (Builder $q) use ($locale): void {
                                $q->whereNull("text->>{$locale}")
                                    ->orWhereRaw("(text->>?) = ''", [$locale]);
                            });
                        } else {
                            $query->whereRaw("(json_extract(text, ?) IS NULL OR json_extract(text, ?) = '' OR json_extract(text, ?) = '\"\"')", ['$.'.$locale, '$.'.$locale, '$.'.$locale]);
                        }
                    }),
                Filter::make('has_translation_in_locale')
                    ->form([
                        Select::make('locale')
                            ->label(__('Has translation in locale'))
                            ->options($localeOptions)
                            ->required(),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $locale = $data['locale'] ?? null;
                        if ($locale === null || $locale === '') {
                            return;
                        }
                        $driver = $query->getConnection()->getDriverName();
                        if ($driver === 'mysql') {
                            $query->whereNotNull("text->{$locale}")->where("text->{$locale}", '!=', '');
                        } elseif ($driver === 'pgsql') {
                            $query->whereNotNull("text->>{$locale}")->whereRaw("(text->>?) != ''", [$locale]);
                        } else {
                            $query->whereNotNull(DB::raw("json_extract(text, '$.{$locale}')"))->whereRaw("json_extract(text, '$.{$locale}') != '' AND json_extract(text, '$.{$locale}') != '\"\"'");
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguageLines::route('/'),
            'create' => CreateLanguageLine::route('/create'),
            'edit' => EditLanguageLine::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery();
    }

    public static function canViewAny(): bool
    {
        return \Illuminate\Support\Facades\Gate::allows('use-translation-manager');
    }
}
