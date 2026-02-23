<?php

namespace App\Core\Services;

use App\Core\Models\Language;
use Illuminate\Support\Facades\File;

/**
 * Shared "add locale" flow: create/update Language, optional lang file, optional seeder placeholders, clear cache.
 * Used by BoilerplateLocaleCommand and Filament Add Language wizard.
 */
class AddLocaleService
{
    public function __construct(
        private readonly SupportedLocalesService $supportedLocales,
    ) {}

    /**
     * Add a new locale: Language record, optional lang file, optional seeder placeholders; then clear cache.
     */
    public function add(
        string $code,
        string $name,
        string $script,
        string $native,
        string $regional,
        string $direction,
        bool $isDefault,
        bool $createLangFile,
        bool $addToSeeders,
    ): void {
        $code = strtolower($code);
        if (! in_array($direction, ['ltr', 'rtl'], true)) {
            $direction = 'ltr';
        }

        if ($isDefault) {
            Language::query()->where('is_default', true)->update(['is_default' => false]);
        }

        $sortOrder = (int) Language::query()->max('sort_order') + 1;

        Language::updateOrCreate(
            ['code' => $code],
            [
                'name' => $native ?: $name,
                'script' => $script ?: 'Latn',
                'regional' => $regional ?: '',
                'direction' => $direction,
                'is_default' => $isDefault,
                'is_enabled' => true,
                'sort_order' => $sortOrder,
            ]
        );

        if ($createLangFile) {
            $this->createLangFile($code);
        }

        if ($addToSeeders) {
            $this->addToSettingSeeder($code, ['tagline' => '[TODO]', 'address' => '[TODO]']);
            $this->addToPageSeeder($code);
            $this->addToLandingSectionSeeder($code);
            $this->addToBlogPostSeeder($code);
        }

        $this->supportedLocales->clearCache();
    }

    /**
     * Roll back a locale: remove from DB (force delete), lang file, and seeders; clear cache.
     */
    public function rollback(string $code): void
    {
        $code = strtolower($code);
        $language = Language::query()->where('code', $code)->first();
        if ($language) {
            $language->forceDelete();
        }

        $langPath = lang_path($code.'.json');
        if (File::exists($langPath)) {
            File::delete($langPath);
        }

        $this->removeFromSettingSeeder($code);
        $this->removeFromPageSeeder($code);
        $this->removeFromLandingSectionSeeder($code);
        $this->removeFromBlogPostSeeder($code);

        $this->supportedLocales->clearCache();
    }

    private function createLangFile(string $code): void
    {
        $enPath = lang_path('en.json');
        $targetPath = lang_path($code.'.json');
        if (! File::exists($enPath)) {
            return;
        }
        $enKeys = array_keys(json_decode(File::get($enPath), true) ?? []);
        $empty = array_fill_keys($enKeys, '');
        File::put($targetPath, json_encode($empty, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)."\n");
    }

    /**
     * @param  array{tagline: string, address: string}  $placeholder
     */
    private function addToSettingSeeder(string $code, array $placeholder): void
    {
        $path = database_path('seeders/SettingSeeder.php');
        if (! File::exists($path)) {
            return;
        }
        $content = File::get($path);
        $entry = "        '{$code}' => [\n            'tagline' => '".addslashes($placeholder['tagline'])."',\n            'address' => '".addslashes($placeholder['address'])."',\n        ],";
        $content = preg_replace(
            "/(        'hi' => \[\n            'tagline' => '[^']+',\n            'address' => '[^']+',\n        \],)\n    \];/",
            "$1\n".$entry."\n    ];",
            $content,
            1
        );
        if ($content !== null) {
            File::put($path, $content);
        }
    }

    private function addToPageSeeder(string $code): void
    {
        $path = database_path('seeders/PageSeeder.php');
        if (! File::exists($path)) {
            return;
        }
        $content = File::get($path);
        $newEntry = "\n                '{$code}' => ['title' => '[TODO]', 'meta_title' => '[TODO]', 'meta_description' => '[TODO]'],";
        $content = preg_replace(
            "/(                'hi' => \['title' => '[^']+', 'meta_title' => '[^']+', 'meta_description' => '[^']+'\],)\n            \],/",
            '$1'.$newEntry."\n            ],",
            $content,
            -1
        );
        if ($content !== null) {
            File::put($path, $content);
        }
    }

    private function addToLandingSectionSeeder(string $code): void
    {
        $path = database_path('seeders/LandingSectionSeeder.php');
        if (! File::exists($path)) {
            return;
        }
        $content = File::get($path);
        $escaped = preg_quote($code, '/');
        $patterns = [
            "/(            'hi' => \['title' => '[^']+', 'subtitle' => '[^']+'\],)\n        \];/",
            "/(            'hi' => \['title' => \"[^\"]+\", 'subtitle' => '[^']+', 'cta_text' => '[^']+', 'cta_url' => '[^']+'\],)\n        \];/",
            "/(            'hi' => \['title' => '[^']+', 'subtitle' => '[^']+'\],)\n        \];/",
            "/(                    'hi' => \['title' => '[^']+', 'description' => '[^']+'\],)\n                \],/",
            "/(            'hi' => \['title' => '[^']+'\],)\n        \];/",
            "/(                    'hi' => \['title' => '[^']+', 'description' => \"[^\"]+\"\],)\n                \],/",
            "/(            'hi' => \['title' => '[^']+', 'subtitle' => '[^']+', 'cta_text' => '[^']+', 'cta_url' => '[^']+'\],)\n        \];/",
        ];
        $replacements = [
            "$1\n            '{$code}' => ['title' => '[TODO]', 'subtitle' => '[TODO]'],\n        ];",
            "$1\n            '{$code}' => ['title' => '[TODO]', 'subtitle' => '[TODO]', 'cta_text' => '[TODO]', 'cta_url' => '/register'],\n        ];",
            "$1\n            '{$code}' => ['title' => '[TODO]', 'subtitle' => '[TODO]'],\n        ];",
            "$1\n                    '{$code}' => ['title' => '[TODO]', 'description' => '[TODO]'],\n                ],",
            "$1\n            '{$code}' => ['title' => '[TODO]'],\n        ];",
            "$1\n                    '{$code}' => ['title' => '[TODO]', 'description' => '[TODO]'],\n                ],",
            "$1\n            '{$code}' => ['title' => '[TODO]', 'subtitle' => '[TODO]', 'cta_text' => '[TODO]', 'cta_url' => '/register'],\n        ];",
        ];
        foreach ($patterns as $i => $pattern) {
            $content = preg_replace($pattern, $replacements[$i], $content, 1);
        }
        File::put($path, $content);
    }

    private function addToBlogPostSeeder(string $code): void
    {
        $path = database_path('seeders/BlogPostSeeder.php');
        if (! File::exists($path)) {
            return;
        }
        $content = File::get($path);
        $entry = "        '{$code}' => [\n            'title' => '[TODO]',\n            'excerpt' => '[TODO]',\n            'body' => '<p>[TODO]</p>',\n            'meta_description' => '[TODO]',\n        ],";
        $content = preg_replace(
            "/(        'ko' => \[\n            'title' => '[^']+',\n            'excerpt' => '[^']+',\n            'body' => '[^']+',\n            'meta_description' => '[^']+',\n        \],)\n    \];/",
            "$1\n".$entry."\n    ];",
            $content,
            1
        );
        if ($content !== null) {
            File::put($path, $content);
        }
    }

    private function removeFromSettingSeeder(string $code): void
    {
        $path = database_path('seeders/SettingSeeder.php');
        if (! File::exists($path)) {
            return;
        }
        $content = File::get($path);
        $escaped = preg_quote($code, '/');
        $pattern = "/        '{$escaped}' => \[\s*'tagline' => '[^']*',\s*'address' => '[^']*',\s*\],?\s*\n?/";
        $content = preg_replace($pattern, '', $content);
        if ($content !== null) {
            File::put($path, $content);
        }
    }

    private function removeFromPageSeeder(string $code): void
    {
        $path = database_path('seeders/PageSeeder.php');
        if (! File::exists($path)) {
            return;
        }
        $content = File::get($path);
        $escaped = preg_quote($code, '/');
        $pattern = "/\s*'{$escaped}' => \['title' => '[^']*', 'meta_title' => '[^']*', 'meta_description' => '[^']*'\],?\n?/";
        $content = preg_replace($pattern, '', $content);
        if ($content !== null) {
            File::put($path, $content);
        }
    }

    private function removeFromLandingSectionSeeder(string $code): void
    {
        $path = database_path('seeders/LandingSectionSeeder.php');
        if (! File::exists($path)) {
            return;
        }
        $content = File::get($path);
        $escaped = preg_quote($code, '/');
        $patterns = [
            "/\s*'{$escaped}' => \['title' => '[^']*', 'subtitle' => '[^']*'\],?\n?/",
            "/\s*'{$escaped}' => \['title' => '[^']*', 'subtitle' => '[^']*', 'cta_text' => '[^']*', 'cta_url' => '[^']*'\],?\n?/",
            "/\s*'{$escaped}' => \['title' => '[^']*'\],?\n?/",
            "/\s*'{$escaped}' => \['title' => '[^']*', 'description' => '[^']*'\],?\n?/",
        ];
        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        if ($content !== null) {
            File::put($path, $content);
        }
    }

    private function removeFromBlogPostSeeder(string $code): void
    {
        $path = database_path('seeders/BlogPostSeeder.php');
        if (! File::exists($path)) {
            return;
        }
        $content = File::get($path);
        $escaped = preg_quote($code, '/');
        $pattern = "/        '{$escaped}' => \[\s*'title' => '[^']*',\s*'excerpt' => '[^']*',\s*'body' => '[^']*',\s*'meta_description' => '[^']*',\s*\],?\s*\n?/";
        $content = preg_replace($pattern, '', $content);
        if ($content !== null) {
            File::put($path, $content);
        }
    }
}
