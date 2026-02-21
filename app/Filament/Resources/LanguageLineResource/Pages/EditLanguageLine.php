<?php

namespace App\Filament\Resources\LanguageLineResource\Pages;

use App\Filament\Resources\LanguageLineResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Arrayable;

class EditLanguageLine extends EditRecord
{
    protected static string $resource = LanguageLineResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $text = $this->record->text ?? [];
        foreach ($text as $locale => $value) {
            $data['text'][$locale] = $value;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $text = $data['text'] ?? [];
        if ($text instanceof Arrayable) {
            $text = $text->toArray();
        }
        $data['text'] = array_filter($text, fn ($v) => $v !== null && (string) $v !== '');

        return $data;
    }
}
