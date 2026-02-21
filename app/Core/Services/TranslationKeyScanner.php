<?php

namespace App\Core\Services;

use Illuminate\Support\Facades\File;
use Spatie\TranslationLoader\LanguageLine;

class TranslationKeyScanner
{
    /** @var array<string, string> */
    private array $patterns = [
        '__' => '__\s*\(\s*[\'"]([^\'"]+)[\'"]',
        'trans' => 'trans\s*\(\s*[\'"]([^\'"]+)[\'"]',
        '@lang' => '@lang\s*\(\s*[\'"]([^\'"]+)[\'"]',
    ];

    /**
     * Paths to exclude from scanning (e.g. Translation Manager so its own UI strings are not added as keys).
     *
     * @var array<string>
     */
    private array $excludedPaths = [
        'app/Filament/Resources/LanguageLineResource',
    ];

    /**
     * Scan paths for translation keys and optionally persist missing ones.
     *
     * @param  array<string>|null  $overridePaths  If provided, only these paths are scanned (for testing).
     * @return array{found: int, added: int, to_add_keys: list<string>}
     */
    public function scan(bool $persist = true, ?array $overridePaths = null): array
    {
        $paths = $overridePaths ?? [
            base_path('app'),
            resource_path('views'),
            resource_path('js'),
        ];
        $extensions = ['php', 'blade.php', 'ts', 'tsx', 'js', 'jsx'];
        $keys = $this->scanPaths($paths, $extensions);
        $existing = LanguageLine::query()
            ->where('group', '*')
            ->pluck('key')
            ->flip()
            ->all();
        $toAdd = array_diff_key($keys, $existing);
        $toAddKeys = array_keys($toAdd);
        $fallbackFile = lang_path(config('app.fallback_locale', 'en').'.json');
        $fallbackTranslations = $this->loadFallbackTranslations($fallbackFile);

        $added = 0;
        if ($persist) {
            foreach ($toAddKeys as $key) {
                $text = [];
                if (isset($fallbackTranslations[$key])) {
                    $fallbackLocale = config('app.fallback_locale', 'en');
                    $text[$fallbackLocale] = (string) $fallbackTranslations[$key];
                }
                LanguageLine::query()->create([
                    'group' => '*',
                    'key' => $key,
                    'text' => $text,
                ]);
                $added++;
            }
        }

        return ['found' => count($keys), 'added' => $added, 'to_add_keys' => $toAddKeys];
    }

    /**
     * @param  array<int, string>  $paths
     * @param  array<int, string>  $extensions
     * @return array<string, true>
     */
    private function scanPaths(array $paths, array $extensions): array
    {
        $keys = [];
        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }
            $files = File::allFiles($path);
            foreach ($files as $file) {
                $pathname = $file->getPathname();
                if ($this->isExcludedPath($pathname)) {
                    continue;
                }
                $ext = $file->getExtension();
                if ($ext === 'php' && str_contains($file->getFilename(), '.blade.')) {
                    $ext = 'blade.php';
                } elseif (! in_array($ext, $extensions, true)) {
                    continue;
                }
                $content = File::get($pathname);
                foreach ($this->patterns as $pattern) {
                    if (preg_match_all('/'.$pattern.'/', $content, $matches)) {
                        foreach ($matches[1] as $key) {
                            $key = trim($key);
                            if ($key !== '' && ! str_contains($key, '{')) {
                                $keys[$key] = true;
                            }
                        }
                    }
                }
            }
        }

        return $keys;
    }

    private function isExcludedPath(string $pathname): bool
    {
        $normalized = str_replace('\\', '/', $pathname);
        $base = str_replace('\\', '/', base_path());

        foreach ($this->excludedPaths as $excluded) {
            $fullExcluded = $base.'/'.trim($excluded, '/');
            if (str_starts_with($normalized, $fullExcluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, string>
     */
    private function loadFallbackTranslations(string $path): array
    {
        if (! File::isFile($path)) {
            return [];
        }
        $decoded = json_decode(File::get($path), true);

        return is_array($decoded) ? $decoded : [];
    }
}
