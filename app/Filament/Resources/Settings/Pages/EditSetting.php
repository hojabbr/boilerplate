<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditSetting extends EditRecord
{
    use Translatable;

    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * When switching locale, preserve non-translatable attributes (e.g. key) from the record
     * so they are not lost when the form is refilled (disabled fields may be omitted from form state).
     */
    public function updatedActiveLocale(): void
    {
        if (filament('spatie-translatable')->getPersistLocale()) {
            session()->put('spatie_translatable_active_locale', $this->activeLocale);
        }

        if (blank($this->oldActiveLocale)) {
            return;
        }

        $this->resetValidation();

        $translatableAttributes = static::getResource()::getTranslatableAttributes();

        try {
            $this->otherLocaleData[$this->oldActiveLocale] = Arr::only(
                $this->form->getState(),
                $translatableAttributes
            );

            $record = $this->getRecord();
            $nonTranslatableKeys = array_diff(
                $record->getFillable(),
                $translatableAttributes
            );
            $nonTranslatableFromRecord = Arr::only(
                $record->attributesToArray(),
                $nonTranslatableKeys
            );
            $fromForm = Arr::except($this->form->getState(), $translatableAttributes);
            foreach ($nonTranslatableFromRecord as $attr => $value) {
                if (! array_key_exists($attr, $fromForm) || $fromForm[$attr] === '' || $fromForm[$attr] === null) {
                    $fromForm[$attr] = $value;
                }
            }

            $this->form->fill([
                ...$fromForm,
                ...$this->otherLocaleData[$this->activeLocale] ?? [],
            ]);

            unset($this->otherLocaleData[$this->activeLocale]);
        } catch (ValidationException $e) {
            $this->activeLocale = $this->oldActiveLocale;

            throw $e;
        }
    }
}
