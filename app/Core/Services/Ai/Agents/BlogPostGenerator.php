<?php

namespace App\Core\Services\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class BlogPostGenerator implements Agent, HasStructuredOutput
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a blog author. Given a list of existing post titles (and optionally slugs for internal linking) in the prompt, and an optional topic or hint, produce one new blog post that fits the site and does not duplicate existing content. '
            .'Output only HTML for the article body: use headings (h2, h3), paragraphs, lists, and links as needed. Do not embed images or media in the body. '
            .'For internal links to other posts on the site, use the format [[slug:existing-slug]] where existing-slug is one of the slugs provided in the prompt. Include 1–3 such internal links where relevant. '
            .'Also output: a concise title (plain text); a short excerpt (one or two sentences, plain text only—do not use HTML or any markup in the excerpt); and a meta_description (for SEO, plain text only—no HTML). '
            .'Optionally output suggested_tags: an array of 2–5 short tag names (e.g. category or topic labels) that fit the post.';
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
            'suggested_tags' => $schema->array()->items($schema->string())->default([]),
        ];
    }
}
