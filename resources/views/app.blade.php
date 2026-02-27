<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        <title inertia>{{ $siteName ?? config('app.name', 'Laravel') }}</title>

        @php
            $setting = \App\Core\Models\Setting::site();
            $icon192 = $setting->getFirstMediaUrl('manifest_icon_192');
            $faviconManaged = $setting->getFirstMediaUrl('favicon') ?: $icon192;
            $faviconIco = $faviconManaged ?: asset('favicon/favicon.ico');
            $appleTouchIcon = $setting->getFirstMediaUrl('apple_touch_icon') ?: ($icon192 ?: asset('favicon/apple-touch-icon.png'));
        @endphp
        <link rel="icon" href="{{ $faviconIco }}" sizes="any">
        @unless($faviconManaged)
            <link rel="icon" href="{{ asset('favicon/favicon.svg') }}" type="image/svg+xml">
        @endunless
        <link rel="apple-touch-icon" href="{{ $appleTouchIcon }}">
        <link rel="manifest" href="{{ route('webmanifest') }}">

        <link rel="preload" href="/fonts/inter-variable.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fonts/vazirmatn-variable.woff2" as="font" type="font/woff2" crossorigin>

        @viteReactRefresh
        @vite(['resources/js/app.tsx'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
