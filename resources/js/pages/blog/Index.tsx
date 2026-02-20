import { Link, usePage } from '@inertiajs/react';
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
import blog from '@/routes/blog';

interface Post {
    slug: string;
    title: string;
    excerpt: string;
    published_at: string | null;
    thumbnail_url?: string | null;
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
    posts: Post[];
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
    messages?: BlogIndexMessages;
}) {
    const { locale } = usePage().props as { locale: string };
    const prefix = locale ? `/${locale}` : '';

    return (
        <PublicLayout settings={settings} features={features}>
            <SeoHead
                title={seo?.title ?? 'Blog'}
                description={seo?.description}
            />
            <div className="mx-auto max-w-3xl">
                <h1 className="mb-6 text-2xl font-semibold text-foreground">
                    {messages.title ?? 'Blog'}
                </h1>
                <ul className="space-y-4">
                    {posts.map((post) => (
                        <li key={post.slug}>
                            <Link
                                href={`${prefix}${blog.show.url({ slug: post.slug })}`}
                                className="block transition hover:opacity-90"
                            >
                                <Card className="overflow-hidden">
                                    {post.thumbnail_url && (
                                        <img
                                            src={post.thumbnail_url}
                                            alt=""
                                            className="h-40 w-full object-cover"
                                        />
                                    )}
                                    <CardHeader>
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
                        </li>
                    ))}
                </ul>
                {posts.length === 0 && (
                    <p className="text-muted-foreground">
                        {messages.no_posts ?? 'No posts yet.'}
                    </p>
                )}
            </div>
        </PublicLayout>
    );
}
