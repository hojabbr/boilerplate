import type { InertiaLinkProps } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(url: NonNullable<InertiaLinkProps['href']>): string {
    return typeof url === 'string' ? url : url.url;
}

const HTML_ENTITIES: Record<string, string> = {
    '&lt;': '<',
    '&gt;': '>',
    '&amp;': '&',
    '&quot;': '"',
    '&#39;': "'",
    '&#x27;': "'",
};

/**
 * Decode HTML entities so content stored or sent escaped still renders as HTML.
 * Safe for already-raw HTML (returns as-is). Use before dangerouslySetInnerHTML.
 * Works in both SSR and browser.
 */
export function decodeHtml(html: string): string {
    if (typeof html !== 'string' || html === '') {
        return html;
    }
    return html.replace(
        /&(?:lt|gt|amp|quot|#39|#x27);/g,
        (match) => HTML_ENTITIES[match] ?? match,
    );
}
