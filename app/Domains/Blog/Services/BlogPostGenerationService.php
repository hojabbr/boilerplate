<?php

namespace App\Domains\Blog\Services;

use App\Core\Models\Language;
use App\Core\Models\Setting;
use App\Core\Services\Ai\Agents\BlogPostGenerator;
use App\Core\Services\Ai\Support\AiProviderOptions;
use App\Domains\Blog\Models\BlogPost;
use App\Domains\Blog\Models\Tag;
use App\Domains\Blog\Queries\GetBlogPostSlugsForLocale;
use App\Domains\Blog\Queries\GetLastBlogPostTitles;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\StructuredAgentResponse;

class BlogPostGenerationService
{
    /**
     * Run blog post generation for the given form data. Generates one post in a single source language
     * (English if selected, otherwise the first selected), then translates that content into every other
     * selected language so all rows are the same logical post in different languages.
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
        $imageStyle = $data['image_style'] ?? 'editorial';
        $publishImmediately = (bool) ($data['publish_immediately'] ?? false);

        $agent = new BlogPostGenerator;
        $providerOrFailover = $this->resolveProviderForGeneration($providerKey);

        $languages = Language::query()
            ->whereIn('id', $languageIds)
            ->orderBy('sort_order')
            ->get();

        if ($languages->isEmpty()) {
            return new Collection;
        }

        // Prefer English as single source for generation; then translate once from that source to every other language.
        $sourceLanguage = $languages->firstWhere('code', 'en') ?? $languages->first();
        $remainingLanguages = $languages->filter(fn ($l) => $l->id !== $sourceLanguage->id)->values();

        /** @var Collection<int, BlogPost> $posts */
        $posts = new Collection;

        $sourceLanguageId = $sourceLanguage->id;
        $basePrompt = $this->buildBasePrompt($data, $topicSource, $topic, $hint, $length, $sourceLanguageId);
        $sourceInstruction = sprintf(
            ' Write the entire blog post in %s (locale: %s). Output title, excerpt, body, and meta_description in that language.',
            $sourceLanguage->name,
            $sourceLanguage->code
        );
        $response = $agent->prompt($basePrompt.$sourceInstruction, provider: $providerOrFailover, model: null);
        $sourceStructured = $this->structuredArrayFromResponse($response);
        $slug = Str::slug($sourceStructured['title'] ?? 'untitled');

        $seriesId = isset($data['series_id']) ? (int) $data['series_id'] : null;
        $sourcePost = $this->createPostFromStructured($sourceLanguage, $slug, $sourceStructured, $publishImmediately, $seriesId);
        $posts->push($sourcePost);

        // Translate from the single source into each other language (no chaining).
        foreach ($remainingLanguages as $language) {
            $translatedStructured = $this->translateStructuredContent(
                $sourceStructured,
                $language,
                $agent,
                $providerOrFailover
            );
            $post = $this->createPostFromStructured($language, $slug, $translatedStructured, $publishImmediately, $seriesId);
            $posts->push($post);
        }

        $this->syncSuggestedTags($posts, $sourceStructured);

        $firstPost = $posts->first();
        if ($firstPost instanceof BlogPost && $generateImage && AiProviderOptions::providerSupportsImages($providerKey)) {
            $this->attachGeneratedImageToAllPosts($posts, $firstPost->title, $firstPost->excerpt, $providerKey, $imageStyle);
        }

        return $posts;
    }

    /**
     * Generate only the source post (one language). Used by the job orchestrator before dispatching per-language translation jobs.
     *
     * @param  array<string, mixed>  $data
     * @return array{post: BlogPost|null, structured: array<string, mixed>}
     */
    public function generateSourceOnly(array $data): array
    {
        $languageIds = $data['language_ids'] ?? [];
        $providerKey = $data['provider'] ?? config('ai.default');
        $publishImmediately = (bool) ($data['publish_immediately'] ?? false);
        $seriesId = isset($data['series_id']) ? (int) $data['series_id'] : null;

        $languages = Language::query()
            ->whereIn('id', $languageIds)
            ->orderBy('sort_order')
            ->get();

        if ($languages->isEmpty()) {
            return ['post' => null, 'structured' => []];
        }

        $sourceLanguage = $languages->firstWhere('code', 'en') ?? $languages->first();
        $agent = new BlogPostGenerator;
        $providerOrFailover = $this->resolveProviderForGeneration($providerKey);

        $topicSource = $data['topic_source'] ?? 'specific';
        $topic = $data['topic'] ?? '';
        $hint = $data['hint'] ?? '';
        $length = $data['length'] ?? 'medium';
        $basePrompt = $this->buildBasePrompt($data, $topicSource, $topic, $hint, $length, $sourceLanguage->id);
        $sourceInstruction = sprintf(
            ' Write the entire blog post in %s (locale: %s). Output title, excerpt, body, and meta_description in that language.',
            $sourceLanguage->name,
            $sourceLanguage->code
        );
        $response = $agent->prompt($basePrompt.$sourceInstruction, provider: $providerOrFailover, model: null);
        $sourceStructured = $this->structuredArrayFromResponse($response);
        $slug = Str::slug($sourceStructured['title'] ?? 'untitled');

        $sourcePost = $this->createPostFromStructured($sourceLanguage, $slug, $sourceStructured, $publishImmediately, $seriesId);

        return ['post' => $sourcePost, 'structured' => $sourceStructured];
    }

    /**
     * Translate the source post into one target language and create the translated post. Used by per-language jobs.
     *
     * @param  array<string, mixed>  $data  Must contain provider, publish_immediately, series_id (optional).
     */
    public function translateAndCreatePost(int $sourcePostId, int $targetLanguageId, array $data): BlogPost
    {
        $sourcePost = BlogPost::find($sourcePostId);
        if (! $sourcePost) {
            throw new \InvalidArgumentException("Source blog post {$sourcePostId} not found.");
        }

        $targetLanguage = Language::find($targetLanguageId);
        if (! $targetLanguage) {
            throw new \InvalidArgumentException("Target language {$targetLanguageId} not found.");
        }

        $sourceStructured = [
            'title' => $sourcePost->title,
            'excerpt' => $sourcePost->excerpt,
            'body' => $sourcePost->body,
            'meta_description' => $sourcePost->meta_description,
        ];

        $providerKey = $data['provider'] ?? config('ai.default');
        $publishImmediately = (bool) ($data['publish_immediately'] ?? false);
        $seriesId = isset($data['series_id']) ? (int) $data['series_id'] : null;

        $agent = new BlogPostGenerator;
        $providerOrFailover = $this->resolveProviderForGeneration($providerKey);
        $translated = $this->translateStructuredContent($sourceStructured, $targetLanguage, $agent, $providerOrFailover);

        return $this->createPostFromStructured($targetLanguage, $sourcePost->slug, $translated, $publishImmediately, $seriesId);
    }

    /**
     * Sync tags and optionally attach featured image to all posts with the same slug as the source. Call after all translation jobs complete.
     *
     * @param  array<string, mixed>  $sourceStructured  Must contain suggested_tags if any.
     * @param  array{generate_image?: bool, image_style?: string, provider?: string}  $options
     */
    public function finalizeGeneration(int $sourcePostId, array $sourceStructured, array $options = []): void
    {
        $sourcePost = BlogPost::find($sourcePostId);
        if (! $sourcePost) {
            return;
        }

        $posts = BlogPost::query()
            ->where('slug', $sourcePost->slug)
            ->get();

        if ($posts->isEmpty()) {
            return;
        }

        $this->syncSuggestedTags($posts, $sourceStructured);

        $generateImage = (bool) ($options['generate_image'] ?? false);
        $imageStyle = $options['image_style'] ?? 'editorial';
        $providerKey = $options['provider'] ?? config('ai.default');

        if ($generateImage && AiProviderOptions::providerSupportsImages($providerKey)) {
            $this->attachGeneratedImageToAllPosts($posts, $sourcePost->title, $sourcePost->excerpt, $providerKey, $imageStyle);
        }
    }

    /**
     * Create a BlogPost from structured content (title, excerpt, body, meta_description).
     *
     * @param  array<string, mixed>  $structured
     */
    private function createPostFromStructured(Language $language, string $slug, array $structured, bool $publishImmediately = false, ?int $seriesId = null): BlogPost
    {
        $title = $structured['title'] ?? 'Untitled';
        $excerpt = strip_tags($structured['excerpt'] ?? '');
        $body = $structured['body'] ?? '';
        $metaDescription = strip_tags($structured['meta_description'] ?? $excerpt);

        $attributes = [
            'language_id' => $language->id,
            'slug' => $slug,
            'title' => $title,
            'excerpt' => $excerpt,
            'body' => $body,
            'meta_description' => $metaDescription,
            'published_at' => $publishImmediately ? now() : null,
        ];
        if ($seriesId !== null) {
            $attributes['blog_post_series_id'] = $seriesId;
        }

        return BlogPost::create($attributes);
    }

    /**
     * Translate structured post content into the target language. Uses chunked body translation
     * when body exceeds config size to stay within provider context limits.
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
        $body = $sourceStructured['body'] ?? '';
        $chunkSize = Setting::translationBodyChunkSize();

        if ($chunkSize <= 0 || Str::length($body) <= $chunkSize) {
            $prompt = $this->buildTranslationPrompt($sourceStructured, $targetLanguage);
            $response = $agent->prompt($prompt, provider: $providerOrFailover, model: null);

            return $this->structuredArrayFromResponse($response);
        }

        $chunks = $this->splitBodyIntoChunks($body, $chunkSize);
        $title = $sourceStructured['title'] ?? '';
        $excerpt = $sourceStructured['excerpt'] ?? '';
        $metaDescription = $sourceStructured['meta_description'] ?? '';

        $firstPrompt = $this->buildTranslationPrompt([
            'title' => $title,
            'excerpt' => $excerpt,
            'body' => $chunks[0],
            'meta_description' => $metaDescription,
        ], $targetLanguage);
        $response = $agent->prompt($firstPrompt, provider: $providerOrFailover, model: null);
        $first = $this->structuredArrayFromResponse($response);
        $translatedBody = $first['body'] ?? '';

        for ($i = 1; $i < count($chunks); $i++) {
            $chunkPrompt = $this->buildTranslationFragmentPrompt($chunks[$i], $targetLanguage);
            $chunkResponse = $agent->prompt($chunkPrompt, provider: $providerOrFailover, model: null);
            $chunkStructured = $this->structuredArrayFromResponse($chunkResponse);
            $translatedBody .= $chunkStructured['body'] ?? '';
        }

        return [
            'title' => $first['title'] ?? $title,
            'excerpt' => $first['excerpt'] ?? $excerpt,
            'body' => $translatedBody,
            'meta_description' => $first['meta_description'] ?? $metaDescription,
        ];
    }

    /**
     * Split HTML body into chunks of at most $maxChars, breaking on block boundaries (e.g. </p>, </h2>).
     *
     * @return list<string>
     */
    private function splitBodyIntoChunks(string $body, int $maxChars): array
    {
        if ($body === '' || $maxChars <= 0) {
            return [$body];
        }

        $chunks = [];
        $remaining = $body;
        $boundaryPattern = '/<\/(?:p|h[2-6]|li|div|section|article)\s*>/iu';

        while ($remaining !== '') {
            if (Str::length($remaining) <= $maxChars) {
                $chunks[] = $remaining;
                break;
            }

            $segment = Str::limit($remaining, $maxChars, '');
            $lastBoundary = 0;
            if (preg_match_all($boundaryPattern, $segment, $m, PREG_OFFSET_CAPTURE)) {
                $lastMatch = end($m[0]);
                if ($lastMatch !== false) {
                    $lastBoundary = $lastMatch[1] + Str::length($lastMatch[0]);
                }
            }
            if ($lastBoundary > 0) {
                $chunk = Str::substr($remaining, 0, $lastBoundary);
                $remaining = Str::substr($remaining, $lastBoundary);
            } else {
                $chunk = $segment;
                $remaining = Str::substr($remaining, Str::length($segment));
            }
            $chunks[] = $chunk;
        }

        return $chunks;
    }

    /**
     * Build a prompt that asks to translate only an HTML fragment (output schema: title, excerpt, body, meta_description; only body used).
     */
    private function buildTranslationFragmentPrompt(string $htmlFragment, Language $targetLanguage): string
    {
        return sprintf(
            'Translate the following HTML fragment into %s (locale: %s). Do not invent content—only translate. '
            .'Preserve HTML tags and structure. Preserve every [[slug:...]] placeholder exactly. '
            .'Output valid JSON with keys: title, excerpt, body, meta_description. Set title, excerpt, and meta_description to empty string. Put the translated HTML fragment in body. '
            ."Fragment:\n---\n%s\n---",
            $targetLanguage->name,
            $targetLanguage->code,
            $htmlFragment
        );
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
            .'Preserve HTML structure in the body (tags, headings, links). '
            .'Preserve every [[slug:...]] placeholder exactly; do not translate or modify the slug part inside the brackets. '
            .'Output the same four fields in the target language: title, excerpt, body, meta_description. '
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
        $list = is_array($failover) ? $failover : array_filter(explode(',', (string) $failover));
        $enums = [];
        foreach ($list as $key) {
            $name = is_string($key) ? trim($key) : (string) $key;
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

        return Lab::tryFrom($providerKey) ?? $providerKey;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function buildBasePrompt(array $data, string $topicSource, string $topic, string $hint, string $length, ?int $sourceLanguageId): string
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
            $prompt = 'Suggest and write one new blog post that fits this site. '.($hint !== '' ? "Hint: {$hint}. " : '').'Use the list of existing posts below to produce something distinct. Output HTML body only.'.$lengthInstruction;
        }

        $titles = app(GetLastBlogPostTitles::class)->handle(100);
        $titlesList = $titles->map(fn ($r) => $r['title'])->implode("\n");
        $prompt .= "\n\nExisting post titles (do not duplicate):\n{$titlesList}";

        if ($sourceLanguageId !== null) {
            $slugsForLocale = app(GetBlogPostSlugsForLocale::class)->handle($sourceLanguageId, 80);
            if ($slugsForLocale->isNotEmpty()) {
                $slugList = $slugsForLocale->map(fn ($r) => $r['title'].' ('.$r['slug'].')')->implode("\n");
                $prompt .= "\n\nExisting posts in this language (for internal linking, use only these slugs):\n{$slugList}";
                $prompt .= "\n\nInclude 1–3 internal links in the body using the format [[slug:existing-slug]] where existing-slug is one of the slugs above.";
            }
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
    private function attachGeneratedImageToAllPosts(Collection $posts, string $title, string $excerpt, string $providerKey, string $imageStyle = 'editorial'): void
    {
        try {
            $prompt = $this->buildFeaturedImagePrompt($title, $excerpt, $imageStyle);
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
     * Style modifiers for image variety (deterministic from title+excerpt so same post gets same modifier).
     *
     * @var list<string>
     */
    private const IMAGE_STYLE_MODIFIERS = [
        'minimalist composition, clean negative space',
        'dramatic lighting with strong shadows',
        'warm tones, golden hour feel',
        'cool tones, soft overcast mood',
        'wide angle perspective, expansive',
        'close-up detail, shallow depth of field',
        'Avoid generic stock-photo composition; aim for a distinct, specific scene or angle.',
    ];

    /**
     * Build a prompt for featured images. Branches on image_style (editorial/hero/demo) and adds a style modifier for variety.
     */
    private function buildFeaturedImagePrompt(string $title, string $excerpt, string $imageStyle = 'editorial'): string
    {
        $subject = Str::limit(strip_tags($excerpt), 120);
        if ($subject === '') {
            $subject = $title;
        }

        $modifiers = self::IMAGE_STYLE_MODIFIERS;
        $index = abs(crc32($title."\n".$excerpt)) % count($modifiers);
        $styleModifier = $modifiers[$index];

        $base = "Illustrating this theme: {$title}. Scene or subject suggestion: {$subject}. ";

        $style = match ($imageStyle) {
            'hero' => 'Hero-style image, dramatic and impactful. If including a text overlay or headline, place it centered in the middle of the image so it remains clearly visible when cropped to a wide thumbnail (e.g. 16:10 card crop). Use bold, high-contrast text. '
                .'Professional quality, high contrast, magazine-cover feel. '
                ."Style modifier: {$styleModifier}",
            'demo' => 'Demo or conceptual image. If including text or labels, place them centered in the frame so they stay visible when the image is cropped to a wide thumbnail. '
                .'Clear and informative, suitable for tutorials or product content. '
                ."Style modifier: {$styleModifier}",
            'minimal' => 'Minimalist image: clean composition, ample negative space, few elements. No clutter. '
                .'Do not include text or letters. Calm, refined, modern. '
                ."Style modifier: {$styleModifier}",
            'infographic' => 'Infographic or data-visualization style. May include charts, diagrams, icons, or short labels; place any text centered so it stays visible when cropped to a wide thumbnail. '
                .'Clear, professional, informative. '
                ."Style modifier: {$styleModifier}",
            'lifestyle' => 'Lifestyle photograph: real people in authentic context, candid or natural. '
                .'Do not include text or letters. Warm, relatable, story-driven. '
                ."Style modifier: {$styleModifier}",
            'abstract' => 'Abstract or conceptual image: shapes, gradients, mood, metaphor. No literal text or words. '
                .'Evocative, modern, can suggest the theme without depicting it literally. '
                ."Style modifier: {$styleModifier}",
            default => 'Professional editorial photograph. Style: photorealistic, real-life photography, shot on a DSLR, natural lighting, shallow depth of field where appropriate. '
                .'Mood: serious or exciting, magazine-quality. '
                .'Do not include any text, words, letters, numbers, whiteboards, charts, diagrams, or written content in the image. '
                .'Show only a real-world scene, people, or objects—no infographics or graphic design elements. '
                ."Style modifier: {$styleModifier}",
        };

        return $base.$style;
    }

    /**
     * Sync suggested_tags from structured output to all posts (find or create tags, attach).
     *
     * @param  Collection<int, BlogPost>  $posts
     * @param  array<string, mixed>  $sourceStructured
     */
    private function syncSuggestedTags(Collection $posts, array $sourceStructured): void
    {
        $names = $sourceStructured['suggested_tags'] ?? [];
        if (! is_array($names) || $names === []) {
            return;
        }

        $tagIds = [];
        foreach ($names as $name) {
            if (! is_string($name) || trim($name) === '') {
                continue;
            }
            $tag = Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => trim($name)]
            );
            $tagIds[] = $tag->id;
        }

        if ($tagIds !== []) {
            foreach ($posts as $post) {
                $post->tags()->sync($tagIds);
            }
        }
    }
}
