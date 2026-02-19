<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'slug' => str()->slug($title).'-'.fake()->unique()->numberBetween(1, 99999),
            'title' => ['en' => $title],
            'excerpt' => ['en' => fake()->paragraph()],
            'body' => ['en' => '<p>'.fake()->paragraphs(3, true).'</p>'],
            'meta_description' => ['en' => fake()->sentence()],
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
