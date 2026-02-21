<?php

namespace App\Core\Console\Commands;

use App\Core\Services\TranslationFileImporter;
use Illuminate\Console\Command;
use Spatie\TranslationLoader\LanguageLine;

class ListMissingTranslationsCommand extends Command
{
    protected $signature = 'translations:missing
                            {--locale= : Only report missing translations for this locale (e.g. de, fr)}
                            {--format=text : Output format: text or json}
                            {--reference= : Locale to use as reference/source text (default: app fallback_locale)}
                            {--no-import : Skip importing from lang files before listing missing}';

    protected $description = 'List translation keys that are missing or empty per locale for AI or manual completion. Imports from lang/*.json first unless --no-import.';

    public function handle(): int
    {
        if (! $this->option('no-import')) {
            $result = app(TranslationFileImporter::class)->import();
            $this->info('Imported '.$result['total_keys'].' key(s) from '.$result['files_read'].' locale file(s).');
        }

        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        if ($locales === []) {
            $this->warn('No supported locales configured. Check config/laravellocalization.php.');

            return self::FAILURE;
        }

        $onlyLocale = $this->option('locale');
        if ($onlyLocale !== null) {
            $onlyLocale = trim($onlyLocale);
            if (! in_array($onlyLocale, $locales, true)) {
                $this->warn("Locale \"{$onlyLocale}\" is not in supported locales: ".implode(', ', $locales));

                return self::FAILURE;
            }
            $locales = [$onlyLocale];
        }

        $referenceLocale = $this->option('reference') ?? config('app.fallback_locale', 'en');
        if (! in_array($referenceLocale, array_keys(config('laravellocalization.supportedLocales', [])), true)) {
            $referenceLocale = $locales[0];
        }

        $lines = LanguageLine::query()
            ->where('group', '*')
            ->orderBy('key')
            ->get();

        $missing = [];
        foreach ($lines as $line) {
            $text = $line->text ?? [];
            $referenceText = $text[$referenceLocale] ?? null;
            if ($referenceText === null || (string) $referenceText === '') {
                foreach (array_keys(config('laravellocalization.supportedLocales', [])) as $loc) {
                    $v = $text[$loc] ?? null;
                    if ($v !== null && (string) $v !== '') {
                        $referenceText = $v;
                        break;
                    }
                }
            }
            $referenceText = $referenceText !== null ? (string) $referenceText : '';

            foreach ($locales as $locale) {
                $value = $text[$locale] ?? null;
                if ($value === null || trim((string) $value) === '') {
                    $missing[] = [
                        'key' => $line->key,
                        'locale' => $locale,
                        'reference_text' => $referenceText,
                    ];
                }
            }
        }

        $format = $this->option('format');
        if (strtolower($format) === 'json') {
            $json = json_encode(['missing' => $missing, 'count' => count($missing)], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $this->line($json !== false ? $json : '{}');

            return self::SUCCESS;
        }

        if (count($missing) === 0) {
            $this->info('No missing translations found.');

            return self::SUCCESS;
        }

        $this->info('Missing translations — paste this list to AI to get translations, then add them in Filament or via Import.');
        $this->line('Format: key | reference (source text to translate). Use --format=json for machine-readable output.');
        $this->newLine();

        $groupedByLocale = [];
        foreach ($missing as $item) {
            $groupedByLocale[$item['locale']][] = $item;
        }
        $localeNames = config('laravellocalization.supportedLocales', []);
        foreach ($groupedByLocale as $locale => $items) {
            $name = $localeNames[$locale]['native'] ?? $locale;
            $this->line('## '.$name.' ('.$locale.')');
            foreach ($items as $item) {
                $ref = str_replace(["\r", "\n"], ' ', $item['reference_text']);
                $ref = $ref !== '' ? $ref : '(no reference text)';
                $this->line('  - '.$item['key'].' | reference: '.$ref);
            }
            $this->newLine();
        }

        $this->info('Total: '.count($missing).' missing translation(s).');

        return self::SUCCESS;
    }
}
