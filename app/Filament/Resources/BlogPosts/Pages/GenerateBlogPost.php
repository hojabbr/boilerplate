<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Core\Models\Language;
use App\Core\Services\Ai\Support\AiProviderOptions;
use App\Domains\Blog\Jobs\GenerateBlogPostsJob;
use App\Domains\Blog\Models\BlogPost;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Livewire\Attributes\Locked;

class GenerateBlogPost extends Page
{
    protected static string $resource = BlogPostResource::class;

    protected string $view = 'filament.resources.blog-posts.pages.generate-blog-post';

    protected static ?string $title = 'Generate with AI';

    protected static ?string $navigationLabel = 'Generate with AI';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    #[Locked]
    public bool $isGenerating = false;

    public function mount(): void
    {
        $this->authorizeAccess();
        $this->data = [
            'topic_source' => 'specific',
            'topic' => '',
            'hint' => '',
            'length' => 'medium',
            'provider' => null,
            'language_ids' => [],
            'generate_image' => false,
            'generate_audio' => false,
        ];
    }

    protected function authorizeAccess(): void
    {
        $this->authorize('create', BlogPost::class);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedSchema::make('form'),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $providers = AiProviderOptions::availableProviders();
        if (empty($providers)) {
            return $schema->components([
                Section::make('Configuration required')
                    ->description('Add at least one AI provider API key in config/ai.php (e.g. OPENAI_API_KEY in .env).')
                    ->schema([]),
            ]);
        }

        return $schema
            ->statePath('data')
            ->components([
                Wizard::make([
                    Step::make('Topic')
                        ->description('Choose how to define the blog topic')
                        ->schema([
                            Radio::make('topic_source')
                                ->label('Topic source')
                                ->options([
                                    'specific' => 'I have a specific topic or subject',
                                    'ai_chooses' => 'Let the AI choose based on existing blog content',
                                ])
                                ->default('specific')
                                ->required()
                                ->live(),
                            TextInput::make('topic')
                                ->label('Topic or subject')
                                ->placeholder('e.g. Getting started with Laravel queues')
                                ->required(fn ($get) => $get('topic_source') === 'specific')
                                ->visible(fn ($get) => $get('topic_source') === 'specific')
                                ->maxLength(500),
                            TextInput::make('hint')
                                ->label('Optional hint (category or direction)')
                                ->placeholder('e.g. Something about DevOps')
                                ->visible(fn ($get) => $get('topic_source') === 'ai_chooses')
                                ->maxLength(255),
                            Select::make('length')
                                ->label('Post length')
                                ->options([
                                    'short' => 'Short',
                                    'medium' => 'Medium',
                                    'long' => 'Very long',
                                ])
                                ->default('medium')
                                ->required()
                                ->helperText('Short: 2–3 paragraphs. Medium: 4–6 paragraphs. Very long: in-depth, multiple sections.'),
                        ]),
                    Step::make('Provider')
                        ->description('Select AI provider (uses provider default model from Laravel AI SDK)')
                        ->schema([
                            Select::make('provider')
                                ->label('AI provider')
                                ->options($providers)
                                ->required()
                                ->live(),
                        ]),
                    Step::make('Options & languages')
                        ->description('Languages and optional image/audio')
                        ->schema([
                            CheckboxList::make('language_ids')
                                ->label('Languages')
                                ->options(
                                    Language::query()->orderBy('sort_order')->pluck('name', 'id')->all()
                                )
                                ->required()
                                ->minItems(1)
                                ->columns(2),
                            Toggle::make('generate_image')
                                ->label('Generate featured image')
                                ->visible(fn ($get) => AiProviderOptions::providerSupportsImages((string) $get('provider')))
                                ->default(false),
                            Toggle::make('generate_audio')
                                ->label('Generate audio (TTS)')
                                ->visible(fn ($get) => AiProviderOptions::providerSupportsTts((string) $get('provider')))
                                ->default(false),
                        ]),
                    Step::make('Generate')
                        ->description('Review and generate')
                        ->schema([
                            Section::make('Summary')
                                ->schema([])
                                ->description(fn () => $this->getSummaryDescription()),
                        ]),
                ])
                    ->submitAction(view('filament.resources.blog-posts.pages.generate-submit-action'))
                    ->alpineSubmitHandler('$wire.generate()'),
            ]);
    }

    protected function getSummaryDescription(): string
    {
        $d = $this->data;
        $topicMode = ($d['topic_source'] ?? '') === 'specific' ? 'Specific topic' : 'AI chooses topic';
        $topic = $d['topic_source'] === 'specific' ? ($d['topic'] ?? '') : ($d['hint'] ?? '—');
        $length = match ($d['length'] ?? 'medium') {
            'short' => 'Short',
            'long' => 'Very long',
            default => 'Medium',
        };
        $provider = $d['provider'] ?? '—';
        $langCount = is_array($d['language_ids'] ?? null) ? count($d['language_ids']) : 0;
        $img = ($d['generate_image'] ?? false) ? 'Yes' : 'No';
        $audio = ($d['generate_audio'] ?? false) ? 'Yes' : 'No';

        return "Topic mode: {$topicMode}\nTopic/hint: {$topic}\nLength: {$length}\nProvider: {$provider} (SDK default model)\nLanguages: {$langCount} selected\nFeatured image: {$img}\nAudio (TTS): {$audio}";
    }

    public function generate(): void
    {
        if ($this->isGenerating) {
            return;
        }

        $this->authorizeAccess();

        $data = $this->form->getState();
        $languageIds = $data['language_ids'] ?? [];
        if (empty($languageIds)) {
            Notification::make()->title('Select at least one language.')->danger()->send();

            return;
        }

        $this->isGenerating = true;

        GenerateBlogPostsJob::dispatch($data, (int) auth()->id());

        $this->isGenerating = false;
        Notification::make()
            ->title('Generation started.')
            ->body('You will be notified when the blog post(s) are ready.')
            ->success()
            ->send();

        $this->redirect(BlogPostResource::getUrl('index'));
    }
}
