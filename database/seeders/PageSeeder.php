<?php

namespace Database\Seeders;

use App\Models\Page;
use Database\Factories\PageFactory;
use Illuminate\Database\Seeder;
use Throwable;

class PageSeeder extends Seeder
{
    /**
     * Sample gallery image URLs (Unsplash) for about-us page.
     *
     * @var array<int, string>
     */
    protected array $aboutGalleryUrls = [
        'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
        'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=1200&q=80',
    ];

    /**
     * Default pages with per-locale translations.
     * Keys must exist in config('laravellocalization.supportedLocales').
     * Fallback to 'en' when a locale is missing.
     *
     * @var array<int, array{slug: string, type: string, show_in_navigation?: bool, show_in_footer?: bool, order?: int, translations: array<string, array{title: string, body: string, meta_title: string, meta_description: string}>}>
     */
    protected array $defaultPages = [
        [
            'slug' => 'privacy-policy',
            'type' => 'privacy',
            'show_in_navigation' => false,
            'show_in_footer' => true,
            'order' => 1,
            'translations' => [
                'en' => ['title' => 'Privacy Policy', 'meta_title' => 'Privacy Policy', 'meta_description' => 'Our privacy policy.'],
                'de' => ['title' => 'Datenschutzrichtlinie', 'meta_title' => 'Datenschutzrichtlinie', 'meta_description' => 'Unsere Datenschutzrichtlinie.'],
                'es' => ['title' => 'Política de privacidad', 'meta_title' => 'Política de privacidad', 'meta_description' => 'Nuestra política de privacidad.'],
                'fr' => ['title' => 'Politique de confidentialité', 'meta_title' => 'Politique de confidentialité', 'meta_description' => 'Notre politique de confidentialité.'],
                'it' => ['title' => 'Informativa sulla privacy', 'meta_title' => 'Informativa sulla privacy', 'meta_description' => 'La nostra informativa sulla privacy.'],
                'ru' => ['title' => 'Политика конфиденциальности', 'meta_title' => 'Политика конфиденциальности', 'meta_description' => 'Наша политика конфиденциальности.'],
                'ar' => ['title' => 'سياسة الخصوصية', 'meta_title' => 'سياسة الخصوصية', 'meta_description' => 'سياسة الخصوصية الخاصة بنا.'],
                'fa' => ['title' => 'سیاست حفظ حریم خصوصی', 'meta_title' => 'سیاست حفظ حریم خصوصی', 'meta_description' => 'سیاست حفظ حریم خصوصی ما.'],
                'ja' => ['title' => 'プライバシーポリシー', 'meta_title' => 'プライバシーポリシー', 'meta_description' => '当社のプライバシーポリシー。'],
                'zh' => ['title' => '隐私政策', 'meta_title' => '隐私政策', 'meta_description' => '我们的隐私政策。'],
                'ko' => ['title' => '개인정보 처리방침', 'meta_title' => '개인정보 처리방침', 'meta_description' => '당사의 개인정보 처리방침.'],
            ],
        ],
        [
            'slug' => 'terms-of-use',
            'type' => 'terms',
            'show_in_navigation' => false,
            'show_in_footer' => true,
            'order' => 2,
            'translations' => [
                'en' => ['title' => 'Terms of Use', 'meta_title' => 'Terms of Use', 'meta_description' => 'Terms of use for this site.'],
                'de' => ['title' => 'Nutzungsbedingungen', 'meta_title' => 'Nutzungsbedingungen', 'meta_description' => 'Nutzungsbedingungen dieser Website.'],
                'es' => ['title' => 'Términos de uso', 'meta_title' => 'Términos de uso', 'meta_description' => 'Términos de uso de este sitio.'],
                'fr' => ['title' => 'Conditions d\'utilisation', 'meta_title' => 'Conditions d\'utilisation', 'meta_description' => 'Conditions d\'utilisation de ce site.'],
                'it' => ['title' => 'Termini di utilizzo', 'meta_title' => 'Termini di utilizzo', 'meta_description' => 'Termini di utilizzo di questo sito.'],
                'ru' => ['title' => 'Условия использования', 'meta_title' => 'Условия использования', 'meta_description' => 'Условия использования этого сайта.'],
                'ar' => ['title' => 'شروط الاستخدام', 'meta_title' => 'شروط الاستخدام', 'meta_description' => 'شروط استخدام هذا الموقع.'],
                'fa' => ['title' => 'شرایط استفاده', 'meta_title' => 'شرایط استفاده', 'meta_description' => 'شرایط استفاده از این سایت.'],
                'ja' => ['title' => '利用規約', 'meta_title' => '利用規約', 'meta_description' => '当サイトの利用規約。'],
                'zh' => ['title' => '使用条款', 'meta_title' => '使用条款', 'meta_description' => '本网站的使用条款。'],
                'ko' => ['title' => '이용약관', 'meta_title' => '이용약관', 'meta_description' => '본 사이트 이용약관.'],
            ],
        ],
        [
            'slug' => 'about-us',
            'type' => 'about',
            'show_in_navigation' => true,
            'show_in_footer' => true,
            'order' => 0,
            'translations' => [
                'en' => ['title' => 'About Us', 'meta_title' => 'About Us', 'meta_description' => 'Learn more about us.'],
                'de' => ['title' => 'Über uns', 'meta_title' => 'Über uns', 'meta_description' => 'Erfahren Sie mehr über uns.'],
                'es' => ['title' => 'Sobre nosotros', 'meta_title' => 'Sobre nosotros', 'meta_description' => 'Conozca más sobre nosotros.'],
                'fr' => ['title' => 'À propos de nous', 'meta_title' => 'À propos de nous', 'meta_description' => 'En savoir plus sur nous.'],
                'it' => ['title' => 'Chi siamo', 'meta_title' => 'Chi siamo', 'meta_description' => 'Scopri di più su di noi.'],
                'ru' => ['title' => 'О нас', 'meta_title' => 'О нас', 'meta_description' => 'Узнайте больше о нас.'],
                'ar' => ['title' => 'من نحن', 'meta_title' => 'من نحن', 'meta_description' => 'اعرف المزيد عنا.'],
                'fa' => ['title' => 'درباره ما', 'meta_title' => 'درباره ما', 'meta_description' => 'درباره ما بیشتر بدانید.'],
                'ja' => ['title' => '私たちについて', 'meta_title' => '私たちについて', 'meta_description' => '私たちについてもっと知る。'],
                'zh' => ['title' => '关于我们', 'meta_title' => '关于我们', 'meta_description' => '了解更多关于我们。'],
                'ko' => ['title' => '회사 소개', 'meta_title' => '회사 소개', 'meta_description' => '회사에 대해 자세히 알아보세요.'],
            ],
        ],
    ];

    /**
     * Long HTML body for privacy policy (WYSIWYG-style).
     * Each section uses separate block elements so prose spacing renders correctly.
     */
    protected function longPrivacyBody(): string
    {
        $intro = "<p>This Privacy Policy describes how we collect, use, and share your information when you use our services. Please read it carefully.</p>\n";
        $s1 = "<h2>Information we collect</h2>\n<p>We collect information you provide directly, such as when you create an account, contact us, or subscribe to our newsletter.</p>\n<p>This may include your name, email address, and any other details you choose to provide.</p>\n";
        $s2 = "<h2>How we use your information</h2>\n<p>We use the information we collect to operate and improve our services, to communicate with you, and to comply with legal obligations.</p>\n<p>We do not sell your personal information to third parties.</p>\n";
        $s3 = "<h2>Cookies and similar technologies</h2>\n<p>We use cookies and similar technologies to recognize you and to personalize your experience.</p>\n<p>You can control cookies through your browser settings.</p>\n";
        $s4 = "<h2>Data retention and security</h2>\n<p>We retain your information only for as long as necessary to fulfill the purposes described in this policy.</p>\n<p>We implement appropriate technical and organizational measures to protect your data.</p>\n";
        $s5 = "<h2>Your rights</h2>\n<p>Depending on your location, you may have the right to access, correct, or delete your personal data, or to object to or restrict certain processing.</p>\n<p>Contact us to exercise these rights.</p>\n";
        $outro = "<p>We may update this policy from time to time. We will notify you of any material changes by posting the new policy on this page and updating the effective date.</p>\n";

        return $intro.$s1.$s2.$s3.$s4.$s5.$outro.PageFactory::longWysiwygBody(5);
    }

    /**
     * Long HTML body for terms of use (WYSIWYG-style).
     * Each section uses separate block elements so prose spacing renders correctly.
     */
    protected function longTermsBody(): string
    {
        $intro = "<p>These Terms of Use govern your access to and use of our website and services. By using our services, you agree to these terms.</p>\n";
        $s1 = "<h2>Eligibility and acceptance</h2>\n<p>You must be at least 18 years of age and have the legal capacity to enter into these terms.</p>\n<p>By creating an account or using our services, you represent that you meet these requirements.</p>\n";
        $s2 = "<h2>Use of services</h2>\n<p>You agree to use our services only for lawful purposes and in accordance with these terms.</p>\n<p>You may not use our services to violate any laws, infringe on others' rights, or transmit harmful or offensive content.</p>\n";
        $s3 = "<h2>Intellectual property</h2>\n<p>All content and materials on our services are owned by us or our licensors.</p>\n<p>You may not copy, modify, or distribute our content without prior written permission.</p>\n";
        $s4 = "<h2>Disclaimer of warranties</h2>\n<p>Our services are provided \"as is\" without warranties of any kind.</p>\n<p>We do not guarantee that our services will be uninterrupted, error-free, or free of harmful components.</p>\n";
        $s5 = "<h2>Limitation of liability</h2>\n<p>To the fullest extent permitted by law, we shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of our services.</p>\n";
        $outro = "<p>We reserve the right to modify these terms at any time. Your continued use of our services after changes constitutes acceptance of the revised terms.</p>\n";

        return $intro.$s1.$s2.$s3.$s4.$s5.$outro.PageFactory::longWysiwygBody(5);
    }

    /**
     * Long HTML body for about us (WYSIWYG-style).
     * Each section uses separate block elements so prose spacing renders correctly.
     */
    protected function longAboutBody(): string
    {
        $intro = "<p>We are a team dedicated to building products that help our customers succeed.</p>\n<p>Our mission is to deliver reliable, user-friendly solutions that make a difference.</p>\n";
        $s1 = "<h2>Our story</h2>\n<p>Founded with a focus on quality and customer satisfaction, we have grown into a trusted partner for organizations of all sizes.</p>\n<p>Our journey has been shaped by the feedback and trust of our users.</p>\n";
        $s2 = "<h2>Our values</h2>\n<p>We believe in transparency, innovation, and putting our customers first.</p>\n<p>Every decision we make is guided by these core values and our commitment to excellence.</p>\n";
        $s3 = "<h2>Our team</h2>\n<p>Our team brings together diverse skills and backgrounds.</p>\n<p>We are developers, designers, and support specialists who share a common goal: to build something that matters.</p>\n";
        $outro = "<p>Thank you for choosing us. We look forward to supporting you and growing together.</p>\n";

        return $intro.$s1.$s2.$s3.$outro.PageFactory::longWysiwygBody(6);
    }

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
                [
                    'type' => $page['type'],
                    'show_in_navigation' => $page['show_in_navigation'] ?? false,
                    'show_in_footer' => $page['show_in_footer'] ?? true,
                    'order' => $page['order'] ?? 0,
                ]
            );

            $translations = $page['translations'];
            $longBody = match ($page['slug']) {
                'privacy-policy' => $this->longPrivacyBody(),
                'terms-of-use' => $this->longTermsBody(),
                'about-us' => $this->longAboutBody(),
                default => PageFactory::longWysiwygBody(6),
            };

            foreach ($supportedLocales as $locale) {
                $t = $translations[$locale] ?? $translations[$fallbackLocale] ?? $translations['en'] ?? reset($translations);
                $model->setTranslation('title', $locale, $t['title']);
                $model->setTranslation('body', $locale, $longBody);
                $model->setTranslation('meta_title', $locale, $t['meta_title']);
                $model->setTranslation('meta_description', $locale, $t['meta_description']);
            }

            $model->save();

            if ($page['slug'] === 'about-us') {
                try {
                    $model->clearMediaCollection('gallery');
                    foreach ($this->aboutGalleryUrls as $url) {
                        $model->addMediaFromUrl($url)->toMediaCollection('gallery');
                    }
                } catch (Throwable $e) {
                    $this->command?->warn('Could not attach about-us gallery images: '.$e->getMessage());
                }
            }
        }
    }
}
