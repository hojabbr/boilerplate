<?php

namespace App\Core\Services\Ai\Support;

class AiProviderOptions
{
    /**
     * Providers that have a non-empty API key configured.
     *
     * @return array<string, string> provider key => label
     */
    public static function availableProviders(): array
    {
        $providers = config('ai.providers', []);
        $labels = [
            'openai' => 'OpenAI',
            'anthropic' => 'Anthropic',
            'gemini' => 'Google Gemini',
            'azure' => 'Azure OpenAI',
            'groq' => 'Groq',
            'xai' => 'xAI',
            'deepseek' => 'DeepSeek',
            'mistral' => 'Mistral',
            'ollama' => 'Ollama',
            'openrouter' => 'OpenRouter',
            'eleven' => 'ElevenLabs',
        ];

        $result = [];
        foreach ($providers as $key => $config) {
            $keyVal = $config['key'] ?? null;
            if ($keyVal !== null && $keyVal !== '') {
                $result[$key] = $labels[$key] ?? ucfirst($key);
            }
        }

        return $result;
    }

    /**
     * Whether the provider supports image generation.
     */
    public static function providerSupportsImages(string $provider): bool
    {
        return in_array($provider, config('ai.provider_capabilities.images', []), true);
    }

    /**
     * Whether the provider supports TTS.
     */
    public static function providerSupportsTts(string $provider): bool
    {
        return in_array($provider, config('ai.provider_capabilities.tts', []), true);
    }
}
