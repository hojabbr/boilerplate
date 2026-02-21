import { Link, usePage } from '@inertiajs/react';
import { m } from 'motion/react';
import { fadeInUpView } from '@/components/common/motion-presets';
import { PaginatorLinks } from '@/components/common/PaginatorLinks';
import { SeoHead } from '@/components/common/SeoHead';
import {
    Card,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import PublicLayout, {
    EMPTY_PUBLIC_FEATURES,
    EMPTY_PUBLIC_SETTINGS,
    type PublicFeatures,
    type PublicSettings,
} from '@/layouts/public-layout';
import { home } from '@/routes';
import blog from '@/routes/blog';

interface Post {
    slug: string;
    title: string;
    excerpt: string;
    published_at: string | null;
    thumbnail_url?: string | null;
}

interface PaginatorLinkItem {
    url: string | null;
    label: string;
    active: boolean;
}

interface PostsPaginator {
    data: Post[];
    current_page: number;
    last_page: number;
    per_page: number;
    links: PaginatorLinkItem[];
}

interface Seo {
    title: string;
    description?: string;
}

interface BlogIndexMessages {
    title?: string;
    no_posts?: string;
}

const EMPTY_BLOG_INDEX_MESSAGES: BlogIndexMessages = {};

export default function BlogIndex({
    posts,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
    messages = EMPTY_BLOG_INDEX_MESSAGES,
}: {
    posts: PostsPaginator;
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
    messages?: BlogIndexMessages;
}) {
    const { locale, translations } = usePage().props as {
        locale: string;
        translations?: Record<string, string>;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';
    const items = posts.data;
    const breadcrumbs = [
        {
            title: t['nav.home'] ?? 'Home',
            href: prefix ? prefix : home.url(),
        },
        {
            title: messages.title ?? t['nav.blog'] ?? 'Blog',
            href: `${prefix}${blog.index.url()}`,
        },
    ];

    return (
        <PublicLayout
            breadcrumbs={breadcrumbs}
            settings={settings}
            features={features}
        >
            <SeoHead
                title={seo?.title ?? 'Blog'}
                description={seo?.description}
            />
            <div className="mx-auto max-w-3xl space-y-8">
                <h1 className="mb-6 text-2xl font-semibold text-foreground">
                    {messages.title ?? 'Blog'}
                </h1>
                <ul className="space-y-4">
                    {items.map((post, index) => (
                        <m.li
                            key={post.slug}
                            {...fadeInUpView}
                            transition={{
                                duration: 0.4,
                                ease: 'easeOut' as const,
                                delay: index * 0.05,
                            }}
                        >
                            <Link
                                href={`${prefix}${blog.show.url({ slug: post.slug })}`}
                                className="block transition hover:opacity-90"
                            >
                                <Card className="flex flex-col gap-0 overflow-hidden p-0">
                                    {post.thumbnail_url && (
                                        <img
                                            src={post.thumbnail_url}
                                            alt=""
                                            className="h-56 w-full shrink-0 object-cover"
                                        />
                                    )}
                                    <CardHeader className="py-6">
                                        <CardTitle>{post.title}</CardTitle>
                                        {post.excerpt && (
                                            <CardDescription>
                                                {post.excerpt}
                                            </CardDescription>
                                        )}
                                        {post.published_at && (
                                            <p className="text-xs text-muted-foreground">
                                                {new Date(
                                                    post.published_at,
                                                ).toLocaleDateString()}
                                            </p>
                                        )}
                                    </CardHeader>
                                </Card>
                            </Link>
                        </m.li>
                    ))}
                </ul>
                {items.length === 0 && (
                    <p className="text-muted-foreground">
                        {messages.no_posts ?? 'No posts yet.'}
                    </p>
                )}
                <PaginatorLinks
                    links={posts.links}
                    currentPage={posts.current_page}
                    lastPage={posts.last_page}
                />
            </div>
        </PublicLayout>
    );
}
