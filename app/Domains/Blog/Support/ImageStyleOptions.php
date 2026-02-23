<?php

namespace App\Domains\Blog\Support;

class ImageStyleOptions
{
    /**
     * All available image styles for blog featured images (key => label).
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'editorial' => 'Editorial (no text)',
            'hero' => 'Hero (headline or text overlay, centered for thumbnails)',
            'demo' => 'Demo / conceptual (UI, screenshots, or labels)',
            'minimal' => 'Minimal (clean, few elements, negative space)',
            'infographic' => 'Infographic (charts, diagrams, data viz; can include labels)',
            'lifestyle' => 'Lifestyle (real people, candid, in context)',
            'abstract' => 'Abstract (conceptual, shapes, gradients, mood)',
        ];
    }

    /**
     * Default style when none selected or for one-time generation.
     */
    public static function default(): string
    {
        return 'editorial';
    }
}
