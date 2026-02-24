<?php

namespace Database\Factories;

use App\Core\Models\Language;
use App\Domains\Testimonial\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domains\Testimonial\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'language_id' => Language::factory(),
            'quote' => fake()->paragraph(),
            'author' => fake()->name(),
            'role' => fake()->optional(0.8)->jobTitle(),
            'sort_order' => 0,
        ];
    }
}
