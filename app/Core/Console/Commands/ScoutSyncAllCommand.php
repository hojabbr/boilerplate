<?php

namespace App\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ScoutSyncAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:sync-all
            {--fresh : Flush each model index before importing}
            {--c|chunk= : The number of records to import at a time per model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all configured searchable models into the search index';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $indexSettings = config('scout.meilisearch.index-settings', []);

        if (empty($indexSettings)) {
            $this->warn('No searchable models found in config/scout.php (meilisearch.index-settings).');

            return self::SUCCESS;
        }

        $classes = array_keys(array_filter($indexSettings, function ($key): bool {
            return is_string($key) && class_exists($key);
        }, ARRAY_FILTER_USE_KEY));

        if (empty($classes)) {
            $this->warn('No valid model classes in meilisearch.index-settings.');

            return self::SUCCESS;
        }

        $options = array_filter([
            '--fresh' => $this->option('fresh'),
            '--chunk' => $this->option('chunk'),
        ]);

        foreach ($classes as $class) {
            $this->info("Importing [{$class}]...");
            Artisan::call('scout:import', array_merge(['model' => $class], $options));
            $this->line(trim(Artisan::output()));
        }

        $this->info('All configured searchable models have been imported.');

        return self::SUCCESS;
    }
}
