<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        $defaultLocale = $locales[0] ?? 'en';

        Setting::updateOrCreate(
            ['key' => 'site'],
            [
                'company_name' => [$defaultLocale => config('app.name')],
                'tagline' => [$defaultLocale => 'Your tagline here'],
                'address' => [$defaultLocale => ''],
                'email' => config('mail.from.address', 'contact@example.com'),
                'phone' => null,
                'social_links' => [],
            ]
        );
    }
}
