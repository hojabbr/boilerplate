<?php

namespace App\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table as promptsTable;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class BoilerplateLocaleCommand extends Command
{
    /** @var array<int, array{action: string, target: string}> */
    private array $plannedActions = [];

    protected $signature = 'boilerplate:locale
                            {--dry-run : Show planned changes without applying}
                            {--rollback= : Roll back a previously added locale (e.g. pt, hi)}';

    protected $description = 'Add a new locale across backend config, frontend i18n, lang file, and optionally seeders.';

    public function handle(): int
    {
        $rollbackCode = $this->option('rollback');
        if ($rollbackCode !== null && $rollbackCode !== '') {
            return $this->rollbackLocale(strtolower(trim($rollbackCode)));
        }

        if (! $this->input->isInteractive()) {
            $this->error('Locale code and other options are required. Run the command interactively or use --rollback=<code> to roll back a locale.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            intro('Add locale (dry run)');
            $this->plannedActions = [];
        } else {
            intro('Add a new locale');
        }

        $supportedLocales = config('laravellocalization.supportedLocales', []);

        $code = text(
            label: 'Locale code',
            placeholder: 'pt, hi, pt-BR',
            required: 'Locale code is required.',
            validate: function (string $value) use ($supportedLocales) {
                $v = strtolower(trim($value));
                if (strlen($v) < 2 || strlen($v) > 15) {
                    return 'Locale code must be 2–15 characters (e.g. pt, pt-BR).';
                }
                if (! preg_match('/^[a-z]{2}(-[a-zA-Z0-9]+)?$/', $v)) {
                    return 'Locale code must be valid (e.g. pt, hi).';
                }
                if (isset($supportedLocales[$v])) {
                    return "Locale [{$v}] is already in supportedLocales.";
                }

                return null;
            }
        );
        $code = strtolower($code);

        $metadataSource = select(
            label: 'Source for locale metadata',
            options: [
                'pick' => 'Pick from commented locales in config',
                'manual' => 'Enter name, script, native, regional manually',
            ],
            default: 'pick'
        );

        $configPath = config_path('laravellocalization.php');
        $configContent = File::get($configPath);

        if ($metadataSource === 'pick') {
            $uncommented = $this->uncommentLocaleInConfig($configPath, $configContent, $code, $dryRun);
            if (! $uncommented) {
                warning("No commented line found for [{$code}]. Add the locale manually to config/laravellocalization.php, or choose manual entry.");
                $name = text(label: 'Name (e.g. Portuguese)', default: $code);
                $script = text(label: 'Script (e.g. Latn)', default: 'Latn');
                $native = text(label: 'Native name', default: $name);
                $regional = text(label: 'Regional (e.g. pt_BR)', default: $code.'_'.strtoupper($code));
                $this->addLocaleToConfig($configPath, $configContent, $code, $name, $script, $native, $regional, $dryRun);
            }
        } else {
            $name = text(label: 'Name (e.g. Portuguese)', default: $code);
            $script = text(label: 'Script (e.g. Latn)', default: 'Latn');
            $native = text(label: 'Native name', default: $name);
            $regional = text(label: 'Regional (e.g. pt_BR)', default: $code.'_'.strtoupper($code));
            $this->addLocaleToConfig($configPath, $configContent, $code, $name, $script, $native, $regional, $dryRun);
        }

        $this->addToI18nSupportedLngs($code, $dryRun);
        $this->createLangFile($code, $dryRun);

        $rtl = confirm('Is this an RTL language?', default: false);
        if ($rtl) {
            $this->addToRtlLocales($code, $dryRun);
        }

        $addToSeeders = confirm('Add placeholder entries to SettingSeeder, PageSeeder, LandingSectionSeeder?', default: true);
        if ($addToSeeders) {
            $this->addLocaleToSeeders($code, $dryRun);
        }

        if ($dryRun) {
            promptsTable(['Action', 'Target'], array_map(fn ($a) => [$a['action'], $a['target']], $this->plannedActions));
            outro('Dry run complete. Run without --dry-run to apply changes.');
        } else {
            outro("Locale [{$code}] has been added.");
        }
        $this->newLine();
        $this->line('Next steps:');
        $this->line('  • Run: php artisan wayfinder:generate');
        if ($addToSeeders) {
            $this->line('  • Run: php artisan db:seed (or seed specific seeders)');
        }
        $this->line('  • Translate lang/'.$code.'.json and seeder content as needed.');

        return self::SUCCESS;
    }

    private function plan(string $action, string $target): void
    {
        $this->plannedActions[] = ['action' => $action, 'target' => $target];
    }

    private function uncommentLocaleInConfig(string $configPath, string $content, string $code, bool $dryRun): bool
    {
        $escaped = preg_quote($code, '/');
        $pattern = '/^(\s*)\/\/\s*(\''.$escaped.'\'\s*=>\s*\[.+\],)\s*$/m';
        if (preg_match($pattern, $content)) {
            if ($dryRun) {
                $this->plan('Uncomment locale', "config/laravellocalization.php [{$code}]");

                return true;
            }
            $newContent = preg_replace($pattern, '$1$2', $content, 1);
            File::put($configPath, $newContent);
            info("Uncommented [{$code}] in config/laravellocalization.php.");

            return true;
        }

        return false;
    }

    private function addLocaleToConfig(string $configPath, string $content, string $code, string $name, string $script, string $native, string $regional, bool $dryRun): void
    {
        if ($dryRun) {
            $this->plan('Add locale', "config/laravellocalization.php [{$code}]");

            return;
        }
        $entry = "        '{$code}' => ['name' => '".addslashes($name)."', 'script' => '{$script}', 'native' => '".addslashes($native)."', 'regional' => '{$regional}'],";
        $needle = "    ],\n\n    // Requires middleware";
        $pos = strpos($content, $needle);
        if ($pos === false) {
            warning('Could not find supportedLocales closing in config; add the locale manually.');

            return;
        }
        $content = substr_replace($content, $entry."\n".$needle, $pos, strlen($needle));
        File::put($configPath, $content);
        info("Added [{$code}] to config/laravellocalization.php.");
    }

    private function addToI18nSupportedLngs(string $code, bool $dryRun): void
    {
        if ($dryRun) {
            $this->plan('Add to supportedLngs', 'resources/js/i18n/index.ts');

            return;
        }
        $path = resource_path('js/i18n/index.ts');
        $content = File::get($path);
        $content = preg_replace(
            "/('ko',)\s*(\n\] as const)/",
            "$1\n    '{$code}',$2",
            $content,
            1
        );
        File::put($path, $content);
        info("Added [{$code}] to resources/js/i18n/index.ts supportedLngs.");
    }

    private function addToRtlLocales(string $code, bool $dryRun): void
    {
        if ($dryRun) {
            $this->plan('Add to rtlLocales', 'resources/js/app.tsx');

            return;
        }
        $path = resource_path('js/app.tsx');
        $content = File::get($path);
        $content = preg_replace(
            "/const rtlLocales = new Set\(\['ar', 'fa'\]\)/",
            "const rtlLocales = new Set(['ar', 'fa', '{$code}'])",
            $content,
            1
        );
        File::put($path, $content);
        info("Added [{$code}] to rtlLocales in resources/js/app.tsx.");
    }

    private function createLangFile(string $code, bool $dryRun): void
    {
        $enPath = lang_path('en.json');
        $targetPath = lang_path($code.'.json');
        if (! File::exists($enPath)) {
            warning('lang/en.json not found; skipping lang file creation.');

            return;
        }
        if ($dryRun) {
            $this->plan('Create lang file', "lang/{$code}.json");

            return;
        }
        File::copy($enPath, $targetPath);
        info("Created lang/{$code}.json from lang/en.json.");
    }

    private function addLocaleToSeeders(string $code, bool $dryRun): void
    {
        if ($dryRun) {
            $this->plan('Add to seeders', 'SettingSeeder, PageSeeder, LandingSectionSeeder');

            return;
        }
        $fallback = ['tagline' => '[TODO]', 'address' => '[TODO]'];
        $this->addToSettingSeeder($code, $fallback);
        $this->addToPageSeeder($code);
        $this->addToLandingSectionSeeder($code);
        info('Added placeholder entries to SettingSeeder, PageSeeder, LandingSectionSeeder.');
    }

    /**
     * @param  array{tagline: string, address: string}  $placeholder
     */
    private function addToSettingSeeder(string $code, array $placeholder): void
    {
        $path = database_path('seeders/SettingSeeder.php');
        $content = File::get($path);
        $entry = "        '{$code}' => [\n            'tagline' => '".addslashes($placeholder['tagline'])."',\n            'address' => '".addslashes($placeholder['address'])."',\n        ],";
        $content = preg_replace(
            "/(        'hi' => \[\n            'tagline' => '[^']+',\n            'address' => '[^']+',\n        \],)\n    \];/",
            "$1\n".$entry."\n    ];",
            $content,
            1
        );
        File::put($path, $content);
    }

    private function addToPageSeeder(string $code): void
    {
        $path = database_path('seeders/PageSeeder.php');
        $content = File::get($path);
        $newEntry = "\n                '{$code}' => ['title' => '[TODO]', 'meta_title' => '[TODO]', 'meta_description' => '[TODO]'],";
        $content = preg_replace(
            "/(                'hi' => \['title' => '[^']+', 'meta_title' => '[^']+', 'meta_description' => '[^']+'\],)\n            \],/",
            '$1'.$newEntry."\n            ],",
            $content,
            -1
        );
        File::put($path, $content);
    }

    private function addToLandingSectionSeeder(string $code): void
    {
        $path = database_path('seeders/LandingSectionSeeder.php');
        $content = File::get($path);

        $content = preg_replace(
            "/(            'hi' => \['title' => '[^']+', 'subtitle' => '[^']+'\],)\n        \];/",
            "$1\n            '{$code}' => ['title' => '[TODO]', 'subtitle' => '[TODO]'],\n        ];",
            $content,
            1
        );

        $content = preg_replace(
            "/(            'hi' => \['title' => \"[^\"]+\", 'subtitle' => '[^']+', 'cta_text' => '[^']+', 'cta_url' => '[^']+'\],)\n        \];/",
            "$1\n            '{$code}' => ['title' => '[TODO]', 'subtitle' => '[TODO]', 'cta_text' => '[TODO]', 'cta_url' => '/register'],\n        ];",
            $content,
            1
        );

        $content = preg_replace(
            "/(            'hi' => \['title' => '[^']+', 'subtitle' => '[^']+'\],)\n        \];/",
            "$1\n            '{$code}' => ['title' => '[TODO]', 'subtitle' => '[TODO]'],\n        ];",
            $content,
            1
        );

        $content = preg_replace(
            "/(                    'hi' => \['title' => '[^']+', 'description' => '[^']+'\],)\n                \],/",
            "$1\n                    '{$code}' => ['title' => '[TODO]', 'description' => '[TODO]'],\n                ],",
            $content,
            -1
        );

        $content = preg_replace(
            "/(            'hi' => \['title' => '[^']+'\],)\n        \];/",
            "$1\n            '{$code}' => ['title' => '[TODO]'],\n        ];",
            $content,
            1
        );

        $content = preg_replace(
            "/(                    'hi' => \['title' => '[^']+', 'description' => \"[^\"]+\"\],)\n                \],/",
            "$1\n                    '{$code}' => ['title' => '[TODO]', 'description' => '[TODO]'],\n                ],",
            $content,
            -1
        );

        $content = preg_replace(
            "/(            'hi' => \['title' => '[^']+', 'subtitle' => '[^']+', 'cta_text' => '[^']+', 'cta_url' => '[^']+'\],)\n        \];/",
            "$1\n            '{$code}' => ['title' => '[TODO]', 'subtitle' => '[TODO]', 'cta_text' => '[TODO]', 'cta_url' => '/register'],\n        ];",
            $content,
            1
        );

        File::put($path, $content);
    }

    private function rollbackLocale(string $code): int
    {
        intro("Roll back locale [{$code}]");

        $supportedLocales = config('laravellocalization.supportedLocales', []);
        if (! isset($supportedLocales[$code])) {
            warning("Locale [{$code}] is not in supportedLocales. Nothing to roll back.");

            return self::SUCCESS;
        }

        $rolled = [];

        $configPath = config_path('laravellocalization.php');
        $configContent = File::get($configPath);
        $escaped = preg_quote($code, '/');
        if (preg_match("/\s*'{$escaped}'\s*=>\s*\[[^\]]+\],?\s*\n?/", $configContent)) {
            $configContent = preg_replace("/\s*'{$escaped}'\s*=>\s*\[[^\]]+\],?\s*\n?/", '', $configContent);
            File::put($configPath, $configContent);
            $rolled[] = ['Reverted', 'config/laravellocalization.php'];
        }

        $i18nPath = resource_path('js/i18n/index.ts');
        $i18nContent = File::get($i18nPath);
        if (str_contains($i18nContent, "'{$code}'")) {
            $i18nContent = preg_replace("/\s*'{$escaped}',?\s*\n?/", '', $i18nContent);
            File::put($i18nPath, $i18nContent);
            $rolled[] = ['Reverted', 'resources/js/i18n/index.ts'];
        }

        $langPath = lang_path($code.'.json');
        if (File::exists($langPath)) {
            File::delete($langPath);
            $rolled[] = ['Removed', "lang/{$code}.json"];
        }

        $appPath = resource_path('js/app.tsx');
        $appContent = File::get($appPath);
        if (str_contains($appContent, "'{$code}'") && str_contains($appContent, 'rtlLocales')) {
            $appContent = preg_replace("/,\s*'{$escaped}'/", '', $appContent);
            File::put($appPath, $appContent);
            $rolled[] = ['Reverted', 'resources/js/app.tsx (rtlLocales)'];
        }

        $this->removeLocaleFromSettingSeeder($code, $rolled);
        $this->removeLocaleFromPageSeeder($code, $rolled);
        $this->removeLocaleFromLandingSectionSeeder($code, $rolled);

        if ($rolled === []) {
            warning("No changes could be reverted for locale [{$code}].");
        } else {
            promptsTable(['Action', 'Target'], $rolled);
            outro("Rolled back locale [{$code}].");
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<int, array{string, string}>  $rolled
     */
    private function removeLocaleFromSettingSeeder(string $code, array &$rolled): void
    {
        $path = database_path('seeders/SettingSeeder.php');
        $content = File::get($path);
        $escaped = preg_quote($code, '/');
        $pattern = "/        '{$escaped}' => \[\s*'tagline' => '[^']*',\s*'address' => '[^']*',\s*\],?\s*\n?/";
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, '', $content);
            File::put($path, $content);
            $rolled[] = ['Reverted', 'SettingSeeder'];
        }
    }

    /**
     * @param  array<int, array{string, string}>  $rolled
     */
    private function removeLocaleFromPageSeeder(string $code, array &$rolled): void
    {
        $path = database_path('seeders/PageSeeder.php');
        $content = File::get($path);
        $escaped = preg_quote($code, '/');
        $pattern = "/\s*'{$escaped}' => \['title' => '[^']*', 'meta_title' => '[^']*', 'meta_description' => '[^']*'\],?\n?/";
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, '', $content);
            File::put($path, $content);
            $rolled[] = ['Reverted', 'PageSeeder'];
        }
    }

    /**
     * @param  array<int, array{string, string}>  $rolled
     */
    private function removeLocaleFromLandingSectionSeeder(string $code, array &$rolled): void
    {
        $path = database_path('seeders/LandingSectionSeeder.php');
        $content = File::get($path);
        $escaped = preg_quote($code, '/');
        $patterns = [
            "/\s*'{$escaped}' => \['title' => '[^']*', 'subtitle' => '[^']*'\],?\n?/",
            "/\s*'{$escaped}' => \['title' => '[^']*', 'subtitle' => '[^']*', 'cta_text' => '[^']*', 'cta_url' => '[^']*'\],?\n?/",
            "/\s*'{$escaped}' => \['title' => '[^']*'\],?\n?/",
            "/\s*'{$escaped}' => \['title' => '[^']*', 'description' => '[^']*'\],?\n?/",
        ];
        $changed = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, '', $content);
                $changed = true;
            }
        }
        if ($changed) {
            File::put($path, $content);
            $rolled[] = ['Reverted', 'LandingSectionSeeder'];
        }
    }
}
