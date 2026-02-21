<?php

namespace App\Console\Commands;

use App\Core\Services\TranslationKeyScanner;
use Illuminate\Console\Command;

class ScanTranslationKeysCommand extends Command
{
    protected $signature = 'translations:scan
                            {--dry-run : List keys that would be added without writing to the database}';

    protected $description = 'Scan app and resources for __(), trans(), @lang() and add missing keys to language_lines.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $scanner = app(TranslationKeyScanner::class);
        $result = $scanner->scan(! $dryRun);

        if ($dryRun) {
            $count = count($result['to_add_keys']);
            $this->info('Dry run: '.$count.' key(s) would be added.');
            foreach ($result['to_add_keys'] as $key) {
                $this->line('  '.$key);
            }

            return self::SUCCESS;
        }

        $this->info('Found '.$result['found'].' unique key(s), added '.$result['added'].' new.');

        return self::SUCCESS;
    }
}
