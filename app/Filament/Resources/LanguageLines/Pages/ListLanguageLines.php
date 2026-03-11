<?php

namespace App\Filament\Resources\LanguageLines\Pages;

use App\Core\Services\TranslationFileImporter;
use App\Filament\Resources\LanguageLines\LanguageLineResource;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\TranslationLoader\LanguageLine;

class ListLanguageLines extends ListRecords
{
    protected static string $resource = LanguageLineResource::class;

    /**
     * For each locale, count of keys that are missing (empty or null). Only locales with count > 0.
     *
     * @return array<string, int>
     */
    public function getMissingKeysByLocale(): array
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        $lines = LanguageLine::query()->where('group', '*')->get();
        $missingByLocale = [];
        foreach ($locales as $locale) {
            $count = 0;
            foreach ($lines as $line) {
                $text = $line->text ?? [];
                $val = $text[$locale] ?? null;
                if ($val === null || (string) $val === '') {
                    $count++;
                }
            }
            if ($count > 0) {
                $missingByLocale[$locale] = $count;
            }
        }

        return $missingByLocale;
    }

    public function content(Schema $schema): Schema
    {
        $missingSummary = Html::make(function (): string {
            $missingByLocale = $this->getMissingKeysByLocale();
            if ($missingByLocale === []) {
                return '';
            }

            $supported = config('laravellocalization.supportedLocales', []);
            $localeNames = [];
            foreach (array_keys($missingByLocale) as $locale) {
                $localeNames[$locale] = $supported[$locale]['name'] ?? $locale;
            }

            return view('filament.resources.language-line-resource.pages.missing-keys-summary', [
                'missingByLocale' => $missingByLocale,
                'localeNames' => $localeNames,
                'fillMissingUrl' => static::getResource()::getUrl('fill-missing'),
            ])->render();
        });

        return $schema->components([
            Section::make(__('Missing keys'))
                ->description(__('Click a locale to open the form and fill all missing translations.'))
                ->schema([$missingSummary])
                ->visible(fn (): bool => $this->getMissingKeysByLocale() !== []),
            ...parent::content($schema)->getComponents(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearAll')
                ->label(__('Clear all translations'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('Clear all translations'))
                ->modalDescription(fn (): string => __('This will delete all :count translation line(s) from the database. You can re-import from lang files afterward. This cannot be undone.', ['count' => LanguageLine::query()->count()]))
                ->modalSubmitActionLabel(__('Clear all'))
                ->action(function (): void {
                    $count = LanguageLine::query()->count();
                    $locales = array_keys(config('laravellocalization.supportedLocales', []));
                    LanguageLine::query()->delete();
                    foreach ($locales as $locale) {
                        Cache::forget(LanguageLine::getCacheKey('*', $locale));
                    }
                    Notification::make()
                        ->title(__('Translations cleared'))
                        ->body(__(':count translation line(s) removed.', ['count' => $count]))
                        ->success()
                        ->send();
                }),
            Action::make('importFromFiles')
                ->label(__('Import from lang files'))
                ->icon('heroicon-o-arrow-down-tray')
                ->requiresConfirmation()
                ->modalHeading(__('Import from lang files'))
                ->modalDescription(__('This will merge keys from lang/*.json into the database. Existing DB values are kept for existing keys. New keys from files will be added.'))
                ->modalSubmitActionLabel(__('Import'))
                ->action(function (): void {
                    $result = app(TranslationFileImporter::class)->import();
                    Notification::make()
                        ->title(__('Imported from lang files'))
                        ->body(__('Imported :count key(s) from :files locale file(s).', ['count' => $result['total_keys'], 'files' => $result['files_read']]))
                        ->success()
                        ->send();
                }),
            Action::make('exportToFiles')
                ->label(__('Export to lang files'))
                ->icon('heroicon-o-arrow-up-tray')
                ->requiresConfirmation()
                ->modalHeading(__('Export to lang files'))
                ->modalDescription(__('This will write all database translations to lang/*.json, overwriting those files.'))
                ->modalSubmitActionLabel(__('Export'))
                ->action(function (): void {
                    $lines = LanguageLine::query()->where('group', '*')->get();
                    $byLocale = [];
                    foreach ($lines as $line) {
                        $text = $line->text ?? [];
                        foreach ($text as $locale => $value) {
                            if (! isset($byLocale[$locale])) {
                                $byLocale[$locale] = [];
                            }
                            $byLocale[$locale][$line->key] = $value;
                        }
                    }
                    $langPath = lang_path();
                    $filesWritten = 0;
                    $totalKeys = 0;
                    foreach ($byLocale as $locale => $keys) {
                        ksort($keys);
                        $totalKeys += count($keys);
                        $file = $langPath.DIRECTORY_SEPARATOR.$locale.'.json';
                        File::ensureDirectoryExists(dirname($file));
                        File::put($file, json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n");
                        $filesWritten++;
                    }

                    Notification::make()
                        ->title(__('Exported to lang files'))
                        ->body(__('Exported :count key(s) to :files locale file(s).', ['count' => $totalKeys, 'files' => $filesWritten]))
                        ->success()
                        ->send();
                }),
            Action::make('exportCsv')
                ->label(__('Export CSV'))
                ->icon('heroicon-o-document-arrow-down')
                ->url(route('translations.export-csv'), shouldOpenInNewTab: true),
            Action::make('importCsv')
                ->label(__('Import CSV'))
                ->icon('heroicon-o-document-arrow-up')
                ->form([
                    FileUpload::make('file')
                        ->label(__('CSV file'))
                        ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $path = $data['file'];
                    if (is_array($path)) {
                        $path = $path[0] ?? null;
                    }
                    $path = is_string($path) ? $path : null;
                    if ($path === null || ! Storage::exists($path)) {
                        Notification::make()
                            ->title(__('Import failed'))
                            ->body(__('Could not read uploaded file.'))
                            ->danger()
                            ->send();

                        return;
                    }
                    $fullPath = Storage::path($path);
                    $handle = fopen($fullPath, 'r');
                    if ($handle === false) {
                        Notification::make()
                            ->title(__('Import failed'))
                            ->body(__('Could not open file.'))
                            ->danger()
                            ->send();

                        return;
                    }
                    $locales = array_keys(config('laravellocalization.supportedLocales', []));
                    $header = fgetcsv($handle);
                    if ($header === false) {
                        fclose($handle);

                        return;
                    }
                    $keyIndex = 0;
                    $localeColumns = [];
                    foreach ($header as $i => $col) {
                        $col = trim((string) $col);
                        if ($col === 'key') {
                            $keyIndex = $i;
                        } elseif (in_array($col, $locales, true)) {
                            $localeColumns[$i] = $col;
                        }
                    }
                    $imported = 0;
                    while (($row = fgetcsv($handle)) !== false) {
                        $key = trim((string) ($row[$keyIndex] ?? ''));
                        if ($key === '') {
                            continue;
                        }
                        $text = [];
                        foreach ($localeColumns as $i => $locale) {
                            $value = isset($row[$i]) ? trim((string) $row[$i]) : '';
                            if ($value !== '') {
                                $text[$locale] = $value;
                            }
                        }
                        $line = LanguageLine::query()->where('group', '*')->where('key', $key)->first();
                        if ($line) {
                            $existing = $line->text ?? [];
                            $line->update(['text' => array_merge($existing, $text)]);
                        } else {
                            LanguageLine::query()->create(['group' => '*', 'key' => $key, 'text' => $text]);
                        }
                        $imported++;
                    }
                    fclose($handle);
                    Storage::delete($path);
                    Notification::make()
                        ->title(__('CSV imported'))
                        ->body(__(':count row(s) imported.', ['count' => $imported]))
                        ->success()
                        ->send();
                }),
        ];
    }
}
