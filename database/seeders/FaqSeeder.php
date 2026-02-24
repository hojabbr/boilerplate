<?php

namespace Database\Seeders;

use App\Domains\Faq\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Default FAQ pairs (question, answer) used for each language when seeding.
     *
     * @var array<int, array{question: string, answer: string}>
     */
    protected array $defaults = [
        [
            'question' => 'How do I get started?',
            'answer' => 'You can sign up for an account using the Get started button. Once registered, you will have access to your dashboard and all features.',
        ],
        [
            'question' => 'What payment methods do you accept?',
            'answer' => 'We accept all major credit cards, PayPal, and bank transfers. Payment is processed securely through our partner.',
        ],
        [
            'question' => 'Can I cancel my subscription at any time?',
            'answer' => 'Yes. You can cancel your subscription from your account settings at any time. You will retain access until the end of your billing period.',
        ],
        [
            'question' => 'How can I contact support?',
            'answer' => 'Use the Contact page to send us a message, or email us directly. We typically respond within one business day.',
        ],
        [
            'question' => 'Do you offer a free trial?',
            'answer' => 'Yes. New accounts can start with a 14-day free trial. No credit card is required to begin.',
        ],
    ];

    public function run(): void
    {
        $languages = $this->languages();

        foreach ($languages as $language) {
            foreach ($this->defaults as $sortOrder => $item) {
                Faq::updateOrCreate(
                    [
                        'language_id' => $language->id,
                        'question' => $item['question'],
                    ],
                    [
                        'answer' => $item['answer'],
                        'sort_order' => $sortOrder,
                    ]
                );
            }
        }
    }

    use SeedsByLocale;
}
