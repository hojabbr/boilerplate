@props([
    'missingByLocale' => [],
    'localeNames' => [],
    'fillMissingUrl' => '',
])

@if (count($missingByLocale) > 0)
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem;">
        @foreach ($missingByLocale as $locale => $count)
            @php
                $name = $localeNames[$locale] ?? $locale;
            @endphp
            <x-filament::button
                tag="a"
                :href="$fillMissingUrl . '?locale=' . urlencode($locale)"
                color="primary"
                size="sm"
            >
                {{ $name }}: {{ $count }}
            </x-filament::button>
        @endforeach
    </div>
@endif
