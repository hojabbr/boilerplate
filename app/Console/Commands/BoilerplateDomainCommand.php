<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\pause;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table as promptsTable;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

class BoilerplateDomainCommand extends Command
{
    /** @var array<int, array{action: string, target: string}> */
    private array $plannedActions = [];

    private function isDryRun(): bool
    {
        return (bool) $this->option('dry-run');
    }

    private function plan(string $action, string $target): void
    {
        $this->plannedActions[] = ['action' => $action, 'target' => $target];
    }

    protected $signature = 'boilerplate:domain
                            {--dry-run : Show planned actions without making changes}
                            {--rollback= : Roll back a previously scaffolded domain (PascalCase name, e.g. Price)}';

    protected $description = 'Scaffold a new domain (backend slice, optional model, Filament resource, Pennant feature flag, frontend feature module).';

    public function handle(): int
    {
        $rollbackName = $this->option('rollback');
        if ($rollbackName !== null && $rollbackName !== '') {
            return $this->rollbackDomain(trim($rollbackName));
        }

        if (! $this->input->isInteractive()) {
            $this->error('Domain name and other options are required. Run the command interactively or use --rollback=<Name> to roll back a domain.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        if ($dryRun) {
            intro('Scaffold domain (dry run)');
            $this->plannedActions = [];
        } else {
            intro('Scaffold a new domain');
        }

        $name = text(
            label: 'Domain name (PascalCase, singular)',
            placeholder: 'Product, Event, Page',
            default: '',
            required: 'Domain name is required.',
            validate: function (string $value): ?string {
                if (! preg_match('/^[A-Z][a-zA-Z0-9]*$/', $value)) {
                    return 'Domain name must be PascalCase (e.g. Product, Event).';
                }
                if (Str::singular($value) !== $value) {
                    return 'Domain name must be singular (e.g. '.Str::singular($value).', not '.$value.').';
                }

                return null;
            }
        );
        $path = text(
            label: 'Route path (kebab-case)',
            placeholder: Str::kebab($name),
            default: Str::kebab($name),
            validate: function (string $value) {
                $v = trim(Str::lower($value), '/');

                return $v && preg_match('/^[a-z0-9-]+$/', $v) ? null : 'Route path must be kebab-case (e.g. products, events).';
            }
        );
        $path = trim(Str::lower($path), '/');

        $createModel = confirm('Create Eloquent model?', default: true);
        $tableName = null;
        $translatable = false;
        $searchable = false;
        $modelClass = null;
        $migrationFirst = false;
        /** @var array{tableName: string, softDeletes: bool, columns: array<int, array{name: string, type: string}>, translatable: bool}|null $parsedMigration */
        $parsedMigration = null;

        if ($createModel) {
            $singular = Str::singular($name);
            $defaultTable = Str::snake(Str::plural($singular));
            $tableName = text(
                label: 'Table name',
                default: $defaultTable,
                validate: fn (string $value) => preg_match('/^[a-z0-9_]+$/', $value)
                    ? null
                    : 'Table name must be snake_case (e.g. products, blog_posts).'
            );
            $migrationFirst = select(
                label: 'Create migration first (edit it, then we infer schema) or have the command generate the migration?',
                options: [
                    'first' => 'Create migration first (I\'ll edit it)',
                    'generate' => 'Command generates the migration',
                ],
                default: 'generate'
            ) === 'first';

            if ($migrationFirst && ! $this->isDryRun()) {
                $migrationName = 'create_'.$tableName.'_table';
                Artisan::call('make:migration', ['name' => $migrationName, '--no-interaction' => true]);
                info(trim(Artisan::output()));
                $migrationPath = $this->getLatestMigrationPath($migrationName);
                if ($migrationPath) {
                    info('Edit the migration file to add your columns (id, timestamps, softDeletes are optional; we will detect them).');
                    pause('Press ENTER when done editing to continue.');
                    $parsedMigration = $this->parseMigrationFile($migrationPath);
                    if ($parsedMigration) {
                        $tableName = $parsedMigration['tableName'];
                        $translatable = $parsedMigration['translatable'];
                    } else {
                        warning('Could not parse the migration; using default table name and title/body columns.');
                        $parsedMigration = [
                            'tableName' => $tableName,
                            'softDeletes' => false,
                            'columns' => [['name' => 'title', 'type' => 'string'], ['name' => 'body', 'type' => 'text']],
                            'translatable' => false,
                        ];
                    }
                }
            } elseif ($this->isDryRun() && $migrationFirst) {
                $parsedMigration = [
                    'tableName' => $tableName,
                    'softDeletes' => false,
                    'columns' => [['name' => 'title', 'type' => 'string'], ['name' => 'body', 'type' => 'text']],
                    'translatable' => false,
                ];
            } else {
                $translatable = confirm('Use Spatie Translatable?', default: false);
            }

            $searchable = confirm('Make model searchable with Scout/Meilisearch?', default: false);
            $modelClass = "App\\Domains\\{$name}\\Models\\{$singular}";
        }

        $createFilament = $createModel && confirm('Create Filament resource for the model?', default: true);
        $gateWithFeature = confirm('Gate with Pennant feature flag?', default: false);
        $createFrontend = confirm('Create frontend feature module?', default: true);

        $featureKey = Str::kebab($name);

        $this->createDomainBase($name, $path, $gateWithFeature ? $featureKey : null, $createFrontend);

        if ($createModel && $tableName) {
            if ($parsedMigration) {
                $this->createModelFromParsedMigration($name, $modelClass, $parsedMigration, $searchable);
            } else {
                $this->createModel($name, $modelClass, $tableName, $translatable, $searchable);
            }
            if ($searchable) {
                $this->addToScoutConfig($modelClass);
            }
            if ($createFilament) {
                $columns = $parsedMigration['columns'] ?? null;
                $this->createFilamentResource($name, $modelClass, $path, $translatable, $columns);
                $this->addPermissionToSeeder($path);
            }
        }

        if ($gateWithFeature) {
            $this->createFeatureFlag($name, $featureKey);
        }

        if ($createFrontend) {
            $this->createFrontendModule($path, $name);
        }

        if ($this->isDryRun()) {
            promptsTable(['Action', 'Target'], array_map(fn ($a) => [$a['action'], $a['target']], $this->plannedActions));
            outro('Dry run complete. Run without --dry-run to apply changes.');
            $this->echoNextSteps($createModel, $searchable, $modelClass, $gateWithFeature);
        } else {
            outro("Domain [{$name}] has been scaffolded.");
            $this->echoNextSteps($createModel, $searchable, $modelClass, $gateWithFeature);
        }

        return self::SUCCESS;
    }

    private function echoNextSteps(bool $createModel, bool $searchable, ?string $modelClass, bool $gateWithFeature): void
    {
        $this->newLine();
        $this->line('Next steps:');
        $this->line('  • Run: php artisan wayfinder:generate');
        if ($createModel) {
            $this->line('  • Run: php artisan migrate (if you added a new migration)');
            if ($searchable && $modelClass) {
                $this->line('  • Run: php artisan scout:import "'.$modelClass.'"');
            }
        }
        if ($gateWithFeature) {
            $this->line('  • Add the feature to config/features.php toggleable array if not already present, then run: php artisan db:seed --class=FeatureFlagSeeder');
        }
    }

    private function createDomainBase(string $name, string $path, ?string $featureKey, bool $createFrontend): void
    {
        $controllerClass = "{$name}Controller";
        $dir = app_path("Domains/{$name}/Http/Controllers");
        if ($this->isDryRun()) {
            $this->plan('Create controller', "Domains/{$name}/Http/Controllers/{$controllerClass}.php");
            $this->plan('Register route', "GET /{$path} in routes/web.php");

            return;
        }
        $namespace = "App\\Domains\\{$name}\\Http\\Controllers";
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $controllerClass = "{$name}Controller";
        $viewName = "'{$path}/Index'";
        $featureCheck = $featureKey
            ? "        if (! \\Laravel\\Pennant\\Feature::active('{$featureKey}')) {\n            abort(404);\n        }\n\n        "
            : '';

        $body = $createFrontend
            ? "        \$setting = \\App\\Core\\Models\\Setting::site();\n        \$settings = \$pageProps->settingsSlice(\$setting);\n        \$features = \$pageProps->featuresArray();\n        return \\Inertia\\Inertia::render({$viewName}, [\n            'settings' => \$settings,\n            'features' => \$features,\n            'seo' => ['title' => __('{$name}'), 'description' => ''],\n        ]);"
            : "        return \\Inertia\\Inertia::render({$viewName}, ['seo' => ['title' => __('{$name}'), 'description' => '']]);";

        $param = $createFrontend ? 'Request $request, PagePropsServiceInterface $pageProps' : 'Request $request';
        $uses = $createFrontend
            ? "use App\\Core\\Contracts\\PagePropsServiceInterface;\nuse App\\Core\\Http\\Controllers\\Controller;"
            : 'use App\Core\Http\Controllers\Controller;';

        $controllerContent = <<<PHP
<?php

namespace {$namespace};

{$uses}
use Illuminate\Http\Request;
use Inertia\Response;

class {$controllerClass} extends Controller
{
    public function __invoke({$param}): Response
    {
{$featureCheck}{$body}
    }
}

PHP;
        File::put("{$dir}/{$controllerClass}.php", $controllerContent);
        info("Created Domains/{$name}/Http/Controllers/{$controllerClass}.php");

        $this->registerRoute($path, $name);
    }

    private function registerRoute(string $path, string $name): void
    {
        $webPath = base_path('routes/web.php');
        $content = File::get($webPath);
        $controllerFqcn = "App\\Domains\\{$name}\\Http\\Controllers\\{$name}Controller";
        $routeLine = "        Route::get('{$path}', {$name}Controller::class)->name('{$path}');";
        $insertBefore = '        require __DIR__.\'/settings.php\';';
        if (str_contains($content, $name.'Controller::class')) {
            return;
        }
        $content = str_replace(
            $insertBefore,
            $routeLine."\n\n        ".$insertBefore,
            $content
        );
        $content = str_replace(
            'use App\Domains\Search\Http\Controllers\SearchController;',
            "use App\\Domains\\Search\\Http\\Controllers\\SearchController;\nuse {$controllerFqcn};",
            $content
        );
        File::put($webPath, $content);
        info("Registered route GET /{$path} in routes/web.php");
    }

    private function createModel(string $name, string $modelClass, string $tableName, bool $translatable, bool $searchable): void
    {
        $shortName = class_basename($modelClass);
        $migrationName = 'create_'.$tableName.'_table';
        if ($this->isDryRun()) {
            $this->plan('Create migration', "database/migrations/*_{$migrationName}.php");
            $this->plan('Create model', "Domains/{$name}/Models/{$shortName}.php");

            return;
        }
        $dir = app_path("Domains/{$name}/Models");
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        Artisan::call('make:migration', ['name' => $migrationName, '--no-interaction' => true]);
        info(trim(Artisan::output()));

        $traits = ['HasFactory', 'SoftDeletes'];
        if ($translatable) {
            $traits[] = 'HasTranslations';
        }
        if ($searchable) {
            $traits[] = 'Searchable';
        }
        $traitsUse = implode(', ', $traits);
        $imports = [
            'use Illuminate\Database\Eloquent\Model;',
            'use Illuminate\Database\Eloquent\Factories\HasFactory;',
            'use Illuminate\Database\Eloquent\SoftDeletes;',
        ];
        if ($translatable) {
            $imports[] = 'use Spatie\Translatable\HasTranslations;';
        }
        if ($searchable) {
            $imports[] = 'use Laravel\Scout\Searchable;';
        }
        $importBlock = implode("\n", $imports);
        $translatableProp = $translatable ? "\n    public \$translatable = ['title', 'body'];\n" : '';
        $searchableMethod = $searchable ? "\n    public function toSearchableArray(): array\n    {\n        return \$this->toArray();\n    }\n" : '';

        $modelContent = <<<PHP
<?php

namespace App\Domains\\{$name}\Models;

{$importBlock}

class {$shortName} extends Model
{
    use {$traitsUse};
{$translatableProp}

    protected \$table = '{$tableName}';

    protected \$fillable = ['title', 'body'];
{$searchableMethod}
}

PHP;
        File::put(app_path('Domains/'.$name.'/Models/'.$shortName.'.php'), $modelContent);
        info("Created Domains/{$name}/Models/{$shortName}.php");

        $migrationPath = $this->getLatestMigrationPath($migrationName);
        if ($migrationPath) {
            $migrationContent = File::get($migrationPath);
            $defaultSchema = "\$table->string('title');\n            \$table->text('body')->nullable();";
            if ($translatable) {
                $defaultSchema = "\$table->json('title')->nullable();\n            \$table->json('body')->nullable();";
            }
            $migrationContent = preg_replace(
                '/Schema::create\([^,]+,\s*function \([^)]+\) \{[^}]*\}/s',
                "Schema::create('{$tableName}', function (\$table) {\n            \$table->id();\n            {$defaultSchema}\n            \$table->timestamps();\n            \$table->softDeletes();\n        });",
                $migrationContent,
                1
            );
            File::put($migrationPath, $migrationContent);
        }
    }

    private function getLatestMigrationPath(string $partialName): ?string
    {
        $files = File::glob(database_path('migrations/*'.$partialName.'.php'));
        rsort($files);

        return $files[0] ?? null;
    }

    /**
     * @return array{tableName: string, softDeletes: bool, columns: array<int, array{name: string, type: string}>, translatable: bool}|null
     */
    private function parseMigrationFile(string $path): ?array
    {
        $content = File::get($path);
        if (! preg_match("/Schema::create\s*\(\s*['\"]([^'\"]+)['\"]/", $content, $tableMatch)) {
            return null;
        }
        $tableName = $tableMatch[1];
        $softDeletes = (bool) preg_match('/\$table\s*->\s*softDeletes\s*\(\s*\)/', $content);
        $columns = [];
        $skip = ['id', 'created_at', 'updated_at', 'deleted_at'];
        if (preg_match_all('/\$table\s*->\s*(\w+)\s*\(\s*[\'"]([a-z_][a-z0-9_]*)[\'"]\s*\)/i', $content, $colMatches, PREG_SET_ORDER)) {
            foreach ($colMatches as $m) {
                $type = strtolower($m[1]);
                $name = $m[2];
                if (in_array($name, $skip, true)) {
                    continue;
                }
                $columns[] = ['name' => $name, 'type' => $type];
            }
        }
        if ($columns === []) {
            $columns = [['name' => 'title', 'type' => 'string'], ['name' => 'body', 'type' => 'text']];
        }
        $jsonNames = array_column(array_filter($columns, fn ($c) => $c['type'] === 'json'), 'name');
        $translatable = count($jsonNames) >= 1 && array_intersect($jsonNames, ['title', 'body', 'name', 'description']) !== [];

        return [
            'tableName' => $tableName,
            'softDeletes' => $softDeletes,
            'columns' => $columns,
            'translatable' => $translatable,
        ];
    }

    /**
     * @param  array{tableName: string, softDeletes: bool, columns: array<int, array{name: string, type: string}>, translatable: bool}  $parsed
     */
    private function createModelFromParsedMigration(string $name, string $modelClass, array $parsed, bool $searchable): void
    {
        $shortName = class_basename($modelClass);
        if ($this->isDryRun()) {
            $this->plan('Create model (from migration)', "Domains/{$name}/Models/{$shortName}.php");

            return;
        }
        $dir = app_path("Domains/{$name}/Models");
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $shortName = class_basename($modelClass);
        $tableName = $parsed['tableName'];
        $traits = ['HasFactory'];
        if ($parsed['softDeletes']) {
            $traits[] = 'SoftDeletes';
        }
        if ($parsed['translatable']) {
            $traits[] = 'HasTranslations';
        }
        if ($searchable) {
            $traits[] = 'Searchable';
        }
        $traitsUse = implode(', ', $traits);
        $imports = [
            'use Illuminate\Database\Eloquent\Model;',
            'use Illuminate\Database\Eloquent\Factories\HasFactory;',
        ];
        if ($parsed['softDeletes']) {
            $imports[] = 'use Illuminate\Database\Eloquent\SoftDeletes;';
        }
        if ($parsed['translatable']) {
            $imports[] = 'use Spatie\Translatable\HasTranslations;';
        }
        if ($searchable) {
            $imports[] = 'use Laravel\Scout\Searchable;';
        }
        $importBlock = implode("\n", $imports);
        $fillable = array_column($parsed['columns'], 'name');
        $fillableStr = "'".implode("', '", $fillable)."'";
        $translatableProp = $parsed['translatable']
            ? "\n    public \$translatable = [".$fillableStr."];\n"
            : '';
        $searchableMethod = $searchable ? "\n    public function toSearchableArray(): array\n    {\n        return \$this->toArray();\n    }\n" : '';

        $modelContent = <<<PHP
<?php

namespace App\Domains\\{$name}\Models;

{$importBlock}

class {$shortName} extends Model
{
    use {$traitsUse};
{$translatableProp}

    protected \$table = '{$tableName}';

    protected \$fillable = [{$fillableStr}];
{$searchableMethod}
}

PHP;
        File::put(app_path('Domains/'.$name.'/Models/'.$shortName.'.php'), $modelContent);
        info("Created Domains/{$name}/Models/{$shortName}.php (from migration).");
    }

    private function addToScoutConfig(string $modelClass): void
    {
        if ($this->isDryRun()) {
            $this->plan('Add to Scout config', 'config/scout.php (meilisearch.index-settings)');

            return;
        }
        $path = config_path('scout.php');
        $content = File::get($path);
        $entry = '            '.$modelClass.'::class => [],';
        $needle = '            \App\Domains\Page\Models\Page::class => [],';
        if (str_contains($content, $modelClass)) {
            return;
        }
        $content = str_replace(
            $needle,
            $entry."\n".$needle,
            $content
        );
        File::put($path, $content);
        info('Added model to config/scout.php meilisearch.index-settings.');
    }

    /**
     * @param  array<int, array{name: string, type: string}>|null  $columns
     */
    private function createFilamentResource(string $name, string $modelClass, string $path, bool $translatable, ?array $columns = null): void
    {
        $resourceName = Str::plural($name);
        $singularName = Str::singular($name);
        if ($this->isDryRun()) {
            $this->plan('Create Filament resource', "Filament/Resources/{$resourceName}/ (Resource, Schemas, Tables, Pages)");

            return;
        }
        $resourceDir = app_path("Filament/Resources/{$resourceName}");
        if (! File::isDirectory($resourceDir)) {
            File::makeDirectory($resourceDir, 0755, true);
        }
        File::makeDirectory($resourceDir.'/Pages', 0755, true);
        File::makeDirectory($resourceDir.'/Schemas', 0755, true);
        File::makeDirectory($resourceDir.'/Tables', 0755, true);

        $listPage = "List{$resourceName}";
        $createPage = "Create{$singularName}";
        $editPage = "Edit{$singularName}";
        $formSchema = "{$singularName}Form";
        $tableClass = "{$resourceName}Table";

        $recordTitleAttr = $this->recordTitleAttributeFromColumns($columns);
        $headerActions = $translatable
            ? "\n    public static function getHeaderActions(): array\n    {\n        return [\n            \\LaraZeus\\SpatieTranslatable\\LocaleSwitcher::make(),\n        ];\n    }\n"
            : '';

        $modelShortName = class_basename($modelClass);
        $resourceContent = <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName};

use {$modelClass};
use App\Filament\Resources\\{$resourceName}\Pages\\{$createPage};
use App\Filament\Resources\\{$resourceName}\Pages\\{$editPage};
use App\Filament\Resources\\{$resourceName}\Pages\\{$listPage};
use App\Filament\Resources\\{$resourceName}\Schemas\\{$formSchema};
use App\Filament\Resources\\{$resourceName}\Tables\\{$tableClass};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class {$resourceName}Resource extends Resource
{
    protected static ?string \$model = {$modelShortName}::class;

    protected static ?string \$navigationGroup = 'CMS';

    protected static ?string \$recordTitleAttribute = '{$recordTitleAttr}';
{$headerActions}

    public static function form(Schema \$schema): Schema
    {
        return {$formSchema}::configure(\$schema);
    }

    public static function table(Table \$table): Table
    {
        return {$tableClass}::configure(\$table);
    }

    public static function getPages(): array
    {
        return [
            'index' => {$listPage}::route('/'),
            'create' => {$createPage}::route('/create'),
            'edit' => {$editPage}::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
PHP;
        File::put("{$resourceDir}/{$resourceName}Resource.php", $resourceContent);

        $formComponents = $this->filamentFormComponentsFromColumns($columns);
        $formContent = <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName}\Schemas;

use Filament\Schemas\Schema;

class {$formSchema}
{
    public static function configure(Schema \$schema): Schema
    {
        return \$schema
            ->components([
{$formComponents}
            ]);
    }
}
PHP;
        File::put("{$resourceDir}/Schemas/{$formSchema}.php", $formContent);

        $tableColumns = $this->filamentTableColumnsFromColumns($columns);
        $tableContent = <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName}\Tables;

use Filament\Tables\Table;

class {$tableClass}
{
    public static function configure(Table \$table): Table
    {
        return \$table
            ->columns([
{$tableColumns}
            ])
            ->filters([
                //
            ])
            ->actions([
                \\Filament\\Tables\\Actions\\EditAction::make(),
            ]);
    }
}
PHP;
        File::put("{$resourceDir}/Tables/{$tableClass}.php", $tableContent);

        $this->writeFilamentResourcePages($resourceDir, $resourceName, $listPage, $createPage, $editPage);

        info("Created Filament resource {$resourceName} (List/Create/Edit).");
    }

    /**
     * @param  array<int, array{name: string, type: string}>|null  $columns
     */
    private function recordTitleAttributeFromColumns(?array $columns): string
    {
        if ($columns !== null && $columns !== []) {
            foreach (['title', 'name', 'label'] as $prefer) {
                foreach ($columns as $c) {
                    if ($c['name'] === $prefer) {
                        return $prefer;
                    }
                }
            }

            return $columns[0]['name'];
        }

        return 'title';
    }

    /**
     * @param  array<int, array{name: string, type: string}>|null  $columns
     */
    private function filamentFormComponentsFromColumns(?array $columns): string
    {
        if ($columns === null || $columns === []) {
            return "                \\Filament\\Forms\\Components\\TextInput::make('title')->required(),\n                \\Filament\\Forms\\Components\\RichEditor::make('body'),";
        }
        $lines = [];
        foreach ($columns as $c) {
            $name = $c['name'];
            $type = $c['type'];
            if (in_array($type, ['text', 'longText'], true)) {
                $lines[] = "                \\Filament\\Forms\\Components\\RichEditor::make('{$name}'),";
            } elseif ($type === 'json') {
                $lines[] = "                \\Filament\\Forms\\Components\\TextInput::make('{$name}'),";
            } elseif (in_array($type, ['date', 'dateTime'], true)) {
                $lines[] = "                \\Filament\\Forms\\Components\\DateTimePicker::make('{$name}'),";
            } elseif ($type === 'boolean') {
                $lines[] = "                \\Filament\\Forms\\Components\\Toggle::make('{$name}'),";
            } else {
                $req = ($name === 'title' || $name === 'name') ? '->required()' : '';
                $lines[] = "                \\Filament\\Forms\\Components\\TextInput::make('{$name}'){$req},";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, array{name: string, type: string}>|null  $columns
     */
    private function filamentTableColumnsFromColumns(?array $columns): string
    {
        $lines = [];
        if ($columns !== null && $columns !== []) {
            foreach ($columns as $c) {
                $name = $c['name'];
                $type = $c['type'];
                if (in_array($type, ['date', 'dateTime'], true)) {
                    $lines[] = "                \\Filament\\Tables\\Columns\\TextColumn::make('{$name}')->dateTime(),";
                } elseif ($type === 'boolean') {
                    $lines[] = "                \\Filament\\Tables\\Columns\\IconColumn::make('{$name}')->boolean(),";
                } else {
                    $lines[] = "                \\Filament\\Tables\\Columns\\TextColumn::make('{$name}'),";
                }
            }
        } else {
            $lines[] = "                \\Filament\\Tables\\Columns\\TextColumn::make('title'),";
        }
        $lines[] = "                \\Filament\\Tables\\Columns\\TextColumn::make('created_at')->dateTime(),";

        return implode("\n", $lines);
    }

    private function writeFilamentResourcePages(string $resourceDir, string $resourceName, string $listPage, string $createPage, string $editPage): void
    {
        $listPageContent = <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName}\Pages;

use App\Filament\Resources\\{$resourceName}\\{$resourceName}Resource;
use Filament\Resources\Pages\ListRecords;

class {$listPage} extends ListRecords
{
    protected static string \$resource = {$resourceName}Resource::class;
}
PHP;
        File::put("{$resourceDir}/Pages/{$listPage}.php", $listPageContent);

        $createPageContent = <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName}\Pages;

use App\Filament\Resources\\{$resourceName}\\{$resourceName}Resource;
use Filament\Resources\Pages\CreateRecord;

class {$createPage} extends CreateRecord
{
    protected static string \$resource = {$resourceName}Resource::class;
}
PHP;
        File::put("{$resourceDir}/Pages/{$createPage}.php", $createPageContent);

        $editPageContent = <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName}\Pages;

use App\Filament\Resources\\{$resourceName}\\{$resourceName}Resource;
use Filament\Resources\Pages\EditRecord;

class {$editPage} extends EditRecord
{
    protected static string \$resource = {$resourceName}Resource::class;
}
PHP;
        File::put("{$resourceDir}/Pages/{$editPage}.php", $editPageContent);
    }

    private function addPermissionToSeeder(string $path): void
    {
        if ($this->isDryRun()) {
            $this->plan('Add permission', "RoleAndPermissionSeeder (manage {$path})");

            return;
        }
        $seederPath = database_path('seeders/RoleAndPermissionSeeder.php');
        $content = File::get($seederPath);
        $permission = "'manage {$path}'";
        if (str_contains($content, $permission)) {
            return;
        }
        $content = preg_replace(
            "/(\$permissions = \[)(\n.*?'manage feature flags',)/s",
            "$1\n            {$permission},$2",
            $content,
            1
        );
        File::put($seederPath, $content);
        info("Added permission \"manage {$path}\" to RoleAndPermissionSeeder.");
    }

    private function createFeatureFlag(string $name, string $featureKey): void
    {
        $className = "{$name}Feature";
        if ($this->isDryRun()) {
            $this->plan('Create feature class', "Core/Features/{$className}.php");
            $this->plan('Register feature', 'AppServiceProvider + config/features.php');

            return;
        }
        $dir = app_path('Core/Features');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        $content = <<<PHP
<?php

namespace App\Core\Features;

class {$className}
{
    public function __invoke(mixed \$scope): mixed
    {
        return true;
    }
}

PHP;
        File::put("{$dir}/{$className}.php", $content);

        $providerPath = app_path('Core/Providers/AppServiceProvider.php');
        $providerContent = File::get($providerPath);
        $defineLine = "Feature::define('{$featureKey}', {$className}::class);";
        if (str_contains($providerContent, $defineLine)) {
            return;
        }
        $providerContent = str_replace(
            "Feature::define('contact-form', ContactFormFeature::class);",
            "Feature::define('contact-form', ContactFormFeature::class);\n        ".$defineLine,
            $providerContent
        );
        $providerContent = str_replace(
            'use App\Core\Features\ContactFormFeature;',
            "use App\\Core\\Features\\ContactFormFeature;\nuse App\\Core\\Features\\{$className};",
            $providerContent
        );
        File::put($providerPath, $providerContent);

        $configPath = config_path('features.php');
        $configContent = File::get($configPath);
        $entry = "'{$featureKey}' => '".str_replace('_', ' ', Str::title($featureKey))."',";
        if (! str_contains($configContent, "'{$featureKey}'")) {
            $configContent = str_replace(
                "'contact-form' => 'Contact form',",
                "'contact-form' => 'Contact form',\n        ".$entry,
                $configContent
            );
            File::put($configPath, $configContent);
        }
        info("Created {$className} and registered in AppServiceProvider and config/features.php.");
    }

    private function createFrontendModule(string $path, string $name): void
    {
        if ($this->isDryRun()) {
            $this->plan('Create frontend module', "resources/js/features/{$path}/ (pages/Index.tsx, types.ts, index.ts)");

            return;
        }
        $baseDir = resource_path("js/features/{$path}");
        File::makeDirectory($baseDir, 0755, true);
        File::makeDirectory($baseDir.'/pages', 0755, true);

        $indexContent = <<<'TS'
export type { DomainIndexProps } from './types';
TS;
        File::put("{$baseDir}/index.ts", $indexContent);

        $typesContent = <<<'TS'
export interface DomainIndexProps {
    seo?: { title?: string; description?: string };
}
TS;
        File::put("{$baseDir}/types.ts", $typesContent);

        $componentName = Str::studly($path).'Index';
        $indexTsx = <<<TSX
import { m } from 'motion/react';
import { pageEnter } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import PublicLayout, {
    EMPTY_PUBLIC_FEATURES,
    EMPTY_PUBLIC_SETTINGS,
    type PublicFeatures,
    type PublicSettings,
} from '@/layouts/public-layout';
import type { DomainIndexProps } from '../types';

export default function {$componentName}({
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
}: DomainIndexProps & {
    settings?: PublicSettings;
    features?: PublicFeatures;
}) {
    return (
        <PublicLayout settings={settings} features={features}>
            <m.main
                variants={pageEnter.variants}
                initial="hidden"
                animate="visible"
                className="container mx-auto px-4 py-12"
            >
                <SeoHead title={seo?.title} description={seo?.description} />
                <h1 className="text-2xl font-semibold">{seo?.title ?? '{$name}'}</h1>
                <p className="mt-2 text-muted-foreground">Scaffolded domain page. Replace with your content.</p>
            </m.main>
        </PublicLayout>
    );
}

TSX;
        File::put("{$baseDir}/pages/Index.tsx", $indexTsx);
        info("Created resources/js/features/{$path}/ (pages/Index.tsx, types.ts, index.ts).");
    }

    private function rollbackDomain(string $name): int
    {
        if (! preg_match('/^[A-Z][a-zA-Z0-9]*$/', $name)) {
            $this->error('Domain name must be PascalCase (e.g. Price, Products).');

            return self::FAILURE;
        }

        intro("Roll back domain [{$name}]");

        $singular = Str::singular($name);
        $plural = Str::plural($name);
        $path = Str::kebab($name);
        $tableName = Str::snake($plural);
        $featureKey = Str::kebab($name);
        $modelClass = "App\\Domains\\{$name}\\Models\\{$singular}";
        $controllerPath = app_path("Domains/{$name}/Http/Controllers/{$name}Controller.php");
        $modelPath = app_path("Domains/{$name}/Models/{$singular}.php");
        $featurePath = app_path("Core/Features/{$name}Feature.php");
        $resourceDir = app_path("Filament/Resources/{$plural}");
        $frontendDir = resource_path("js/features/{$path}");
        $migrationGlob = database_path("migrations/*create_{$tableName}_table.php");
        $migrations = File::glob($migrationGlob);

        $rolled = [];

        if (File::exists($controllerPath)) {
            File::delete($controllerPath);
            $rolled[] = ['Removed', $controllerPath];
        }
        $webPath = base_path('routes/web.php');
        $webContent = File::get($webPath);
        $routeLine = "        Route::get('{$path}', {$name}Controller::class)->name('{$path}');";
        $controllerFqcn = "App\\Domains\\{$name}\\Http\\Controllers\\{$name}Controller";
        if (str_contains($webContent, $name.'Controller::class')) {
            $webContent = str_replace([$routeLine."\n\n        ", "\n        ".$routeLine."\n", $routeLine."\n", $routeLine], '', $webContent);
            $webContent = preg_replace("/use {$controllerFqcn};?\n?/", '', $webContent);
            File::put($webPath, $webContent);
            $rolled[] = ['Reverted route', 'routes/web.php'];
        }
        if (File::exists($modelPath)) {
            File::delete($modelPath);
            $rolled[] = ['Removed', $modelPath];
        }
        foreach ($migrations as $m) {
            File::delete($m);
            $rolled[] = ['Removed migration', $m];
        }
        if (File::isDirectory($resourceDir)) {
            File::deleteDirectory($resourceDir);
            $rolled[] = ['Removed', "Filament/Resources/{$plural}/"];
        }
        if (File::exists($featurePath)) {
            File::delete($featurePath);
            $rolled[] = ['Removed', $featurePath];
        }
        $providerPath = app_path('Core/Providers/AppServiceProvider.php');
        $providerContent = File::get($providerPath);
        $defineLine = "Feature::define('{$featureKey}', {$name}Feature::class);";
        if (str_contains($providerContent, $defineLine)) {
            $providerContent = str_replace("\n        ".$defineLine, '', $providerContent);
            $providerContent = str_replace("use App\\Core\\Features\\{$name}Feature;\n", '', $providerContent);
            File::put($providerPath, $providerContent);
            $rolled[] = ['Reverted', 'AppServiceProvider (Feature::define)'];
        }
        $configPath = config_path('features.php');
        $configContent = File::get($configPath);
        $entry = "'{$featureKey}' => '".str_replace('_', ' ', Str::title($featureKey))."',";
        if (str_contains($configContent, $entry)) {
            $configContent = str_replace("\n        ".$entry, '', $configContent);
            File::put($configPath, $configContent);
            $rolled[] = ['Reverted', 'config/features.php'];
        }
        $scoutPath = config_path('scout.php');
        $scoutContent = File::get($scoutPath);
        $scoutLine = '            '.$modelClass.'::class => [],';
        if (str_contains($scoutContent, $modelClass)) {
            $scoutContent = str_replace("\n".$scoutLine, '', $scoutContent);
            File::put($scoutPath, $scoutContent);
            $rolled[] = ['Reverted', 'config/scout.php'];
        }
        $seederPath = database_path('seeders/RoleAndPermissionSeeder.php');
        $seederContent = File::get($seederPath);
        $perm = "'manage {$path}'";
        if (str_contains($seederContent, $perm)) {
            $seederContent = preg_replace("/\s*{$perm},\n?/", '', $seederContent);
            File::put($seederPath, $seederContent);
            $rolled[] = ['Reverted', 'RoleAndPermissionSeeder'];
        }
        if (File::isDirectory($frontendDir)) {
            File::deleteDirectory($frontendDir);
            $rolled[] = ['Removed', "resources/js/features/{$path}/"];
        }

        if ($rolled === []) {
            warning("No scaffolded artifacts found for domain [{$name}].");
        } else {
            promptsTable(['Action', 'Target'], $rolled);
            outro("Rolled back domain [{$name}].");
        }

        return self::SUCCESS;
    }
}
