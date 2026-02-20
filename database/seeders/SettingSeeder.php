<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Tagline/description defaults per locale when not using app.description.
     * company_name is always seeded from config('app.name') so it can be overridden in Filament.
     *
     * @var array<string, array{tagline: string, address: string}>
     */
    protected array $translations = [
        'en' => [
            'tagline' => 'Build, ship, and scale with confidence.',
            'address' => '123 Business Street, Suite 100',
        ],
        'de' => [
            'tagline' => 'Entwickeln, ausliefern und skalieren mit Vertrauen.',
            'address' => 'Geschäftsstraße 123, Suite 100',
        ],
        'es' => [
            'tagline' => 'Construye, lanza y escala con confianza.',
            'address' => 'Calle Empresa 123, Suite 100',
        ],
        'fr' => [
            'tagline' => 'Construisez, déployez et scalez en toute confiance.',
            'address' => '123 rue des Affaires, Suite 100',
        ],
        'it' => [
            'tagline' => 'Costruisci, rilascia e scala con sicurezza.',
            'address' => 'Via Business 123, Suite 100',
        ],
        'ru' => [
            'tagline' => 'Создавайте, запускайте и масштабируйте с уверенностью.',
            'address' => 'ул. Бизнес 123, офис 100',
        ],
        'ar' => [
            'tagline' => 'ابنِ واصنع ووسّع بثقة.',
            'address' => 'شارع الأعمال 123، جناح 100',
        ],
        'fa' => [
            'tagline' => 'با اطمینان بسازید، عرضه کنید و مقیاس بدهید.',
            'address' => 'خیابان کسب‌وکار ۱۲۳، سوئیت ۱۰۰',
        ],
        'ja' => [
            'tagline' => '自信を持って構築、リリース、スケール。',
            'address' => 'ビジネス通り123、スイート100',
        ],
        'zh' => [
            'tagline' => '自信地构建、发布与扩展。',
            'address' => '商业街123号，100室',
        ],
        'ko' => [
            'tagline' => '자신 있게 구축하고, 출시하고, 확장하세요.',
            'address' => '비즈니스거리 123, 스위트 100',
        ],
    ];

    /**
     * Run the database seeds.
     * Seeds from config(app.name) and config(app.description); Settings override these at runtime.
     */
    public function run(): void
    {
        $supportedLocales = array_keys(config('laravellocalization.supportedLocales', []));
        $fallback = config('app.fallback_locale', 'en');
        $appName = config('app.name');
        $appDescription = config('app.description', 'Build something great.');

        $setting = Setting::updateOrCreate(
            ['key' => 'site'],
            [
                'email' => config('mail.from.address', 'hello@example.com'),
                'phone' => null,
                'social_links' => [
                    'twitter' => 'https://twitter.com',
                    'linkedin' => 'https://linkedin.com',
                    'github' => 'https://github.com',
                ],
            ]
        );

        foreach ($supportedLocales as $locale) {
            $setting->setTranslation('company_name', $locale, $appName);
            $t = $this->translations[$locale] ?? $this->translations[$fallback] ?? $this->translations['en'];
            $setting->setTranslation('tagline', $locale, $locale === $fallback ? $appDescription : $t['tagline']);
            $setting->setTranslation('address', $locale, $t['address']);
        }

        $setting->save();
    }
}
