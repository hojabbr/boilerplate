import { Head, usePage } from '@inertiajs/react';

export interface SeoProps {
    title: string;
    description?: string | null;
    canonical?: string | null;
    image?: string | null;
    noindex?: boolean;
    type?: 'website' | 'article';
}

interface HreflangUrl {
    code: string;
    url: string;
}

interface SharedSeoProps {
    locale?: string;
    canonical_url?: string;
    hreflang_urls?: HreflangUrl[];
    default_locale?: string;
}

export function SeoHead({
    title,
    description,
    canonical,
    image,
    noindex,
    type = 'website',
}: SeoProps) {
    const { props } = usePage();
    const shared = props as SharedSeoProps;
    const canonicalUrl = canonical ?? shared.canonical_url ?? '';
    const hreflangUrls = shared.hreflang_urls ?? [];
    const defaultLocale = shared.default_locale;
    const xDefaultUrl = defaultLocale
        ? hreflangUrls.find((h) => h.code === defaultLocale)?.url
        : undefined;

    return (
        <Head title={title}>
            {description && <meta name="description" content={description} />}
            {canonicalUrl && <link rel="canonical" href={canonicalUrl} />}
            {noindex && <meta name="robots" content="noindex, nofollow" />}
            {/* Open Graph */}
            <meta property="og:title" content={title} />
            {description && (
                <meta property="og:description" content={description} />
            )}
            {canonicalUrl && <meta property="og:url" content={canonicalUrl} />}
            <meta property="og:type" content={type} />
            {image && <meta property="og:image" content={image} />}
            {shared.locale && (
                <meta
                    property="og:locale"
                    content={String(shared.locale).replace('-', '_')}
                />
            )}
            {hreflangUrls
                .filter(({ code }) => code !== shared.locale)
                .map(({ code }) => (
                    <meta
                        key={code}
                        property="og:locale:alternate"
                        content={code.replace('-', '_')}
                    />
                ))}
            {/* Twitter Card */}
            <meta
                name="twitter:card"
                content={image ? 'summary_large_image' : 'summary'}
            />
            <meta name="twitter:title" content={title} />
            {description && (
                <meta name="twitter:description" content={description} />
            )}
            {image && <meta name="twitter:image" content={image} />}
            {/* hreflang */}
            {hreflangUrls.map(({ code, url }) => (
                <link key={code} rel="alternate" href={url} hrefLang={code} />
            ))}
            {xDefaultUrl && (
                <link rel="alternate" href={xDefaultUrl} hrefLang="x-default" />
            )}
        </Head>
    );
}
