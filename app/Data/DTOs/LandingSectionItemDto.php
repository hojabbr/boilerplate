<?php

namespace App\Data\DTOs;

readonly class LandingSectionItemDto
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $icon_url = null,
    ) {}

    /**
     * @return array{title: string|null, description: string|null, icon_url: string|null}
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'icon_url' => $this->icon_url,
        ];
    }
}
