<?php

namespace App\Core\Services;

use Illuminate\Support\Facades\File;
use Spatie\TranslationLoader\LanguageLine;

class TranslationFileImporter
{
    /**
     * Merge keys from lang/*.json into the database (group *). Existing DB values are kept.
     *
     * @return array{total_keys: int, files_read: int}
     */
    public function import(): array
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        $langPath = lang_path();
        $totalKeys = 0;
        $filesRead = 0;

        foreach ($locales as $locale) {
            $file = $langPath.DIRECTORY_SEPARATOR.$locale.'.json';
            if (! File::isFile($file)) {
                continue;
            }
            $filesRead++;
            $content = File::get($file);
            $decoded = json_decode($content, true);
            if (! is_array($decoded)) {
                continue;
            }
            foreach ($decoded as $key => $value) {
                $totalKeys++;
                $line = LanguageLine::query()
                    ->where('group', '*')
                    ->where('key', $key)
                    ->first();
                $text = $line !== null ? ($line->text ?? []) : [];
                $text[$locale] = (string) $value;
                if ($line) {
                    $line->update(['text' => $text]);
                } else {
                    LanguageLine::query()->create([
                        'group' => '*',
                        'key' => $key,
                        'text' => $text,
                    ]);
                }
            }
        }

        return ['total_keys' => $totalKeys, 'files_read' => $filesRead];
    }
}
