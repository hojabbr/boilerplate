<?php

namespace App\Domains\Blog\Services;

use App\Core\Models\Language;
use App\Core\Services\Ai\Agents\BlogPostGenerator;
use App\Core\Services\Ai\Support\AiProviderOptions;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Blog\Queries\GetLastBlogPostTitles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema as SchemaFacade;
use Illuminate\Support\Str;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\StructuredAgentResponse;

class BlogPostGenerationService
{
    /**
     * Run blog post generation for the given form data. Generates one post (one prompt) in the first
     * selected language, then translates that same content into each other selected language so all
     * rows are the same logical post in different languages.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, BlogPost>
     */
    public function run(array $data): Collection
    {
        $topicSource = $data['topic_source'] ?? 'specific';
        $topic = $data['topic'] ?? '';
        $hint = $data['hint'] ?? '';
        $length = $data['length'] ?? 'medium';
        $providerKey = $data['provider'] ?? config('ai.default');
        $languageIds = $data['language_ids'] ?? [];
        $generateImage = (bool) ($data['generate_image'] ?? false);
        $generateAudio = (bool) ($data['generate_audio'] ?? false);
        $publishImmediately = (bool) ($data['publish_immediately'] ?? false);

        $usePgvector = config('ai.blog.use_pgvector', true)
            && SchemaFacade::getConnection()->getDriverName() === 'pgsql'
            && SchemaFacade::hasTable('blog_post_chunks');

        $agent = new BlogPostGenerator(useSimilaritySearch: $usePgvector);
        $providerOrFailover = $this->resolveProviderForGeneration($providerKey);

        $languages = Language::query()
            ->whereIn('id', $languageIds)
            ->orderBy('sort_order')
            ->get();

        if ($languages->isEmpty()) {
            return new Collection;
        }

        $basePrompt = $this->buildBasePrompt($data, $topicSource, $topic, $hint, $length, $usePgvector);
        /** @var Collection<int, BlogPost> $posts */
        $posts = new Collection;

        // Generate once in the source (first) language.
        $sourceLanguage = $languages->first();
        $sourceInstruction = sprintf(
            ' Write the entire blog post in %s (locale: %s). Output title, excerpt, body, and meta_description in that language.',
            $sourceLanguage->name,
            $sourceLanguage->code
        );
        $response = $agent->prompt($basePrompt.$sourceInstruction, provider: $providerOrFailover, model: null);
        $sourceStructured = $this->structuredArrayFromResponse($response);
        $slug = Str::slug($sourceStructured['title'] ?? 'untitled');

        $sourcePost = $this->createPostFromStructured($sourceLanguage, $slug, $sourceStructured, $publishImmediately);
        $posts->push($sourcePost);

        // Translate the same content into each remaining language.
        $remainingLanguages = $languages->slice(1);
        foreach ($remainingLanguages as $language) {
            $translatedStructured = $this->translateStructuredContent(
                $sourceStructured,
                $language,
                $agent,
                $providerOrFailover
            );
            $post = $this->createPostFromStructured($language, $slug, $translatedStructured, $publishImmediately);
            $posts->push($post);
        }

        $firstPost = $posts->first();
        if ($firstPost instanceof BlogPost && $generateImage && AiProviderOptions::providerSupportsImages($providerKey)) {
            $this->attachGeneratedImageToAllPosts($posts, $firstPost->title, $firstPost->excerpt, $providerKey);
        }
        if ($firstPost instanceof BlogPost && $generateAudio && AiProviderOptions::providerSupportsTts($providerKey)) {
            $this->attachGeneratedAudio($firstPost, $firstPost->excerpt, $providerKey);
        }

        return $posts;
    }

    /**
     * Create a BlogPost from structured content (title, excerpt, body, meta_description).
     *
     * @param  array<string, mixed>  $structured
     */
    private function createPostFromStructured(Language $language, string $slug, array $structured, bool $publishImmediately = false): BlogPost
    {
        $title = $structured['title'] ?? 'Untitled';
        $excerpt = strip_tags($structured['excerpt'] ?? '');
        $body = $structured['body'] ?? '';
        $metaDescription = strip_tags($structured['meta_description'] ?? $excerpt);

        return BlogPost::create([
            'language_id' => $language->id,
            'slug' => $slug,
            'title' => $title,
            'excerpt' => $excerpt,
            'body' => $body,
            'meta_description' => $metaDescription,
            'published_at' => $publishImmediately ? now() : null,
        ]);
    }

    /**
     * Translate structured post content into the target language. Uses the same agent schema;
     * prompt instructs translation only (no new content).
     *
     * @param  array<string, mixed>  $sourceStructured
     * @param  Lab|array<int, Lab>|string  $providerOrFailover
     * @return array<string, mixed>
     */
    private function translateStructuredContent(
        array $sourceStructured,
        Language $targetLanguage,
        BlogPostGenerator $agent,
        Lab|array|string $providerOrFailover
    ): array {
        $prompt = $this->buildTranslationPrompt($sourceStructured, $targetLanguage);
        $response = $agent->prompt($prompt, provider: $providerOrFailover, model: null);

        return $this->structuredArrayFromResponse($response);
    }

    /**
     * Build a prompt that asks to translate existing post content into the target language.
     * Output must match the same schema (title, excerpt, body, meta_description).
     *
     * @param  array<string, mixed>  $sourceStructured
     */
    private function buildTranslationPrompt(array $sourceStructured, Language $targetLanguage): string
    {
        $title = $sourceStructured['title'] ?? '';
        $excerpt = $sourceStructured['excerpt'] ?? '';
        $body = $sourceStructured['body'] ?? '';
        $metaDescription = $sourceStructured['meta_description'] ?? '';

        return sprintf(
            'Translate the following blog post into %s (locale: %s). Do not invent new content—only translate. '
            .'Preserve HTML structure in the body (tags, headings, links). Output the same four fields in the target language: title, excerpt, body, meta_description. '
            ."Output only valid JSON with keys: title, excerpt, body, meta_description.\n\n"
            ."---\nTitle: %s\n\nExcerpt: %s\n\nBody: %s\n\nMeta description: %s\n---",
            $targetLanguage->name,
            $targetLanguage->code,
            $title,
            $excerpt,
            $body,
            $metaDescription
        );
    }

    /**
     * @return Lab|array<int, Lab>|string
     */
    private function resolveProviderForGeneration(string $providerKey): Lab|array|string
    {
        $failover = config('ai.blog.failover_providers', []);
        if ($failover !== [] && $failover !== null) {
            $list = is_array($failover) ? $failover : array_filter(explode(',', (string) $failover));
            $enums = [];
            foreach ($list as $key) {
                $name = is_string($key) ? trim($key) : $key;
                if ($name === '') {
                    continue;
                }
                $enum = Lab::tryFrom($name);
                if ($enum !== null) {
                    $enums[] = $enum;
                }
            }
            if ($enums !== []) {
                return $enums;
            }
        }

        return Lab::tryFrom($providerKey) ?? $providerKey;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function buildBasePrompt(array $data, string $topicSource, string $topic, string $hint, string $length, bool $usePgvector): string
    {
        $lengthInstruction = match ($length) {
            'short' => ' Keep the post short: 2–3 concise paragraphs only. Be direct and scannable.',
            'long' => ' Write a long, in-depth post: multiple sections (use h2/h3), 10+ paragraphs or equivalent. Cover the topic thoroughly with examples or detail where useful.',
            default => ' Write a medium-length post: roughly 4–6 paragraphs, with clear structure (headings if needed).',
        };

        if ($topicSource === 'series') {
            $purpose = $data['series_purpose'] ?? $data['purpose'] ?? '';
            $objective = $data['series_objective'] ?? $data['objective'] ?? '';
            $topics = $data['series_topics'] ?? $data['topics'] ?? '';
            $prompt = 'This post is part of a series. Purpose: '.$purpose.'. Objective: '.$objective.'. Topics: '.$topics.'. Write the next blog post in this series. Output HTML body only (headings, paragraphs, lists, links).'.$lengthInstruction;
        } elseif ($topicSource === 'specific') {
            $prompt = "Write a new blog post on this topic: {$topic}. Output HTML body only (headings, paragraphs, lists, links).{$lengthInstruction}";
        } else {
            $prompt = 'Suggest and write one new blog post that fits this site. '.($hint !== '' ? "Hint: {$hint}. " : '').'Use the similarity search tool to see existing posts and produce something distinct. Output HTML body only.'.$lengthInstruction;
        }

        if (! $usePgvector) {
            $titles = app(GetLastBlogPostTitles::class)->handle(100);
            $titlesList = $titles->map(fn ($r) => $r['title'])->implode("\n");
            $prompt .= "\n\nExisting post titles (do not duplicate):\n{$titlesList}";
        }

        return $prompt;
    }

    /**
     * Extract structured output as array from an agent response (StructuredAgentResponse).
     *
     * @return array<string, mixed>
     */
    private function structuredArrayFromResponse(AgentResponse $response): array
    {
        if ($response instanceof StructuredAgentResponse) {
            return $response->toArray();
        }

        return [];
    }

    /**
     * Generate one featured image and attach it to all posts (all language variants).
     *
     * @param  Collection<int, BlogPost>  $posts
     */
    private function attachGeneratedImageToAllPosts(Collection $posts, string $title, string $excerpt, string $providerKey): void
    {
        try {
            $prompt = $this->buildFeaturedImagePrompt($title, $excerpt);
            $response = \Laravel\Ai\Image::of($prompt)
                ->landscape()
                ->quality('high')
                ->generate($providerKey);
            $imageContent = $response->firstImage()->content();

            foreach ($posts as $post) {
                $tmp = tempnam(sys_get_temp_dir(), 'blog-img-').'.png';
                file_put_contents($tmp, $imageContent);
                $post->addMedia($tmp)->usingFileName('featured-'.Str::random(8).'.png')->toMediaCollection('gallery');
                @unlink($tmp);
            }
        } catch (\Throwable) {
            // Skip image on failure
        }
    }

    /**
     * Build a prompt for photorealistic featured images: real-life photography, no text or whiteboards.
     */
    private function buildFeaturedImagePrompt(string $title, string $excerpt): string
    {
        $subject = Str::limit(strip_tags($excerpt), 120);
        if ($subject === '') {
            $subject = $title;
        }

        return "Professional editorial photograph illustrating this theme: {$title}. "
            ."Scene or subject suggestion: {$subject}. "
            .'Style: photorealistic, real-life photography, shot on a DSLR, natural lighting, shallow depth of field where appropriate. '
            .'Mood: serious or exciting, magazine-quality. '
            .'Do not include any text, words, letters, numbers, whiteboards, charts, diagrams, or written content in the image. '
            .'Show only a real-world scene, people, or objects—no infographics or graphic design elements.';
    }

    private function attachGeneratedAudio(BlogPost $post, string $text, string $providerKey): void
    {
        try {
            $text = Str::limit(strip_tags($text), 1000);
            if ($text === '') {
                return;
            }
            $response = \Laravel\Ai\Audio::of($text)->generate($providerKey);
            $tmp = tempnam(sys_get_temp_dir(), 'blog-tts-').'.mp3';
            file_put_contents($tmp, $response->content());
            $post->addMedia($tmp)->usingFileName('audio-'.Str::random(8).'.mp3')->toMediaCollection('audio');
            @unlink($tmp);
        } catch (\Throwable) {
            // Skip audio on failure
        }
    }
}
