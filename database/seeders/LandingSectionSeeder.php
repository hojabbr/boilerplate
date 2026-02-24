<?php

namespace Database\Seeders;

use App\Domains\Landing\Models\LandingSection;
use App\Domains\Landing\Models\LandingSectionItem;
use Illuminate\Database\Seeder;
use Throwable;

class LandingSectionSeeder extends Seeder
{
    /**
     * Sample image URLs (Unsplash) for landing sections and items.
     *
     * @var array<string, string>
     */
    protected array $sampleUrls = [
        'hero' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&q=80',
        'cta' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&q=80',
        'feature_1' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&q=80',
        'feature_2' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80',
        'feature_3' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=800&q=80',
    ];

    /**
     * Supported locales; fallback to en when missing.
     */
    protected function locales(): array
    {
        return array_keys(config('laravellocalization.supportedLocales', ['en' => []]));
    }

    protected function fallbackLocale(): string
    {
        return config('app.fallback_locale', 'en');
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = $this->locales();
        $fallback = $this->fallbackLocale();

        $this->seedHero($locales, $fallback);
        $this->seedFeatures($locales, $fallback);
        $this->seedLatestPosts($locales, $fallback);
        $this->seedCta($locales, $fallback);
    }

    /**
     * @param  list<string>  $locales
     */
    protected function seedLatestPosts(array $locales, string $fallback): void
    {
        $translations = [
            'en' => ['title' => 'Latest from the blog', 'subtitle' => 'Recent articles and updates.'],
            'de' => ['title' => 'Neuestes aus dem Blog', 'subtitle' => 'Aktuelle Artikel und Updates.'],
            'es' => ['title' => 'Lo último del blog', 'subtitle' => 'Artículos y novedades recientes.'],
            'fr' => ['title' => 'Derniers articles du blog', 'subtitle' => 'Articles et actualités récents.'],
            'it' => ['title' => 'Ultimi dal blog', 'subtitle' => 'Articoli e aggiornamenti recenti.'],
            'ru' => ['title' => 'Последнее из блога', 'subtitle' => 'Недавние статьи и обновления.'],
            'ro' => ['title' => 'Cele mai recente de pe blog', 'subtitle' => 'Articole și actualizări recente.'],
            'tr' => ['title' => 'Blogdan son yazılar', 'subtitle' => 'Son makaleler ve güncellemeler.'],
            'ur' => ['title' => 'بلاگ سے تازہ ترین', 'subtitle' => 'حالیہ مضامین اور اپڈیٹس۔'],
            'ar' => ['title' => 'آخر من المدونة', 'subtitle' => 'المقالات والتحديثات الأخيرة.'],
            'fa' => ['title' => 'آخرین مطالب وبلاگ', 'subtitle' => 'مقالات و به‌روزرسانی‌های اخیر.'],
            'ja' => ['title' => 'ブログの最新記事', 'subtitle' => '最近の記事とお知らせ。'],
            'zh' => ['title' => '博客最新', 'subtitle' => '近期文章与动态。'],
            'ko' => ['title' => '블로그 최신 글', 'subtitle' => '최근 글과 소식.'],
            'hi' => ['title' => 'ब्लॉग से नवीनतम', 'subtitle' => 'हाल के लेख और अपडेट।'],
        ];

        $section = LandingSection::updateOrCreate(
            ['type' => 'latest_posts'],
            ['sort_order' => 3]
        );

        foreach ($locales as $locale) {
            $t = $translations[$locale] ?? $translations[$fallback] ?? $translations['en'];
            $section->setTranslation('title', $locale, $t['title']);
            $section->setTranslation('subtitle', $locale, $t['subtitle']);
        }
        $section->save();
    }

    /**
     * @param  list<string>  $locales
     */
    protected function seedHero(array $locales, string $fallback): void
    {
        $appName = config('app.name');
        $appDescription = config('app.description', 'Build something great.');
        $translations = [
            'en' => ['title' => "Welcome to {$appName}", 'subtitle' => $appDescription, 'cta_text' => 'Get started', 'cta_url' => '/register'],
            'de' => ['title' => "Willkommen bei {$appName}", 'subtitle' => 'Entwickeln, ausliefern und skalieren mit Vertrauen.', 'cta_text' => 'Loslegen', 'cta_url' => '/register'],
            'es' => ['title' => "Bienvenido a {$appName}", 'subtitle' => 'Construye, lanza y escala con confianza.', 'cta_text' => 'Empezar', 'cta_url' => '/register'],
            'fr' => ['title' => "Bienvenue chez {$appName}", 'subtitle' => 'Construisez, déployez et scalez en toute confiance.', 'cta_text' => 'Commencer', 'cta_url' => '/register'],
            'it' => ['title' => "Benvenuto in {$appName}", 'subtitle' => 'Costruisci, rilascia e scala con sicurezza.', 'cta_text' => 'Inizia', 'cta_url' => '/register'],
            'ru' => ['title' => "Добро пожаловать в {$appName}", 'subtitle' => 'Создавайте, запускайте и масштабируйте с уверенностью.', 'cta_text' => 'Начать', 'cta_url' => '/register'],
            'ro' => ['title' => "Bine ați venit la {$appName}", 'subtitle' => 'Construiți, lansați și scalați cu încredere.', 'cta_text' => 'Începeți', 'cta_url' => '/register'],
            'tr' => ['title' => "{$appName}'a hoş geldiniz", 'subtitle' => 'Güvenle oluşturun, yayınlayın ve ölçeklendirin.', 'cta_text' => 'Başlayın', 'cta_url' => '/register'],
            'ur' => ['title' => "{$appName} میں خوش آمدید", 'subtitle' => 'اعتماد کے ساتھ بنائیں، لانچ کریں اور پھیلائیں۔', 'cta_text' => 'شروع کریں', 'cta_url' => '/register'],
            'ar' => ['title' => "مرحباً بك في {$appName}", 'subtitle' => 'ابنِ واصنع ووسّع بثقة.', 'cta_text' => 'ابدأ', 'cta_url' => '/register'],
            'fa' => ['title' => "به {$appName} خوش آمدید", 'subtitle' => 'با اطمینان بسازید، عرضه کنید و مقیاس بدهید.', 'cta_text' => 'شروع کنید', 'cta_url' => '/register'],
            'ja' => ['title' => "{$appName}へようこそ", 'subtitle' => '自信を持って構築、リリース、スケール。', 'cta_text' => '始める', 'cta_url' => '/register'],
            'zh' => ['title' => "欢迎使用 {$appName}", 'subtitle' => '自信地构建、发布与扩展。', 'cta_text' => '开始', 'cta_url' => '/register'],
            'ko' => ['title' => "{$appName}에 오신 것을 환영합니다", 'subtitle' => '자신 있게 구축하고, 출시하고, 확장하세요.', 'cta_text' => '시작하기', 'cta_url' => '/register'],
            'hi' => ['title' => "{$appName} में आपका स्वागत है", 'subtitle' => 'विश्वास के साथ निर्माण करें, लॉन्च करें और बढ़ाएं।', 'cta_text' => 'शुरू करें', 'cta_url' => '/register'],
        ];

        $section = LandingSection::updateOrCreate(
            ['type' => 'hero'],
            ['sort_order' => 0]
        );

        foreach ($locales as $locale) {
            $t = $translations[$locale] ?? $translations[$fallback] ?? $translations['en'];
            $section->setTranslation('title', $locale, $t['title']);
            $section->setTranslation('subtitle', $locale, $t['subtitle']);
            $section->setTranslation('cta_text', $locale, $t['cta_text']);
            $section->setTranslation('cta_url', $locale, $t['cta_url']);
        }
        $section->save();

        try {
            $section->clearMediaCollection('image');
            $section->addMediaFromUrl($this->sampleUrls['hero'])->toMediaCollection('image');
        } catch (Throwable $e) {
            $this->command?->warn('Could not attach hero sample image: '.$e->getMessage());
        }
    }

    /**
     * @param  list<string>  $locales
     */
    protected function seedFeatures(array $locales, string $fallback): void
    {
        $sectionTranslations = [
            'en' => ['title' => 'Why choose us', 'subtitle' => 'Everything you need to grow your business.'],
            'de' => ['title' => 'Warum uns wählen', 'subtitle' => 'Alles, was Sie brauchen, um Ihr Geschäft auszubauen.'],
            'es' => ['title' => 'Por qué elegirnos', 'subtitle' => 'Todo lo que necesitas para hacer crecer tu negocio.'],
            'fr' => ['title' => 'Pourquoi nous choisir', 'subtitle' => 'Tout ce dont vous avez besoin pour développer votre entreprise.'],
            'it' => ['title' => 'Perché sceglierci', 'subtitle' => 'Tutto ciò che serve per far crescere il tuo business.'],
            'ru' => ['title' => 'Почему мы', 'subtitle' => 'Всё необходимое для роста вашего бизнеса.'],
            'ro' => ['title' => 'De ce să ne alegeți', 'subtitle' => 'Tot ce aveți nevoie pentru a vă dezvolta afacerea.'],
            'tr' => ['title' => 'Neden bizi seçmelisiniz', 'subtitle' => 'İşinizi büyütmek için ihtiyacınız olan her şey.'],
            'ur' => ['title' => 'ہمیں کیوں چنیں', 'subtitle' => 'اپنے کاروبار کو بڑھانے کے لیے جو کچھ درکار ہے۔'],
            'ar' => ['title' => 'لماذا تختارنا', 'subtitle' => 'كل ما تحتاجه لتنمية عملك.'],
            'fa' => ['title' => 'چرا ما را انتخاب کنید', 'subtitle' => 'همه آنچه برای رشد کسب‌وکارتان نیاز دارید.'],
            'ja' => ['title' => '選ばれる理由', 'subtitle' => 'ビジネス成長に必要なすべて。'],
            'zh' => ['title' => '为什么选择我们', 'subtitle' => '助力业务增长所需的一切。'],
            'ko' => ['title' => '왜 우리를 선택해야 할까요', 'subtitle' => '비즈니스 성장에 필요한 모든 것.'],
            'hi' => ['title' => 'हमें क्यों चुनें', 'subtitle' => 'अपने व्यवसाय को बढ़ाने के लिए जो कुछ चाहिए।'],
        ];

        $section = LandingSection::updateOrCreate(
            ['type' => 'features'],
            ['sort_order' => 1]
        );

        foreach ($locales as $locale) {
            $t = $sectionTranslations[$locale] ?? $sectionTranslations[$fallback] ?? $sectionTranslations['en'];
            $section->setTranslation('title', $locale, $t['title']);
            $section->setTranslation('subtitle', $locale, $t['subtitle']);
        }
        $section->save();

        $itemsData = [
            [
                'sort_order' => 0,
                'translations' => [
                    'en' => ['title' => 'Secure & reliable', 'description' => 'Enterprise-grade security and 99.9% uptime so you can focus on what matters.'],
                    'de' => ['title' => 'Sicher & zuverlässig', 'description' => 'Unternehmenssicherheit und 99,9 % Verfügbarkeit.'],
                    'es' => ['title' => 'Seguro y fiable', 'description' => 'Seguridad de nivel empresarial y 99,9 % de tiempo de actividad.'],
                    'fr' => ['title' => 'Sécurisé et fiable', 'description' => 'Sécurité niveau entreprise et 99,9 % de disponibilité.'],
                    'it' => ['title' => 'Sicuro e affidabile', 'description' => 'Sicurezza enterprise e 99,9% di uptime.'],
                    'ru' => ['title' => 'Безопасно и надёжно', 'description' => 'Корпоративная безопасность и 99,9 % доступности.'],
                    'ro' => ['title' => 'Sigur și fiabil', 'description' => 'Securitate la nivel enterprise și 99,9% disponibilitate.'],
                    'tr' => ['title' => 'Güvenli ve güvenilir', 'description' => 'Kurumsal güvenlik ve %99,9 çalışma süresi.'],
                    'ur' => ['title' => 'محفوظ اور قابل اعتماد', 'description' => 'انٹرپرائز گریڈ سیکیورٹی اور 99.9% اپٹائم۔'],
                    'ar' => ['title' => 'آمن وموثوق', 'description' => 'أمان على مستوى المؤسسات ووقت تشغيل 99.9٪.'],
                    'fa' => ['title' => 'امن و قابل اعتماد', 'description' => 'امنیت سطح سازمانی و ۹۹.۹٪ آپتایم.'],
                    'ja' => ['title' => 'セキュアで信頼性の高い', 'description' => 'エンタープライズ級のセキュリティと99.9%の稼働率。'],
                    'zh' => ['title' => '安全可靠', 'description' => '企业级安全与 99.9% 可用性。'],
                    'ko' => ['title' => '안전하고 신뢰할 수 있는', 'description' => '엔터프라이즈급 보안과 99.9% 가동 시간.'],
                    'hi' => ['title' => 'सुरक्षित और विश्वसनीय', 'description' => 'एंटरप्राइज़-ग्रेड सुरक्षा और 99.9% अपटाइम।'],
                ],
            ],
            [
                'sort_order' => 1,
                'translations' => [
                    'en' => ['title' => 'Easy to use', 'description' => 'Intuitive interface and clear documentation. Get started in minutes, not days.'],
                    'de' => ['title' => 'Einfach zu bedienen', 'description' => 'Intuitive Oberfläche und klare Dokumentation. In Minuten starten.'],
                    'es' => ['title' => 'Fácil de usar', 'description' => 'Interfaz intuitiva y documentación clara. Empieza en minutos.'],
                    'fr' => ['title' => 'Facile à utiliser', 'description' => 'Interface intuitive et documentation claire. Démarrez en quelques minutes.'],
                    'it' => ['title' => 'Facile da usare', 'description' => 'Interfaccia intuitiva e documentazione chiara. Inizia in pochi minuti.'],
                    'ru' => ['title' => 'Просто в использовании', 'description' => 'Интуитивный интерфейс и понятная документация.'],
                    'ro' => ['title' => 'Ușor de utilizat', 'description' => 'Interfață intuitivă și documentație clară. Începeți în câteva minute.'],
                    'tr' => ['title' => 'Kullanımı kolay', 'description' => 'Sezgisel arayüz ve net dokümantasyon. Dakikalar içinde başlayın.'],
                    'ur' => ['title' => 'استعمال میں آسان', 'description' => 'بدیہی انٹرفیس اور واضح دستاویزات۔ منٹوں میں شروع کریں۔'],
                    'ar' => ['title' => 'سهل الاستخدام', 'description' => 'واجهة بديهية ووثائق واضحة. ابدأ خلال دقائق.'],
                    'fa' => ['title' => 'کاربری آسان', 'description' => 'رابط کاربری شهودی و مستندات روشن. در چند دقیقه شروع کنید.'],
                    'ja' => ['title' => '使いやすい', 'description' => '直感的なインターフェースと分かりやすいドキュメント。数分で開始。'],
                    'zh' => ['title' => '易于使用', 'description' => '直观界面与清晰文档，几分钟即可上手。'],
                    'ko' => ['title' => '사용하기 쉬운', 'description' => '직관적인 인터페이스와 명확한 문서. 몇 분 만에 시작하세요.'],
                    'hi' => ['title' => 'उपयोग में आसान', 'description' => 'सहज इंटरफ़ेस और स्पष्ट दस्तावेज़ीकरण। मिनटों में शुरू करें।'],
                ],
            ],
            [
                'sort_order' => 2,
                'translations' => [
                    'en' => ['title' => 'Scalable', 'description' => 'From startup to enterprise. Grow with us without switching platforms.'],
                    'de' => ['title' => 'Skalierbar', 'description' => 'Vom Start-up bis zum Unternehmen. Wachsen Sie mit uns.'],
                    'es' => ['title' => 'Escalable', 'description' => 'Desde startup hasta empresa. Crece con nosotros.'],
                    'fr' => ['title' => 'Évolutif', 'description' => 'De la startup à l\'entreprise. Grandissez avec nous.'],
                    'it' => ['title' => 'Scalabile', 'description' => 'Dalla startup all\'impresa. Cresci con noi.'],
                    'ru' => ['title' => 'Масштабируемость', 'description' => 'От стартапа до корпорации. Растите вместе с нами.'],
                    'ro' => ['title' => 'Scalabil', 'description' => 'De la startup la enterprise. Creșteți alături de noi.'],
                    'tr' => ['title' => 'Ölçeklenebilir', 'description' => 'Startup\'tan kurumsala. Bizimle büyüyün.'],
                    'ur' => ['title' => 'پھیلنے کے قابل', 'description' => 'اسٹارٹ اپ سے انٹرپرائز تک۔ ہمارے ساتھ بڑھیں۔'],
                    'ar' => ['title' => 'قابل للتوسع', 'description' => 'من الشركات الناشئة إلى المؤسسات. انمِ معنا.'],
                    'fa' => ['title' => 'مقیاس‌پذیر', 'description' => 'از استارتاپ تا سازمان. با ما رشد کنید.'],
                    'ja' => ['title' => 'スケーラブル', 'description' => 'スタートアップからエンタープライズまで。'],
                    'zh' => ['title' => '可扩展', 'description' => '从创业公司到企业，与我们一起成长。'],
                    'ko' => ['title' => '확장 가능', 'description' => '스타트업부터 엔터프라이즈까지. 우리와 함께 성장하세요.'],
                    'hi' => ['title' => 'मापनीय', 'description' => 'स्टार्टअप से एंटरप्राइज़ तक। हमारे साथ बढ़ें।'],
                ],
            ],
        ];

        $featureUrlKeys = ['feature_1', 'feature_2', 'feature_3'];
        foreach ($itemsData as $index => $itemData) {
            $item = LandingSectionItem::updateOrCreate(
                [
                    'landing_section_id' => $section->id,
                    'sort_order' => $itemData['sort_order'],
                ],
                []
            );
            foreach ($locales as $locale) {
                $t = $itemData['translations'][$locale] ?? $itemData['translations'][$fallback] ?? $itemData['translations']['en'];
                $item->setTranslation('title', $locale, $t['title']);
                $item->setTranslation('description', $locale, $t['description']);
            }
            $item->save();

            $urlKey = $featureUrlKeys[$index] ?? 'feature_1';
            try {
                $item->clearMediaCollection('icon');
                $item->addMediaFromUrl($this->sampleUrls[$urlKey])->toMediaCollection('icon');
            } catch (Throwable $e) {
                $this->command?->warn("Could not attach feature item {$index} icon: ".$e->getMessage());
            }
        }
    }

    /**
     * @param  list<string>  $locales
     */
    protected function seedCta(array $locales, string $fallback): void
    {
        $appName = config('app.name');
        $translations = [
            'en' => ['title' => 'Ready to get started?', 'subtitle' => "Join thousands of teams already building with {$appName}.", 'cta_text' => 'Start free trial', 'cta_url' => '/register'],
            'de' => ['title' => 'Bereit loszulegen?', 'subtitle' => "Tausende Teams bauen bereits mit {$appName}.", 'cta_text' => 'Kostenlos testen', 'cta_url' => '/register'],
            'es' => ['title' => '¿Listo para empezar?', 'subtitle' => "Únete a miles de equipos que ya construyen con {$appName}.", 'cta_text' => 'Prueba gratis', 'cta_url' => '/register'],
            'fr' => ['title' => 'Prêt à commencer ?', 'subtitle' => "Rejoignez des milliers d'équipes qui utilisent déjà {$appName}.", 'cta_text' => 'Essai gratuit', 'cta_url' => '/register'],
            'it' => ['title' => 'Pronto a iniziare?', 'subtitle' => "Unisciti a migliaia di team che usano già {$appName}.", 'cta_text' => 'Prova gratuita', 'cta_url' => '/register'],
            'ru' => ['title' => 'Готовы начать?', 'subtitle' => 'К нам уже присоединились тысячи команд.', 'cta_text' => 'Бесплатный пробный период', 'cta_url' => '/register'],
            'ro' => ['title' => 'Gata să începeți?', 'subtitle' => "Alăturați-vă miilor de echipe care construiesc deja cu {$appName}.", 'cta_text' => 'Începeți perioada de probă gratuită', 'cta_url' => '/register'],
            'tr' => ['title' => 'Başlamaya hazır mısınız?', 'subtitle' => "{$appName} ile zaten inşa eden binlerce ekibe katılın.", 'cta_text' => 'Ücretsiz denemeyi başlatın', 'cta_url' => '/register'],
            'ur' => ['title' => 'شروع کرنے کے لیے تیار ہیں؟', 'subtitle' => "ہزاروں ٹیموں میں شامل ہوں جو پہلے سے {$appName} کے ساتھ بنا رہی ہیں۔", 'cta_text' => 'مفت ٹرائل شروع کریں', 'cta_url' => '/register'],
            'ar' => ['title' => 'هل أنت مستعد للبدء؟', 'subtitle' => "انضم إلى آلاف الفرق التي تبني بالفعل مع {$appName}.", 'cta_text' => 'ابدأ التجربة المجانية', 'cta_url' => '/register'],
            'fa' => ['title' => 'آماده شروع هستید؟', 'subtitle' => "به هزاران تیمی بپیوندید که همین الان با {$appName} می‌سازند.", 'cta_text' => 'شروع دوره آزمایشی رایگان', 'cta_url' => '/register'],
            'ja' => ['title' => '始める準備はできましたか？', 'subtitle' => "すでに{$appName}で構築している何千ものチームに参加しましょう。", 'cta_text' => '無料トライアルを開始', 'cta_url' => '/register'],
            'zh' => ['title' => '准备好开始了吗？', 'subtitle' => "加入数千个已在用 {$appName} 构建的团队。", 'cta_text' => '免费试用', 'cta_url' => '/register'],
            'ko' => ['title' => '시작할 준비가 되셨나요?', 'subtitle' => "이미 {$appName}로 구축 중인 수천 개 팀에 합류하세요.", 'cta_text' => '무료 체험 시작', 'cta_url' => '/register'],
            'hi' => ['title' => 'शुरू करने के लिए तैयार हैं?', 'subtitle' => "हजारों टीमें पहले से {$appName} के साथ बना रही हैं। जुड़ें।", 'cta_text' => 'मुफ्त ट्रायल शुरू करें', 'cta_url' => '/register'],
        ];

        $section = LandingSection::updateOrCreate(
            ['type' => 'cta'],
            ['sort_order' => 4]
        );

        foreach ($locales as $locale) {
            $t = $translations[$locale] ?? $translations[$fallback] ?? $translations['en'];
            $section->setTranslation('title', $locale, $t['title']);
            $section->setTranslation('subtitle', $locale, $t['subtitle']);
            $section->setTranslation('cta_text', $locale, $t['cta_text']);
            $section->setTranslation('cta_url', $locale, $t['cta_url']);
        }
        $section->save();

        try {
            $section->clearMediaCollection('image');
            $section->addMediaFromUrl($this->sampleUrls['cta'])->toMediaCollection('image');
        } catch (Throwable $e) {
            $this->command?->warn('Could not attach CTA sample image: '.$e->getMessage());
        }
    }
}
