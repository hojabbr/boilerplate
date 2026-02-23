<?php

namespace Database\Seeders;

use App\Core\Models\Language;
use Illuminate\Database\Seeder;

/**
 * Seeds the languages table. Single source for "default locales" when config is minimal.
 * At runtime, supported locales come from the DB via SupportedLocalesService (cached, injected into config).
 */
class LanguageSeeder extends Seeder
{
    private const RTL_SCRIPTS = ['Arab', 'Hebr', 'Mong', 'Tfng', 'Thaa'];

    /**
     * Default locales for initial seed when config has none or only fallback.
     * Add/remove here to change which languages exist after a fresh db:seed.
     *
     * @return array<string, array{name: string, script: string, native: string, regional: string}>
     */
    public static function defaultLocales(): array
    {
        return [
            'en' => ['name' => 'English', 'script' => 'Latn', 'native' => 'English', 'regional' => 'en_GB'],
            'de' => ['name' => 'German', 'script' => 'Latn', 'native' => 'Deutsch', 'regional' => 'de_DE'],
            'es' => ['name' => 'Spanish', 'script' => 'Latn', 'native' => 'español', 'regional' => 'es_ES'],
            'fr' => ['name' => 'French', 'script' => 'Latn', 'native' => 'français', 'regional' => 'fr_FR'],
            'it' => ['name' => 'Italian', 'script' => 'Latn', 'native' => 'italiano', 'regional' => 'it_IT'],
            'ru' => ['name' => 'Russian', 'script' => 'Latn', 'native' => 'русский', 'regional' => 'ru_RU'],
            'ro' => ['name' => 'Romanian', 'script' => 'Latn', 'native' => 'română', 'regional' => 'ro_RO'],
            'tr' => ['name' => 'Turkish', 'script' => 'Latn', 'native' => 'Türkçe', 'regional' => 'tr_TR'],
            'ur' => ['name' => 'Urdu', 'script' => 'Arab', 'native' => 'اردو', 'regional' => 'ur_PK'],
            'ar' => ['name' => 'Arabic', 'script' => 'Arab', 'native' => 'العربية', 'regional' => 'ar_SA'],
            'fa' => ['name' => 'Persian', 'script' => 'Arab', 'native' => 'فارسی', 'regional' => 'fa_IR'],
            'ja' => ['name' => 'Japanese', 'script' => 'Jpan', 'native' => '日本語', 'regional' => 'ja_JP'],
            'zh' => ['name' => 'Chinese', 'script' => 'Hans', 'native' => '中文', 'regional' => 'zh_CN'],
            'ko' => ['name' => 'Korean', 'script' => 'Kore', 'native' => '한국어', 'regional' => 'ko_KR'],
        ];
    }

    public function run(): void
    {
        $fromConfig = config('laravellocalization.supportedLocales', []);
        $supportedLocales = (count($fromConfig) <= 1) ? self::defaultLocales() : $fromConfig;
        $defaultCode = config('app.locale', 'en');
        $sortOrder = 0;

        foreach ($supportedLocales as $code => $config) {
            $script = $config['script'] ?? null;
            Language::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $config['native'] ?? $config['name'] ?? $code,
                    'script' => $script,
                    'regional' => $config['regional'] ?? null,
                    'direction' => $this->directionFromScript($script),
                    'is_default' => $code === $defaultCode,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }

    private function directionFromScript(?string $script): string
    {
        if ($script === null) {
            return 'ltr';
        }

        return in_array($script, self::RTL_SCRIPTS, true) ? 'rtl' : 'ltr';
    }
}
