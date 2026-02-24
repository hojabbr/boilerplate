<?php

namespace Database\Seeders;

use App\Core\Models\Language;
use App\Domains\Testimonial\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Default testimonials (quote, author, role) used for each language when seeding.
     *
     * @var array<int, array{quote: string, author: string, role: string|null}>
     */
    protected array $defaults = [
        [
            'quote' => 'This product has completely changed how we work. We are more productive and our team is happier.',
            'author' => 'Jane Smith',
            'role' => 'Head of Operations, Acme Inc.',
        ],
        [
            'quote' => 'Outstanding support and a platform that just works. We recommend it to everyone in our industry.',
            'author' => 'Michael Chen',
            'role' => 'CTO, TechStart',
        ],
        [
            'quote' => 'Simple to use, powerful under the hood. Exactly what we were looking for.',
            'author' => 'Sarah Williams',
            'role' => 'Founder, Growth Labs',
        ],
    ];

    public function run(): void
    {
        $languages = $this->languages();

        foreach ($languages as $language) {
            foreach ($this->defaults as $sortOrder => $item) {
                Testimonial::updateOrCreate(
                    [
                        'language_id' => $language->id,
                        'author' => $item['author'],
                        'quote' => $item['quote'],
                    ],
                    [
                        'role' => $item['role'],
                        'sort_order' => $sortOrder,
                    ]
                );
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Language>
     */
    protected function languages()
    {
        $query = Language::query()->orderBy('sort_order');

        if (\Illuminate\Support\Facades\Schema::hasColumn((new Language)->getTable(), 'is_enabled')) {
            $query->where('is_enabled', true);
        }

        return $query->get();
    }
}
