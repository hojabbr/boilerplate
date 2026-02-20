<?php

namespace App\Data\DTOs;

use App\Models\LandingSection;

readonly class LandingSectionDto
{
    /**
     * @param  array<int, array{title: string|null, description: string|null, icon_url: string|null}>  $items
     */
    public function __construct(
        public string $type,
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $body = null,
        public ?string $cta_text = null,
        public ?string $cta_url = null,
        public ?string $image_url = null,
        public array $items = [],
    ) {}

    public static function fromModel(LandingSection $section): self
    {
        $media = $section->getFirstMedia('image');
        $imageUrl = $media ? $media->getUrl('full') : null;

        $items = [];
        foreach ($section->items as $item) {
            $itemMedia = $item->getFirstMedia('icon');
            $items[] = (new LandingSectionItemDto(
                title: $item->title,
                description: $item->description,
                icon_url: $itemMedia ? $itemMedia->getUrl('card') : null,
            ))->toArray();
        }

        return new self(
            type: $section->type,
            title: $section->title,
            subtitle: $section->subtitle,
            body: $section->body,
            cta_text: $section->cta_text,
            cta_url: $section->cta_url,
            image_url: $imageUrl,
            items: $items,
        );
    }

    /**
     * @return array{type: string, title: string|null, subtitle: string|null, body: string|null, cta_text: string|null, cta_url: string|null, image_url: string|null, items: array<int, array{title: string|null, description: string|null, icon_url: string|null}>}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'body' => $this->body,
            'cta_text' => $this->cta_text,
            'cta_url' => $this->cta_url,
            'image_url' => $this->image_url,
            'items' => $this->items,
        ];
    }
}
