<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Throwable;

class BlogPostSeeder extends Seeder
{
    /**
     * Sample gallery image URLs (Unsplash) for blog posts.
     *
     * @var array<int, string>
     */
    protected array $galleryUrls = [
        'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=1200&q=80',
        'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=1200&q=80',
        'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=1200&q=80',
        'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=1200&q=80',
        'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&q=80',
        'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1200&q=80',
        'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=1200&q=80',
        'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&q=80',
        'https://images.unsplash.com/photo-1551434678-e076c223a692?w=1200&q=80',
        'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
    ];

    /**
     * Welcome post content per locale.
     * Fallback to 'en' when a locale is missing.
     *
     * @var array<string, array{title: string, excerpt: string, body: string, meta_description: string}>
     */
    protected array $welcomePostByLocale = [
        'en' => [
            'title' => 'Welcome to our blog',
            'excerpt' => 'This is the first post. Edit or add more in the admin panel.',
            'body' => '<p>This is the body of the first blog post. You can edit this content in the Filament admin panel.</p>',
            'meta_description' => 'Welcome to our blog',
        ],
        'de' => [
            'title' => 'Willkommen in unserem Blog',
            'excerpt' => 'Dies ist der erste Beitrag. Bearbeiten oder fügen Sie weitere im Admin-Bereich hinzu.',
            'body' => '<p>Dies ist der Inhalt des ersten Blogbeitrags. Sie können diesen Inhalt im Filament-Admin-Bereich bearbeiten.</p>',
            'meta_description' => 'Willkommen in unserem Blog',
        ],
        'es' => [
            'title' => 'Bienvenido a nuestro blog',
            'excerpt' => 'Esta es la primera entrada. Edite o añada más en el panel de administración.',
            'body' => '<p>Este es el contenido de la primera entrada del blog. Puede editar este contenido en el panel de administración de Filament.</p>',
            'meta_description' => 'Bienvenido a nuestro blog',
        ],
        'fr' => [
            'title' => 'Bienvenue sur notre blog',
            'excerpt' => 'Ceci est le premier article. Modifiez ou ajoutez-en d\'autres dans le panneau d\'administration.',
            'body' => '<p>Ceci est le contenu du premier article de blog. Vous pouvez modifier ce contenu dans le panneau d\'administration Filament.</p>',
            'meta_description' => 'Bienvenue sur notre blog',
        ],
        'it' => [
            'title' => 'Benvenuto nel nostro blog',
            'excerpt' => 'Questo è il primo articolo. Modifica o aggiungine altri nel pannello di amministrazione.',
            'body' => '<p>Questo è il contenuto del primo articolo del blog. Puoi modificare questo contenuto nel pannello di amministrazione Filament.</p>',
            'meta_description' => 'Benvenuto nel nostro blog',
        ],
        'ru' => [
            'title' => 'Добро пожаловать в наш блог',
            'excerpt' => 'Это первая запись. Редактируйте или добавьте новые в панели администратора.',
            'body' => '<p>Это содержание первой записи блога. Вы можете редактировать этот контент в панели администратора Filament.</p>',
            'meta_description' => 'Добро пожаловать в наш блог',
        ],
        'ar' => [
            'title' => 'مرحباً بكم في مدونتنا',
            'excerpt' => 'هذه أول مشاركة. قم بالتعديل أو الإضافة في لوحة الإدارة.',
            'body' => '<p>هذا محتوى أول مشاركة في المدونة. يمكنك تحرير هذا المحتوى في لوحة إدارة Filament.</p>',
            'meta_description' => 'مرحباً بكم في مدونتنا',
        ],
        'fa' => [
            'title' => 'به وبلاگ ما خوش آمدید',
            'excerpt' => 'این اولین پست است. در پنل مدیریت ویرایش یا موارد بیشتری اضافه کنید.',
            'body' => '<p>این متن اولین پست وبلاگ است. می‌توانید این محتوا را در پنل مدیریت Filament ویرایش کنید.</p>',
            'meta_description' => 'به وبلاگ ما خوش آمدید',
        ],
        'ja' => [
            'title' => 'ブログへようこそ',
            'excerpt' => 'これは最初の投稿です。管理パネルで編集または追加してください。',
            'body' => '<p>これは最初のブログ投稿の本文です。Filamentの管理パネルでこのコンテンツを編集できます。</p>',
            'meta_description' => 'ブログへようこそ',
        ],
        'zh' => [
            'title' => '欢迎来到我们的博客',
            'excerpt' => '这是第一篇文章。请在管理面板中编辑或添加更多内容。',
            'body' => '<p>这是第一篇博客文章的正文。您可以在 Filament 管理面板中编辑此内容。</p>',
            'meta_description' => '欢迎来到我们的博客',
        ],
        'ko' => [
            'title' => '블로그에 오신 것을 환영합니다',
            'excerpt' => '첫 번째 게시글입니다. 관리자 패널에서 편집하거나 더 추가하세요.',
            'body' => '<p>첫 번째 블로그 게시글의 본문입니다. Filament 관리자 패널에서 이 콘텐츠를 편집할 수 있습니다.</p>',
            'meta_description' => '블로그에 오신 것을 환영합니다',
        ],
    ];

    /**
     * Additional posts (slug => en-only content). Used for all locales via fallback.
     *
     * @var array<string, array{title: string, excerpt: string, body: string, meta_description: string}>
     */
    protected array $additionalPosts = [
        'getting-started' => [
            'title' => 'Getting started with our platform',
            'excerpt' => 'A quick guide to set up your account and make the most of our features.',
            'body' => '<p>Welcome to the platform. This guide will walk you through the initial setup and key features.</p><p>First, complete your profile and preferences. Then explore the dashboard and connect your first project. Our support team is available if you need help.</p><p>Check out the documentation for detailed guides and API references.</p>',
            'meta_description' => 'Getting started guide for our platform.',
        ],
        'best-practices' => [
            'title' => 'Best practices for success',
            'excerpt' => 'Learn how to get the best results by following these proven strategies.',
            'body' => '<p>Over time we have gathered insights from thousands of users. Here are the practices that consistently lead to better outcomes.</p><p>Start with clear goals, iterate based on feedback, and keep your workflow simple. Regular reviews and small improvements often beat big, infrequent changes.</p>',
            'meta_description' => 'Best practices and tips for success.',
        ],
        'security-and-privacy' => [
            'title' => 'Security and privacy at the core',
            'excerpt' => 'How we protect your data and what you can do to stay secure.',
            'body' => '<p>Security is a top priority. We use encryption, regular audits, and strict access controls to protect your information.</p><p>You can enable two-factor authentication and manage session and API keys from your account settings. We never store sensitive credentials in plain text.</p>',
            'meta_description' => 'Security and privacy overview.',
        ],
        'new-features-roundup' => [
            'title' => 'New features and improvements',
            'excerpt' => 'A roundup of the latest updates and how they can help you.',
            'body' => '<p>This release brings several improvements based on your feedback. The dashboard is faster, and we have added new options for customization.</p><p>Export and reporting have been enhanced. You can now schedule reports and share them with your team. Try the new templates and let us know what you think.</p>',
            'meta_description' => 'Latest features and product updates.',
        ],
        'integrating-with-your-stack' => [
            'title' => 'Integrating with your stack',
            'excerpt' => 'Connect your existing tools and automate your workflow.',
            'body' => '<p>Our API and integrations make it easy to fit into your current setup. We offer webhooks, REST and GraphQL APIs, and pre-built connectors for popular tools.</p><p>Documentation and code samples are available for all major languages. If you need a custom integration, our team can help.</p>',
            'meta_description' => 'Integration guide and API overview.',
        ],
        'scaling-with-confidence' => [
            'title' => 'Scaling with confidence',
            'excerpt' => 'How we help you grow from prototype to production.',
            'body' => '<p>Whether you are just starting or already at scale, the platform is designed to grow with you. We handle the infrastructure so you can focus on your product.</p><p>Performance monitoring and alerts help you stay on top of usage and costs. Upgrade or adjust your plan at any time.</p>',
            'meta_description' => 'Scaling and growth on our platform.',
        ],
        'customer-story' => [
            'title' => 'Customer story: from idea to launch',
            'excerpt' => 'How one team used our platform to ship their product in record time.',
            'body' => '<p>We spoke with a team that went from concept to launch in under three months. They shared their workflow, challenges, and how they used our features to stay on track.</p><p>Key takeaways: start small, ship often, and use automation to free up time for the work that matters most.</p>',
            'meta_description' => 'Customer success story and case study.',
        ],
        'support-and-resources' => [
            'title' => 'Support and learning resources',
            'excerpt' => 'Where to find help, documentation, and community.',
            'body' => '<p>We offer multiple ways to get support: in-app chat, email, and a comprehensive help center. For developers, there are API docs, tutorials, and sample projects.</p><p>Our community forum is a great place to ask questions and share tips. We also run regular webinars and publish guides on the blog.</p>',
            'meta_description' => 'Support options and learning resources.',
        ],
        'roadmap-and-feedback' => [
            'title' => 'Roadmap and how we use your feedback',
            'excerpt' => 'What we are building next and how your input shapes our priorities.',
            'body' => '<p>Our roadmap is driven largely by user feedback. We review feature requests and usage data to decide what to build next.</p><p>You can submit ideas and vote on others in the feedback portal. We publish quarterly roadmap updates so you know what to expect.</p>',
            'meta_description' => 'Product roadmap and feedback process.',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fallbackLocale = config('app.fallback_locale', 'en');
        $languages = Language::orderBy('sort_order')->get();
        $localeKeys = array_keys($this->welcomePostByLocale);

        $allPosts = ['welcome-to-our-blog' => $this->welcomePostByLocale];
        foreach ($this->additionalPosts as $slug => $enContent) {
            $allPosts[$slug] = [];
            foreach ($localeKeys as $locale) {
                $allPosts[$slug][$locale] = $enContent;
            }
        }

        $galleryIndex = 0;
        foreach ($allPosts as $slug => $contentsByLocale) {
            foreach ($languages as $language) {
                $content = $contentsByLocale[$language->code]
                    ?? $contentsByLocale[$fallbackLocale]
                    ?? $contentsByLocale['en']
                    ?? reset($contentsByLocale);

                $post = BlogPost::updateOrCreate(
                    [
                        'language_id' => $language->id,
                        'slug' => $slug,
                    ],
                    [
                        'title' => $content['title'],
                        'excerpt' => $content['excerpt'],
                        'body' => $content['body'],
                        'meta_description' => $content['meta_description'],
                        'published_at' => now()->subDays(rand(0, 60)),
                    ]
                );

                try {
                    $post->clearMediaCollection('gallery');
                    $url = $this->galleryUrls[$galleryIndex % count($this->galleryUrls)];
                    $post->addMediaFromUrl($url)->toMediaCollection('gallery');
                } catch (Throwable $e) {
                    $this->command?->warn("Could not attach blog gallery for {$slug}: ".$e->getMessage());
                }
            }
            $galleryIndex++;
        }
    }
}
