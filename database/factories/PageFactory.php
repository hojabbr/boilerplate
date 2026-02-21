<?php

namespace Database\Factories;

use App\Domains\Page\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Page\Models\Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'type' => 'custom',
            'title' => ['en' => fake()->sentence(4)],
            'body' => ['en' => $this->longWysiwygBody()],
            'meta_title' => ['en' => fake()->sentence(3)],
            'meta_description' => ['en' => fake()->paragraph(1)],
        ];
    }

    /**
     * Long HTML body resembling WYSIWYG output (paragraphs, headings, lists).
     * Uses short paragraphs and clear block structure so prose renders with proper spacing.
     */
    public static function longWysiwygBody(int $paragraphs = 8): string
    {
        $blocks = [];
        for ($i = 0; $i < $paragraphs; $i++) {
            if ($i > 0 && $i % 3 === 0) {
                $blocks[] = '<h2>'.fake()->sentence(4).'</h2>';
            }
            $blocks[] = '<p>'.fake()->paragraph(2).'</p>';
        }
        $blocks[] = '<h3>'.fake()->sentence(3).'</h3>';
        $blocks[] = '<ul><li>'.implode('</li><li>', fake()->sentences(4)).'</li></ul>';
        $blocks[] = '<p>'.fake()->paragraph(2).'</p>';

        return implode("\n", $blocks);
    }
}
