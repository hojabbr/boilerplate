<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Default pages with per-locale translations.
     * Keys must exist in config('laravellocalization.supportedLocales').
     * Fallback to 'en' when a locale is missing.
     *
     * @var array<int, array{slug: string, type: string, translations: array<string, array{title: string, body: string, meta_title: string, meta_description: string}>}>
     */
    protected array $defaultPages = [
        [
            'slug' => 'privacy-policy',
            'type' => 'privacy',
            'translations' => [
                'en' => ['title' => 'Privacy Policy', 'body' => '<p>Content for this page. Edit in the admin panel.</p>', 'meta_title' => 'Privacy Policy', 'meta_description' => 'Our privacy policy.'],
                'de' => ['title' => 'Datenschutzrichtlinie', 'body' => '<p>Inhalt dieser Seite. Im Admin-Bereich bearbeiten.</p>', 'meta_title' => 'Datenschutzrichtlinie', 'meta_description' => 'Unsere Datenschutzrichtlinie.'],
                'es' => ['title' => 'Política de privacidad', 'body' => '<p>Contenido de esta página. Editar en el panel de administración.</p>', 'meta_title' => 'Política de privacidad', 'meta_description' => 'Nuestra política de privacidad.'],
                'fr' => ['title' => 'Politique de confidentialité', 'body' => '<p>Contenu de cette page. Modifier dans le panneau d\'administration.</p>', 'meta_title' => 'Politique de confidentialité', 'meta_description' => 'Notre politique de confidentialité.'],
                'it' => ['title' => 'Informativa sulla privacy', 'body' => '<p>Contenuto di questa pagina. Modifica nel pannello di amministrazione.</p>', 'meta_title' => 'Informativa sulla privacy', 'meta_description' => 'La nostra informativa sulla privacy.'],
                'ru' => ['title' => 'Политика конфиденциальности', 'body' => '<p>Содержание этой страницы. Редактировать в панели администратора.</p>', 'meta_title' => 'Политика конфиденциальности', 'meta_description' => 'Наша политика конфиденциальности.'],
                'ar' => ['title' => 'سياسة الخصوصية', 'body' => '<p>محتوى هذه الصفحة. التحرير في لوحة الإدارة.</p>', 'meta_title' => 'سياسة الخصوصية', 'meta_description' => 'سياسة الخصوصية الخاصة بنا.'],
                'fa' => ['title' => 'سیاست حفظ حریم خصوصی', 'body' => '<p>محتوای این صفحه. در پنل مدیریت ویرایش کنید.</p>', 'meta_title' => 'سیاست حفظ حریم خصوصی', 'meta_description' => 'سیاست حفظ حریم خصوصی ما.'],
                'ja' => ['title' => 'プライバシーポリシー', 'body' => '<p>このページの内容。管理パネルで編集してください。</p>', 'meta_title' => 'プライバシーポリシー', 'meta_description' => '当社のプライバシーポリシー。'],
                'zh' => ['title' => '隐私政策', 'body' => '<p>本页内容。请在管理面板中编辑。</p>', 'meta_title' => '隐私政策', 'meta_description' => '我们的隐私政策。'],
                'ko' => ['title' => '개인정보 처리방침', 'body' => '<p>이 페이지의 내용. 관리자 패널에서 편집하세요.</p>', 'meta_title' => '개인정보 처리방침', 'meta_description' => '당사의 개인정보 처리방침.'],
            ],
        ],
        [
            'slug' => 'terms-of-use',
            'type' => 'terms',
            'translations' => [
                'en' => ['title' => 'Terms of Use', 'body' => '<p>Content for this page. Edit in the admin panel.</p>', 'meta_title' => 'Terms of Use', 'meta_description' => 'Terms of use for this site.'],
                'de' => ['title' => 'Nutzungsbedingungen', 'body' => '<p>Inhalt dieser Seite. Im Admin-Bereich bearbeiten.</p>', 'meta_title' => 'Nutzungsbedingungen', 'meta_description' => 'Nutzungsbedingungen dieser Website.'],
                'es' => ['title' => 'Términos de uso', 'body' => '<p>Contenido de esta página. Editar en el panel de administración.</p>', 'meta_title' => 'Términos de uso', 'meta_description' => 'Términos de uso de este sitio.'],
                'fr' => ['title' => 'Conditions d\'utilisation', 'body' => '<p>Contenu de cette page. Modifier dans le panneau d\'administration.</p>', 'meta_title' => 'Conditions d\'utilisation', 'meta_description' => 'Conditions d\'utilisation de ce site.'],
                'it' => ['title' => 'Termini di utilizzo', 'body' => '<p>Contenuto di questa pagina. Modifica nel pannello di amministrazione.</p>', 'meta_title' => 'Termini di utilizzo', 'meta_description' => 'Termini di utilizzo di questo sito.'],
                'ru' => ['title' => 'Условия использования', 'body' => '<p>Содержание этой страницы. Редактировать в панели администратора.</p>', 'meta_title' => 'Условия использования', 'meta_description' => 'Условия использования этого сайта.'],
                'ar' => ['title' => 'شروط الاستخدام', 'body' => '<p>محتوى هذه الصفحة. التحرير في لوحة الإدارة.</p>', 'meta_title' => 'شروط الاستخدام', 'meta_description' => 'شروط استخدام هذا الموقع.'],
                'fa' => ['title' => 'شرایط استفاده', 'body' => '<p>محتوای این صفحه. در پنل مدیریت ویرایش کنید.</p>', 'meta_title' => 'شرایط استفاده', 'meta_description' => 'شرایط استفاده از این سایت.'],
                'ja' => ['title' => '利用規約', 'body' => '<p>このページの内容。管理パネルで編集してください。</p>', 'meta_title' => '利用規約', 'meta_description' => '当サイトの利用規約。'],
                'zh' => ['title' => '使用条款', 'body' => '<p>本页内容。请在管理面板中编辑。</p>', 'meta_title' => '使用条款', 'meta_description' => '本网站的使用条款。'],
                'ko' => ['title' => '이용약관', 'body' => '<p>이 페이지의 내용. 관리자 패널에서 편집하세요.</p>', 'meta_title' => '이용약관', 'meta_description' => '본 사이트 이용약관.'],
            ],
        ],
        [
            'slug' => 'about-us',
            'type' => 'about',
            'translations' => [
                'en' => ['title' => 'About Us', 'body' => '<p>Content for this page. Edit in the admin panel.</p>', 'meta_title' => 'About Us', 'meta_description' => 'Learn more about us.'],
                'de' => ['title' => 'Über uns', 'body' => '<p>Inhalt dieser Seite. Im Admin-Bereich bearbeiten.</p>', 'meta_title' => 'Über uns', 'meta_description' => 'Erfahren Sie mehr über uns.'],
                'es' => ['title' => 'Sobre nosotros', 'body' => '<p>Contenido de esta página. Editar en el panel de administración.</p>', 'meta_title' => 'Sobre nosotros', 'meta_description' => 'Conozca más sobre nosotros.'],
                'fr' => ['title' => 'À propos de nous', 'body' => '<p>Contenu de cette page. Modifier dans le panneau d\'administration.</p>', 'meta_title' => 'À propos de nous', 'meta_description' => 'En savoir plus sur nous.'],
                'it' => ['title' => 'Chi siamo', 'body' => '<p>Contenuto di questa pagina. Modifica nel pannello di amministrazione.</p>', 'meta_title' => 'Chi siamo', 'meta_description' => 'Scopri di più su di noi.'],
                'ru' => ['title' => 'О нас', 'body' => '<p>Содержание этой страницы. Редактировать в панели администратора.</p>', 'meta_title' => 'О нас', 'meta_description' => 'Узнайте больше о нас.'],
                'ar' => ['title' => 'من نحن', 'body' => '<p>محتوى هذه الصفحة. التحرير في لوحة الإدارة.</p>', 'meta_title' => 'من نحن', 'meta_description' => 'اعرف المزيد عنا.'],
                'fa' => ['title' => 'درباره ما', 'body' => '<p>محتوای این صفحه. در پنل مدیریت ویرایش کنید.</p>', 'meta_title' => 'درباره ما', 'meta_description' => 'درباره ما بیشتر بدانید.'],
                'ja' => ['title' => '私たちについて', 'body' => '<p>このページの内容。管理パネルで編集してください。</p>', 'meta_title' => '私たちについて', 'meta_description' => '私たちについてもっと知る。'],
                'zh' => ['title' => '关于我们', 'body' => '<p>本页内容。请在管理面板中编辑。</p>', 'meta_title' => '关于我们', 'meta_description' => '了解更多关于我们。'],
                'ko' => ['title' => '회사 소개', 'body' => '<p>이 페이지의 내용. 관리자 패널에서 편집하세요.</p>', 'meta_title' => '회사 소개', 'meta_description' => '회사에 대해 자세히 알아보세요.'],
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supportedLocales = array_keys(config('laravellocalization.supportedLocales', []));
        $fallbackLocale = config('app.fallback_locale', 'en');

        foreach ($this->defaultPages as $page) {
            $model = Page::updateOrCreate(
                ['slug' => $page['slug']],
                ['type' => $page['type']]
            );

            $translations = $page['translations'];

            foreach ($supportedLocales as $locale) {
                $t = $translations[$locale] ?? $translations[$fallbackLocale] ?? $translations['en'] ?? reset($translations);
                $model->setTranslation('title', $locale, $t['title']);
                $model->setTranslation('body', $locale, $t['body']);
                $model->setTranslation('meta_title', $locale, $t['meta_title']);
                $model->setTranslation('meta_description', $locale, $t['meta_description']);
            }

            $model->save();
        }
    }
}
