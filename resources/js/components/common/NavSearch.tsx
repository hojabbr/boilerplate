'use client';

import { Link, usePage } from '@inertiajs/react';
import { Search } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';

interface SearchResult {
    id: number;
    title: string;
    slug: string;
    type: 'page' | 'post';
    url: string;
}

interface SearchResponse {
    pages: SearchResult[];
    blog_posts: SearchResult[];
}

export default function NavSearch() {
    const { locale } = usePage().props as { locale?: string };
    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState('');
    const [results, setResults] = useState<SearchResponse | null>(null);
    const [loading, setLoading] = useState(false);

    const prefix = locale ? `/${locale}` : '';
    const searchUrl = `${prefix}/search`;

    const fetchResults = useCallback(
        async (q: string) => {
            if (!q.trim()) {
                setResults(null);
                return;
            }
            setLoading(true);
            let raw: unknown;
            try {
                const res = await fetch(
                    `${searchUrl}?q=${encodeURIComponent(q.trim())}`,
                    { headers: { Accept: 'application/json' } },
                );
                raw = await res.json();
            } catch {
                raw = undefined;
            }
            setLoading(false);
            const data = raw as SearchResponse | undefined;
            const pages = Array.isArray(data?.pages) ? data.pages : [];
            const blog_posts = Array.isArray(data?.blog_posts)
                ? data.blog_posts
                : [];
            setResults({ pages, blog_posts });
        },
        [searchUrl],
    );

    useEffect(() => {
        if (!query.trim()) {
            return;
        }
        const t = setTimeout(() => fetchResults(query), 200);
        return () => clearTimeout(t);
    }, [query, fetchResults]);

    const hasResults =
        results &&
        ((results.pages?.length ?? 0) > 0 ||
            (results.blog_posts?.length ?? 0) > 0);
    const emptyQuery = !query.trim();

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    className="text-muted-foreground hover:text-foreground"
                    aria-label="Search"
                >
                    <Search className="size-4" />
                </Button>
            </PopoverTrigger>
            <PopoverContent
                className="w-80 p-0"
                align="end"
                onOpenAutoFocus={(e) => e.preventDefault()}
            >
                <div className="flex items-center border-b px-2">
                    <Search className="size-4 shrink-0 text-muted-foreground" />
                    <Input
                        placeholder="Search pages and blog..."
                        value={query}
                        onChange={(e) => {
                            const next = e.target.value;
                            setQuery(next);
                            if (!next.trim()) {
                                setResults(null);
                            }
                        }}
                        className="border-0 shadow-none focus-visible:ring-0"
                        autoFocus
                    />
                </div>
                <div className="max-h-72 overflow-auto py-1">
                    {loading && (
                        <p className="px-3 py-4 text-center text-sm text-muted-foreground">
                            Searching...
                        </p>
                    )}
                    {!loading && emptyQuery && (
                        <p className="px-3 py-4 text-center text-sm text-muted-foreground">
                            Type to search
                        </p>
                    )}
                    {!loading && !emptyQuery && !hasResults && (
                        <p className="px-3 py-4 text-center text-sm text-muted-foreground">
                            No results
                        </p>
                    )}
                    {!loading && hasResults && (
                        <>
                            {results!.pages.length > 0 && (
                                <div className="px-2 py-1">
                                    <p className="mb-1 px-2 text-xs font-medium text-muted-foreground">
                                        Pages
                                    </p>
                                    {results!.pages.map((item) => (
                                        <Link
                                            key={`page-${item.id}`}
                                            href={item.url}
                                            className={cn(
                                                'block rounded-sm px-2 py-2 text-sm hover:bg-accent',
                                            )}
                                            onClick={() => setOpen(false)}
                                        >
                                            {item.title}
                                        </Link>
                                    ))}
                                </div>
                            )}
                            {results!.blog_posts.length > 0 && (
                                <div className="px-2 py-1">
                                    <p className="mb-1 px-2 text-xs font-medium text-muted-foreground">
                                        Blog
                                    </p>
                                    {results!.blog_posts.map((item) => (
                                        <Link
                                            key={`post-${item.id}`}
                                            href={item.url}
                                            className={cn(
                                                'block rounded-sm px-2 py-2 text-sm hover:bg-accent',
                                            )}
                                            onClick={() => setOpen(false)}
                                        >
                                            {item.title}
                                        </Link>
                                    ))}
                                </div>
                            )}
                        </>
                    )}
                </div>
            </PopoverContent>
        </Popover>
    );
}
