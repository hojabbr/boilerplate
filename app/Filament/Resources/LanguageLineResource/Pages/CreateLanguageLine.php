<?php

namespace App\Filament\Resources\LanguageLineResource\Pages;

use App\Filament\Resources\LanguageLineResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Arrayable;

class CreateLanguageLine extends CreateRecord
{
    protected static string $resource = LanguageLineResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $text = $data['text'] ?? [];
        if ($text instanceof Arrayable) {
            $text = $text->toArray();
        }
        $data['text'] = array_filter($text, fn ($v) => $v !== null && (string) $v !== '');

        return $data;
    }
}
