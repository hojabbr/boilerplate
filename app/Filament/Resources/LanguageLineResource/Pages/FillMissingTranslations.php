<?php

namespace App\Filament\Resources\LanguageLineResource\Pages;

use App\Filament\Resources\LanguageLineResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Spatie\TranslationLoader\LanguageLine;

class FillMissingTranslations extends Page
{
    protected static string $resource = LanguageLineResource::class;

    /**
     * Locale we are filling (from query or form).
     */
    public ?string $locale = null;

    /**
     * Missing lines for current locale: [['key' => string, 'reference' => string], ...]
     *
     * @var list<array{key: string, reference: string}>
     */
    public array $missingLines = [];

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->authorize('viewAny', LanguageLine::class);

        $locale = request()->query('locale');
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        if ($locale === null || ! in_array($locale, $locales, true)) {
            $missingByLocale = $this->getMissingKeysByLocale();
            $locale = $locale ?: (array_key_first($missingByLocale) ?? $locales[0]);
        }

        $this->locale = $locale;
        $this->missingLines = $this->getMissingLinesForLocale($locale);
        $values = [];
        foreach ($this->missingLines as $item) {
            $values[$item['key']] = '';
        }
        $this->form->fill([
            'locale' => $locale,
            'values' => $values,
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        $count = count($this->missingLines);

        return __('Fill missing translations').' ('.$this->locale.') — '.$count.' '.__('keys');
    }

    /**
     * @return array<string, int>
     */
    protected function getMissingKeysByLocale(): array
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        $lines = LanguageLine::query()->where('group', '*')->get();
        $missingByLocale = [];
        foreach ($locales as $loc) {
            $count = 0;
            foreach ($lines as $line) {
                $text = $line->text ?? [];
                $val = $text[$loc] ?? null;
                if ($val === null || (string) $val === '') {
                    $count++;
                }
            }
            if ($count > 0) {
                $missingByLocale[$loc] = $count;
            }
        }

        return $missingByLocale;
    }

    /**
     * @return list<array{key: string, reference: string}>
     */
    protected function getMissingLinesForLocale(string $locale): array
    {
        $referenceLocale = config('app.fallback_locale', 'en');
        $lines = LanguageLine::query()->where('group', '*')->get();
        $out = [];
        foreach ($lines as $line) {
            $text = $line->text ?? [];
            $val = $text[$locale] ?? null;
            if ($val === null || (string) $val === '') {
                $reference = (string) ($text[$referenceLocale] ?? $text['en'] ?? $line->key);

                $out[] = [
                    'key' => $line->key,
                    'reference' => $reference,
                ];
            }
        }

        return $out;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('saveMissing')
                ->footer([
                    Actions::make($this->getFormActions())
                        ->alignment(\Filament\Support\Enums\Alignment::End)
                        ->key('form-actions'),
                ]),
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        $indexUrl = LanguageLineResource::getUrl('index');
        $fillMissingUrl = LanguageLineResource::getUrl('fill-missing');

        return [
            Action::make('saveMissing')
                ->label(__('Save translations'))
                ->submit('saveMissing')
                ->keyBindings(['mod+s']),
            Action::make('cancel')
                ->label(__('filament-panels::resources/pages/create-record.form.actions.cancel.label'))
                ->alpineClickHandler(
                    FilamentView::hasSpaMode($indexUrl)
                        ? 'document.referrer ? window.history.back() : Livewire.navigate('.Js::from($indexUrl).')'
                        : 'document.referrer ? window.history.back() : (window.location.href = '.Js::from($indexUrl).')'
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
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        $localeOptions = array_combine($locales, $locales);

        $components = [
            Section::make(__('Locale'))
                ->description(__('Select the locale to fill. Only keys missing for that locale are listed.'))
                ->schema([
                    Select::make('locale')
                        ->label(__('Locale'))
                        ->options($localeOptions)
                        ->required()
                        ->default($this->locale)
                        ->live()
                        ->afterStateUpdated(function (?string $state): void {
                            if ($state !== null && $state !== '') {
                                $this->redirect(LanguageLineResource::getUrl('fill-missing').'?locale='.urlencode($state));
                            }
                        }),
                ])
                ->collapsible(false),
        ];

        if ($this->missingLines !== []) {
            $inputs = [];
            foreach ($this->missingLines as $item) {
                $key = $item['key'];
                $reference = $item['reference'];
                $inputs[] = TextInput::make('values.'.$key)
                    ->label(Str::limit($key, 70))
                    ->placeholder(Str::limit($reference, 80))
                    ->helperText(Str::limit($reference, 120))
                    ->maxLength(65535)
                    ->columnSpanFull();
            }
            $components[] = Section::make(__('Missing keys'))
                ->description(__('Enter the translation for each key. The placeholder shows the reference text (e.g. from English).'))
                ->schema($inputs)
                ->collapsible(false);
        } else {
            $components[] = Section::make(__('Missing keys'))
                ->schema([
                    Text::make(__('No missing keys for this locale.')),
                ])
                ->collapsible(false);
        }

        return $schema->statePath('data')->components($components);
    }

    public function saveMissing(): void
    {
        $this->authorize('viewAny', LanguageLine::class);

        $data = $this->form->getState();
        $locale = $data['locale'] ?? $this->locale;
        $values = $data['values'] ?? [];

        if ($locale === null || $locale === '') {
            Notification::make()->title(__('Please select a locale.'))->danger()->send();

            return;
        }

        $updated = 0;
        foreach ($values as $key => $value) {
            $value = is_string($value) ? trim($value) : '';
            if ($value === '') {
                continue;
            }
            $line = LanguageLine::query()->where('group', '*')->where('key', $key)->first();
            if ($line !== null) {
                $text = $line->text ?? [];
                $text[$locale] = $value;
                $line->text = $text;
                $line->save();
                $updated++;
            }
        }

        \Illuminate\Support\Facades\Cache::forget(LanguageLine::getCacheKey('*', $locale));

        Notification::make()
            ->title(__('Translations saved'))
            ->body(__(':count translation(s) updated for :locale.', ['count' => $updated, 'locale' => $locale]))
            ->success()
            ->send();

        $this->redirect(LanguageLineResource::getUrl('fill-missing').'?locale='.urlencode($locale));
    }
}
