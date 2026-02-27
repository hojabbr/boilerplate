import { Head, usePage } from '@inertiajs/react';

export interface SeoProps {
    title: string;
    description?: string | null;
    canonical?: string | null;
    image?: string | null;
    imageAlt?: string | null;
    noindex?: boolean;
    type?: 'website' | 'article';
    /** ISO 8601 date string — article:published_time */
    publishedAt?: string | null;
    /** ISO 8601 date string — article:modified_time */
    modifiedAt?: string | null;
    /** author:name / article:author */
    authorName?: string | null;
    /** article:tag entries */
    tags?: string[];
    /** Schema.org JSON-LD object(s) rendered as <script type="application/ld+json"> */
    jsonLd?: Record<string, unknown> | Record<string, unknown>[];
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
    name?: string;
    default_og_image?: string | null;
    twitter_handle?: string | null;
}

export function SeoHead({
    title,
    description,
    canonical,
    image,
    imageAlt,
    noindex = false,
    type = 'website',
    publishedAt,
    modifiedAt,
    authorName,
    tags,
    jsonLd,
}: SeoProps) {
    const { props } = usePage();
    const shared = props as SharedSeoProps;
    const siteName = shared.name;
    const canonicalUrl = canonical ?? shared.canonical_url ?? '';
    const hreflangUrls = shared.hreflang_urls ?? [];
    const defaultLocale = shared.default_locale;
    const xDefaultUrl = defaultLocale
        ? hreflangUrls.find((h) => h.code === defaultLocale)?.url
        : undefined;

    const resolvedImage = image ?? shared.default_og_image ?? null;
    const resolvedImageAlt = imageAlt ?? title;
    const twitterHandle = shared.twitter_handle ?? null;
    const isArticle = type === 'article';

    const jsonLdSchemas: Record<string, unknown>[] = jsonLd
        ? Array.isArray(jsonLd)
            ? jsonLd
            : [jsonLd]
        : [];

    return (
        <Head title={title}>
            {/* Core */}
            {description && <meta name="description" content={description} />}
            {canonicalUrl && <link rel="canonical" href={canonicalUrl} />}
            <meta
                name="robots"
                content={
                    noindex
                        ? 'noindex, nofollow'
                        : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'
                }
            />

            {/* Open Graph — base */}
            <meta property="og:title" content={title} />
            {siteName && <meta property="og:site_name" content={siteName} />}
            {description && (
                <meta property="og:description" content={description} />
            )}
            {canonicalUrl && <meta property="og:url" content={canonicalUrl} />}
            <meta property="og:type" content={type} />
            {resolvedImage && (
                <meta property="og:image" content={resolvedImage} />
            )}
            {resolvedImage && resolvedImage.startsWith('https') && (
                <meta property="og:image:secure_url" content={resolvedImage} />
            )}
            {resolvedImage && (
                <meta property="og:image:alt" content={resolvedImageAlt} />
            )}
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

            {/* Open Graph — article */}
            {isArticle && publishedAt && (
                <meta property="article:published_time" content={publishedAt} />
            )}
            {isArticle && modifiedAt && (
                <meta property="article:modified_time" content={modifiedAt} />
            )}
            {isArticle && authorName && (
                <meta property="article:author" content={authorName} />
            )}
            {isArticle &&
                tags?.map((tag) => (
                    <meta key={tag} property="article:tag" content={tag} />
                ))}

            {/* Twitter Card */}
            <meta
                name="twitter:card"
                content={resolvedImage ? 'summary_large_image' : 'summary'}
            />
            {twitterHandle && (
                <meta name="twitter:site" content={twitterHandle} />
            )}
            {twitterHandle && isArticle && (
                <meta name="twitter:creator" content={twitterHandle} />
            )}
            <meta name="twitter:title" content={title} />
            {description && (
                <meta name="twitter:description" content={description} />
            )}
            {resolvedImage && (
                <meta name="twitter:image" content={resolvedImage} />
            )}
            {resolvedImage && (
                <meta name="twitter:image:alt" content={resolvedImageAlt} />
            )}

            {/* hreflang */}
            {hreflangUrls.map(({ code, url }) => (
                <link key={code} rel="alternate" href={url} hrefLang={code} />
            ))}
            {xDefaultUrl && (
                <link rel="alternate" href={xDefaultUrl} hrefLang="x-default" />
            )}

            {/* JSON-LD structured data */}
            {jsonLdSchemas.map((schema) => (
                <script
                    key={String(
                        schema['@type'] ??
                            schema['@id'] ??
                            JSON.stringify(schema),
                    )}
                    type="application/ld+json"
                    dangerouslySetInnerHTML={{
                        __html: JSON.stringify(schema),
                    }}
                />
            ))}
        </Head>
    );
}
