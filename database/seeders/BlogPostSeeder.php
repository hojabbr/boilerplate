<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Language;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
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
     * Run the database seeds.
     */
    public function run(): void
    {
        $fallbackLocale = config('app.fallback_locale', 'en');
        $slug = 'welcome-to-our-blog';

        foreach (Language::orderBy('sort_order')->get() as $language) {
            $content = $this->welcomePostByLocale[$language->code]
                ?? $this->welcomePostByLocale[$fallbackLocale]
                ?? $this->welcomePostByLocale['en'];

            BlogPost::updateOrCreate(
                [
                    'language_id' => $language->id,
                    'slug' => $slug,
                ],
                [
                    'title' => $content['title'],
                    'excerpt' => $content['excerpt'],
                    'body' => $content['body'],
                    'meta_description' => $content['meta_description'],
                    'published_at' => now(),
                ]
            );
        }
    }
}
