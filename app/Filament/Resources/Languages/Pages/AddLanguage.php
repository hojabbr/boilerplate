<?php

namespace App\Filament\Resources\Languages\Pages;

use App\Core\Models\Language;
use App\Core\Services\AddLocaleService;
use App\Filament\Resources\Languages\LanguageResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

class AddLanguage extends Page
{
    protected static string $resource = LanguageResource::class;

    protected string $view = 'filament.resources.languages.pages.add-language';

    protected static ?string $title = 'Add language';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->authorize('create', Language::class);
        $this->form->fill([
            'code' => '',
            'name' => '',
            'script' => 'Latn',
            'regional' => '',
            'direction' => 'ltr',
            'is_default' => false,
            'create_lang_file' => true,
            'add_to_seeders' => true,
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('addLanguage')
                ->footer([
                    Actions::make($this->getFormActions())
                        ->alignment(Alignment::End)
                        ->key('form-actions'),
                ]),
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        $url = LanguageResource::getUrl('index');

        return [
            Action::make('addLanguage')
                ->label('Add language')
                ->submit('addLanguage')
                ->keyBindings(['mod+s']),
            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
                ->alpineClickHandler(
                    FilamentView::hasSpaMode($url)
                        ? 'document.referrer ? window.history.back() : Livewire.navigate('.Js::from($url).')'
                        : 'document.referrer ? window.history.back() : (window.location.href = '.Js::from($url).')'
                )
                ->color('gray'),
        ];
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Locale')
                    ->description('Language record and display options.')
                    ->schema([
                        TextInput::make('code')
                            ->label('Locale code')
                            ->required()
                            ->maxLength(15)
                            ->rules(['regex:/^[a-z]{2}(-[a-zA-Z0-9]+)?$/'])
                            ->unique(Language::class, 'code')
                            ->helperText('e.g. pt, pt-BR'),
                        TextInput::make('name')
                            ->label('Name (e.g. Portuguese)')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('script')
                            ->maxLength(255)
                            ->default('Latn')
                            ->helperText('e.g. Latn, Arab'),
                        TextInput::make('regional')
                            ->maxLength(255)
                            ->helperText('e.g. pt_BR'),
                        Select::make('direction')
                            ->options(['ltr' => 'LTR', 'rtl' => 'RTL'])
                            ->default('ltr')
                            ->required(),
                        Toggle::make('is_default')
                            ->label('Set as default locale'),
                    ])
                    ->columns(2),
                Section::make('Options')
                    ->description('Create lang file and add placeholder entries to content seeders.')
                    ->schema([
                        Toggle::make('create_lang_file')
                            ->label('Create lang file (empty keys from en.json)')
                            ->default(true),
                        Toggle::make('add_to_seeders')
                            ->label('Add placeholder entries to SettingSeeder, PageSeeder, LandingSectionSeeder, BlogPostSeeder')
                            ->default(true),
                    ]),
            ]);
    }

    public function addLanguage(AddLocaleService $addLocale): void
    {
        $this->authorize('create', Language::class);

        $data = $this->form->getState();
        $code = strtolower(trim((string) ($data['code'] ?? '')));
        if ($code === '') {
            Notification::make()->title('Locale code is required.')->danger()->send();

            return;
        }

        $addLocale->add(
            code: $code,
            name: (string) ($data['name'] ?? $code),
            script: (string) ($data['script'] ?? 'Latn'),
            native: (string) ($data['name'] ?? $code),
            regional: (string) ($data['regional'] ?? ''),
            direction: in_array($data['direction'] ?? 'ltr', ['ltr', 'rtl'], true) ? $data['direction'] : 'ltr',
            isDefault: (bool) ($data['is_default'] ?? false),
            createLangFile: (bool) ($data['create_lang_file'] ?? true),
            addToSeeders: (bool) ($data['add_to_seeders'] ?? true),
        );

        Notification::make()
            ->title('Language added.')
            ->body("Locale [{$code}] has been added. You can edit it below.")
            ->success()
            ->send();

        $language = Language::query()->where('code', $code)->first();

        $this->redirect($language
            ? LanguageResource::getUrl('edit', ['record' => $language])
            : LanguageResource::getUrl('index'));
    }
}
