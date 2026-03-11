<?php

namespace App\Filament\Resources\LanguageLines\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\TranslationLoader\LanguageLine;

class LanguageLinesTable
{
    public static function configure(Table $table): Table
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        $localeOptions = array_combine($locales, $locales);

        return $table
            ->columns([
                TextColumn::make('group')
                    ->label(__('Group'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
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
                TextColumn::make('text_preview')
                    ->label(__('Preview'))
                    ->getStateUsing(function (LanguageLine $record): string {
                        $fallback = config('app.fallback_locale', 'en');
                        $text = $record->text ?? [];
                        $value = $text[$fallback] ?? (is_array($text) ? (string) reset($text) : '');

                        return Str::limit((string) $value, 50);
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key');
    }
}
