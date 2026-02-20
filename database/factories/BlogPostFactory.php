<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     * BlogPost is row-per-locale: language_id + string columns (no Translatable).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'language_id' => Language::factory(),
            'slug' => str()->slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'title' => $title,
            'excerpt' => fake()->paragraph(),
            'body' => '<p>'.fake()->paragraphs(3, true).'</p>',
            'meta_description' => fake()->sentence(),
            'published_at' => now(),
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }
}
