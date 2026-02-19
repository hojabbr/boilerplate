<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supportedLocales = config('laravellocalization.supportedLocales', []);
        $defaultCode = config('app.locale', 'en');
        $sortOrder = 0;

        foreach ($supportedLocales as $code => $config) {
            Language::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $config['native'] ?? $config['name'] ?? $code,
                    'script' => $config['script'] ?? null,
                    'regional' => $config['regional'] ?? null,
                    'is_default' => $code === $defaultCode,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }
}
