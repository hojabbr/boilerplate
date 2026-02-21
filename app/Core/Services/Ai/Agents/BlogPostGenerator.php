<?php

namespace App\Core\Services\Ai\Agents;

use App\Domains\Blog\Models\BlogPostChunk;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Laravel\Ai\Tools\SimilaritySearch;
use Stringable;

class BlogPostGenerator implements Agent, HasStructuredOutput, HasTools
{
    use Promptable;

    public function __construct(
        protected bool $useSimilaritySearch = false,
    ) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a blog author. Given context about existing posts (from the similarity search tool when available, or a list of titles provided in the prompt), and an optional topic or hint, produce one new blog post that fits the site and does not duplicate existing content. '
            .'Output only HTML for the article body: use headings (h2, h3), paragraphs, lists, and links as needed. Do not embed images or media in the body. '
            .'Also output: a concise title (plain text); a short excerpt (one or two sentences, plain text only—do not use HTML or any markup in the excerpt); and a meta_description (for SEO, plain text only—no HTML). '
            .'When you have access to the similarity search tool, use it to find relevant existing posts and ensure your new post is distinct and on-topic.';
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        if (! $this->useSimilaritySearch) {
            return [];
        }

        return [
            SimilaritySearch::usingModel(
                model: BlogPostChunk::class,
                column: 'embedding',
                minSimilarity: 0.4,
                limit: 15,
            )->withDescription('Search existing blog post content to avoid duplicates and match site style.'),
        ];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required(),
            'excerpt' => $schema->string()->required(),
            'body' => $schema->string()->required(),
            'meta_description' => $schema->string()->required(),
        ];
    }
}
