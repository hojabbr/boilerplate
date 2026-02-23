<?php

namespace App\Core\Console\Commands;

use App\Core\Models\Language;
use App\Core\Services\AddLocaleService;
use App\Core\Services\SupportedLocalesService;
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

    protected $description = 'Add a new locale (Language record, lang file, optional seeders). DB is source of truth; frontend uses shared props.';

    public function handle(AddLocaleService $addLocale, SupportedLocalesService $supportedLocales): int
    {
        $rollbackCode = $this->option('rollback');
        if ($rollbackCode !== null && $rollbackCode !== '') {
            return $this->rollbackLocale(strtolower(trim($rollbackCode)), $addLocale);
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

        $supportedLocalesArray = $supportedLocales->get();

        $code = text(
            label: 'Locale code',
            placeholder: 'pt, hi, pt-BR',
            required: 'Locale code is required.',
            validate: function (string $value) use ($supportedLocalesArray) {
                $v = strtolower(trim($value));
                if (strlen($v) < 2 || strlen($v) > 15) {
                    return 'Locale code must be 2–15 characters (e.g. pt, pt-BR).';
                }
                if (! preg_match('/^[a-z]{2}(-[a-zA-Z0-9]+)?$/', $v)) {
                    return 'Locale code must be valid (e.g. pt, hi).';
                }
                if (isset($supportedLocalesArray[$v])) {
                    return "Locale [{$v}] is already in the languages table.";
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
        $configContent = File::exists($configPath) ? File::get($configPath) : '';

        $name = $code;
        $script = 'Latn';
        $native = $code;
        $regional = $code.'_'.strtoupper($code);

        if ($metadataSource === 'manual') {
            $name = text(label: 'Name (e.g. Portuguese)', default: $code);
            $script = text(label: 'Script (e.g. Latn)', default: 'Latn');
            $native = text(label: 'Native name', default: $name);
            $regional = text(label: 'Regional (e.g. pt_BR)', default: $code.'_'.strtoupper($code));
        } elseif ($metadataSource === 'pick') {
            $parsed = $this->parseCommentedLocaleInConfig($configContent, $code);
            if ($parsed !== null) {
                [$name, $script, $native, $regional] = $parsed;
            } else {
                warning("No commented line found for [{$code}]. Enter manually.");
                $name = text(label: 'Name (e.g. Portuguese)', default: $code);
                $script = text(label: 'Script (e.g. Latn)', default: 'Latn');
                $native = text(label: 'Native name', default: $name);
                $regional = text(label: 'Regional (e.g. pt_BR)', default: $code.'_'.strtoupper($code));
            }
        }

        $direction = confirm('Is this an RTL language?', default: false) ? 'rtl' : 'ltr';
        $isDefault = confirm('Set as default locale?', default: false);
        $createLangFile = confirm('Create lang file (empty keys from en.json)?', default: true);
        $addToSeeders = confirm('Add placeholder entries to SettingSeeder, PageSeeder, LandingSectionSeeder, BlogPostSeeder?', default: true);

        if ($dryRun) {
            $this->plan('Create/update Language', "languages table [{$code}]");
            if ($createLangFile) {
                $this->plan('Create lang file', "lang/{$code}.json");
            }
            if ($addToSeeders) {
                $this->plan('Add to seeders', 'SettingSeeder, PageSeeder, LandingSectionSeeder, BlogPostSeeder');
            }
            $this->plan('Clear cache', SupportedLocalesService::CACHE_KEY);
            promptsTable(['Action', 'Target'], array_map(fn ($a) => [$a['action'], $a['target']], $this->plannedActions));
            outro('Dry run complete. Run without --dry-run to apply changes.');
        } else {
            $addLocale->add(
                code: $code,
                name: $name,
                script: $script,
                native: $native,
                regional: $regional,
                direction: $direction,
                isDefault: $isDefault,
                createLangFile: $createLangFile,
                addToSeeders: $addToSeeders,
            );
            info("Created/updated Language [{$code}] and cleared supported_locales cache.");
            if ($createLangFile) {
                info("Created lang/{$code}.json with empty translations.");
            }
            if ($addToSeeders) {
                info('Added placeholder entries to seeders.');
            }
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

    /**
     * @return array{0: string, 1: string, 2: string, 3: string}|null
     */
    private function parseCommentedLocaleInConfig(string $content, string $code): ?array
    {
        $escaped = preg_quote($code, '/');
        $pattern = '/\/\/\s*\''.$escaped.'\'\s*=>\s*\[\s*\'name\'\s*=>\s*\'([^\']*)\',\s*\'script\'\s*=>\s*\'([^\']*)\',\s*\'native\'\s*=>\s*\'([^\']*)\',\s*\'regional\'\s*=>\s*\'([^\']*)\'/';
        if (preg_match($pattern, $content, $m)) {
            return [$m[1], $m[2], $m[3], $m[4]];
        }

        return null;
    }

    private function rollbackLocale(string $code, AddLocaleService $addLocale): int
    {
        intro("Roll back locale [{$code}]");

        if (! Language::query()->where('code', $code)->exists()) {
            warning("Locale [{$code}] is not in the languages table. Nothing to roll back.");

            return self::SUCCESS;
        }

        $addLocale->rollback($code);
        outro("Rolled back locale [{$code}] (DB, lang file, seeders, cache).");

        return self::SUCCESS;
    }
}
