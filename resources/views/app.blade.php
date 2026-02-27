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

        @php
            // $page['props']['seo'] is set by every public controller; fall back to site-wide values for
            // protected/admin pages that don't pass a seo prop.
            $pageSeo      = $page['props']['seo'] ?? null;
            $seoTitle     = ($pageSeo['title']       ?? null) ?: ($siteName ?? config('app.name', 'Laravel'));
            $seoDesc      = ($pageSeo['description'] ?? null) ?: ($siteTagline ?? null);
            $seoImage     = ($pageSeo['image']       ?? null) ?: ($defaultOgImage ?? null);
            $seoType      = $pageSeo['type'] ?? 'website';
        @endphp
        {{-- SEO: per-page data from Inertia props (works without SSR); @inertiaHead overrides with richer SSR output. --}}
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
        @if($seoDesc)
            <meta name="description" content="{{ $seoDesc }}">
            <meta property="og:description" content="{{ $seoDesc }}">
            <meta name="twitter:description" content="{{ $seoDesc }}">
        @endif
        <meta property="og:type" content="{{ $seoType }}">
        <meta property="og:site_name" content="{{ $siteName ?? config('app.name', 'Laravel') }}">
        <meta property="og:title" content="{{ $seoTitle }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @if($seoImage)
            <meta property="og:image" content="{{ $seoImage }}">
            @if(str_starts_with($seoImage, 'https'))
                <meta property="og:image:secure_url" content="{{ $seoImage }}">
            @endif
            <meta name="twitter:image" content="{{ $seoImage }}">
        @endif
        <meta name="twitter:card" content="{{ $seoImage ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $seoTitle }}">
        @if($twitterHandle ?? null)
            <meta name="twitter:site" content="{{ $twitterHandle }}">
        @endif

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
