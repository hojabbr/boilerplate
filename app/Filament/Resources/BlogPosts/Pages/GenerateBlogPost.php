<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Core\Models\Language;
use App\Core\Services\Ai\Support\AiProviderOptions;
use App\Domains\Blog\Jobs\GenerateBlogPostsJob;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Blog\Models\BlogPostSeries;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\BlogPostSeriesResource;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
            'generation_type' => 'one_time',
            'topic_source' => 'specific',
            'topic' => '',
            'hint' => '',
            'length' => 'medium',
            'provider' => null,
            'language_ids' => [],
            'generate_image' => false,
            'generate_audio' => false,
            'publish_immediately' => false,
            'purpose' => '',
            'objective' => '',
            'topics' => '',
            'start_date' => null,
            'end_date' => null,
            'days_of_week' => [],
            'run_at_hours' => [],
            'total_posts_limit' => null,
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

        $daysOfWeekOptions = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        $hourOptions = [];
        for ($h = 0; $h < 24; $h++) {
            $hourOptions[$h] = sprintf('%02d:00', $h);
        }

        return $schema
            ->statePath('data')
            ->components([
                Wizard::make([
                    Step::make('Generation type')
                        ->description('One-time post or scheduled series')
                        ->schema([
                            Radio::make('generation_type')
                                ->label('Type')
                                ->options([
                                    'one_time' => 'One-time (generate now)',
                                    'scheduled_series' => 'Scheduled series (recurring)',
                                ])
                                ->default('one_time')
                                ->required()
                                ->live(),
                        ]),
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
                        ])
                        ->visible(fn ($get) => ($get('generation_type') ?? 'one_time') === 'one_time'),
                    Step::make('Series definition')
                        ->description('Purpose, objective, and topics for the series')
                        ->schema([
                            TextInput::make('name')
                                ->label('Series name (optional)')
                                ->placeholder('e.g. Weekly DevOps Tips')
                                ->maxLength(255),
                            Textarea::make('purpose')
                                ->label('Purpose of the series')
                                ->placeholder('e.g. Educate readers on best practices')
                                ->required(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series')
                                ->rows(2),
                            Textarea::make('objective')
                                ->label('Objective')
                                ->placeholder('e.g. One actionable tip per post')
                                ->required(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series')
                                ->rows(2),
                            Textarea::make('topics')
                                ->label('What the series is about')
                                ->placeholder('e.g. CI/CD, monitoring, security')
                                ->required(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series')
                                ->rows(2),
                            Select::make('length')
                                ->label('Post length')
                                ->options([
                                    'short' => 'Short',
                                    'medium' => 'Medium',
                                    'long' => 'Very long',
                                ])
                                ->default('medium')
                                ->required(),
                        ])
                        ->visible(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series'),
                    Step::make('Schedule')
                        ->description('When and how often to run')
                        ->schema([
                            DatePicker::make('start_date')
                                ->label('Start date')
                                ->required(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series')
                                ->native(false),
                            DatePicker::make('end_date')
                                ->label('End date')
                                ->required(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series')
                                ->native(false),
                            CheckboxList::make('days_of_week')
                                ->label('Days of the week')
                                ->options($daysOfWeekOptions)
                                ->required(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series')
                                ->minItems(1)
                                ->columns(4),
                            CheckboxList::make('run_at_hours')
                                ->label('Hours of the day (UTC)')
                                ->options($hourOptions)
                                ->required(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series')
                                ->minItems(1)
                                ->columns(6),
                            TextInput::make('total_posts_limit')
                                ->label('Total posts cap (optional)')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('Leave empty for no limit'),
                        ])
                        ->visible(fn ($get) => ($get('generation_type') ?? '') === 'scheduled_series'),
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
                            Toggle::make('publish_immediately')
                                ->label('Publish immediately')
                                ->helperText('If off, posts are created as drafts.')
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
        $isSeries = ($d['generation_type'] ?? 'one_time') === 'scheduled_series';
        $length = match ($d['length'] ?? 'medium') {
            'short' => 'Short',
            'long' => 'Very long',
            default => 'Medium',
        };
        $provider = $d['provider'] ?? '—';
        $langCount = is_array($d['language_ids'] ?? null) ? count($d['language_ids']) : 0;
        $img = ($d['generate_image'] ?? false) ? 'Yes' : 'No';
        $audio = ($d['generate_audio'] ?? false) ? 'Yes' : 'No';
        $publish = ($d['publish_immediately'] ?? false) ? 'Yes' : 'No';

        if ($isSeries) {
            $days = is_array($d['days_of_week'] ?? null) ? implode(', ', array_map(fn ($i) => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$i] ?? $i, $d['days_of_week'])) : '—';
            $hours = is_array($d['run_at_hours'] ?? null) ? implode(', ', array_map(fn ($h) => sprintf('%02d:00', $h), $d['run_at_hours'])) : '—';
            $cap = isset($d['total_posts_limit']) && $d['total_posts_limit'] !== '' ? (string) $d['total_posts_limit'] : 'No limit';

            return "Type: Scheduled series\nPurpose: ".($d['purpose'] ?? '—')."\nObjective: ".($d['objective'] ?? '—')."\nTopics: ".($d['topics'] ?? '—')."\nStart: ".($d['start_date'] ?? '—').' End: '.($d['end_date'] ?? '—')."\nDays: {$days}\nHours: {$hours}\nCap: {$cap}\nLength: {$length}\nProvider: {$provider}\nLanguages: {$langCount}\nImage: {$img} Audio: {$audio}\nPublish immediately: {$publish}";
        }

        $topicMode = ($d['topic_source'] ?? '') === 'specific' ? 'Specific topic' : 'AI chooses topic';
        $topic = ($d['topic_source'] ?? '') === 'specific' ? ($d['topic'] ?? '') : ($d['hint'] ?? '—');

        return "Type: One-time\nTopic mode: {$topicMode}\nTopic/hint: {$topic}\nLength: {$length}\nProvider: {$provider}\nLanguages: {$langCount}\nImage: {$img} Audio: {$audio}\nPublish immediately: {$publish}";
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

        $isSeries = ($data['generation_type'] ?? 'one_time') === 'scheduled_series';

        if ($isSeries) {
            $runAtHours = $data['run_at_hours'] ?? [];
            if (empty($runAtHours)) {
                Notification::make()->title('Select at least one hour of the day for the schedule.')->danger()->send();

                return;
            }
            $daysOfWeek = $data['days_of_week'] ?? [];
            if (empty($daysOfWeek)) {
                Notification::make()->title('Select at least one day of the week.')->danger()->send();

                return;
            }

            $this->isGenerating = true;
            BlogPostSeries::create([
                'user_id' => auth()->id(),
                'name' => $data['name'] ?? null,
                'purpose' => $data['purpose'] ?? '',
                'objective' => $data['objective'] ?? '',
                'topics' => $data['topics'] ?? '',
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'days_of_week' => array_map('intval', $daysOfWeek),
                'run_at_hours' => array_map('intval', $runAtHours),
                'posts_per_run' => 1,
                'total_posts_limit' => isset($data['total_posts_limit']) && $data['total_posts_limit'] !== '' && $data['total_posts_limit'] !== null ? (int) $data['total_posts_limit'] : null,
                'provider' => $data['provider'],
                'length' => $data['length'] ?? 'medium',
                'language_ids' => array_map('intval', $languageIds),
                'generate_image' => (bool) ($data['generate_image'] ?? false),
                'generate_audio' => (bool) ($data['generate_audio'] ?? false),
                'publish_immediately' => (bool) ($data['publish_immediately'] ?? false),
            ]);
            $this->isGenerating = false;
            Notification::make()
                ->title('Scheduled series created.')
                ->body('Posts will be generated automatically at the chosen times. You can view or delete the series from the Scheduled series page.')
                ->success()
                ->send();

            $this->redirect(BlogPostSeriesResource::getUrl('index'));
        } else {
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
}
