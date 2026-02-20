import { Link, usePage } from '@inertiajs/react';
import { m } from 'motion/react';
import { fadeInUp, fadeInUpView } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import { Button } from '@/components/ui/button';
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
import { register } from '@/routes';
import blog from '@/routes/blog';
import contact from '@/routes/contact';
import page from '@/routes/page';

interface Seo {
    title: string;
    description?: string;
}

interface WelcomeMessages {
    heading?: string;
    tagline_fallback?: string;
    cta_get_started?: string;
    cta_contact_us?: string;
    explore?: string;
    about_us_title?: string;
    about_us_description?: string;
    blog_title?: string;
    blog_description?: string;
    contact_title?: string;
    contact_description?: string;
}

interface SectionItem {
    title?: string | null;
    description?: string | null;
    icon_url?: string | null;
}

interface Section {
    type: string;
    title?: string | null;
    subtitle?: string | null;
    body?: string | null;
    cta_text?: string | null;
    cta_url?: string | null;
    image_url?: string | null;
    items?: SectionItem[];
}

interface LatestPost {
    slug: string;
    title: string;
    excerpt: string;
    published_at: string | null;
    thumbnail_url?: string | null;
}

interface WelcomeProps {
    canRegister?: boolean;
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
    messages?: WelcomeMessages;
    sections?: Section[];
    latest_posts?: LatestPost[];
}

const EMPTY_WELCOME_MESSAGES: WelcomeMessages = {};
const EMPTY_SECTIONS: Section[] = [];

export default function Welcome({
    canRegister = true,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
    messages = EMPTY_WELCOME_MESSAGES,
    sections = EMPTY_SECTIONS,
    latest_posts = [],
}: WelcomeProps) {
    const { locale, nav_pages = [] } = usePage().props as {
        locale: string;
        nav_pages?: Array<{ slug: string; title: string }>;
    };
    const prefix = locale ? `/${locale}` : '';
    const showPages = features.pages ?? false;
    const showBlog = features.blog ?? false;
    const showContact = features.contactForm ?? false;
    const tagline =
        settings.tagline ||
        (messages.tagline_fallback ?? 'Build something great.');
    const companyName = settings.company_name ?? 'App';

    const heroSection = sections.find((s) => s.type === 'hero');
    const featuresSections = sections.filter((s) => s.type === 'features');
    const testimonialsSections = sections.filter(
        (s) => s.type === 'testimonials',
    );
    const latestPostsSections = sections.filter(
        (s) => s.type === 'latest_posts',
    );
    const ctaSections = sections.filter((s) => s.type === 'cta');

    const heroHeading =
        heroSection?.title ?? messages.heading ?? `Welcome to ${companyName}`;
    const heroSubtitle = heroSection?.subtitle ?? tagline;
    const heroPrimaryCta =
        heroSection?.cta_text ?? messages.cta_get_started ?? 'Get started';
    const heroSecondaryCta = messages.cta_contact_us ?? 'Contact us';
    const heroPrimaryUrl = heroSection?.cta_url
        ? heroSection.cta_url.startsWith('http')
            ? heroSection.cta_url
            : `${prefix}${heroSection.cta_url}`
        : `${prefix}${register.url()}`;

    return (
        <PublicLayout
            settings={settings}
            features={features}
            canRegister={canRegister}
        >
            <SeoHead
                title={seo?.title ?? 'Welcome'}
                description={seo?.description}
            />
            <article className="flex flex-col gap-12 py-8 sm:py-12 lg:gap-16 lg:py-16">
                <section
                    className={
                        heroSection?.image_url
                            ? 'relative mx-auto w-full max-w-5xl overflow-hidden rounded-xl bg-muted/50'
                            : 'mx-auto w-full max-w-3xl text-center'
                    }
                >
                    {heroSection?.image_url && (
                        <div className="absolute inset-0 flex items-center justify-center">
                            <img
                                src={heroSection.image_url}
                                alt=""
                                className="h-full w-full object-cover opacity-30"
                            />
                        </div>
                    )}
                    <m.div
                        className={
                            heroSection?.image_url
                                ? 'relative mx-auto max-w-3xl px-6 py-12 text-center sm:py-16'
                                : ''
                        }
                        {...fadeInUp}
                    >
                        <h1 className="text-3xl font-semibold tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                            {heroHeading}
                        </h1>
                        <p className="mt-4 text-lg text-muted-foreground sm:text-xl">
                            {heroSubtitle}
                        </p>
                        <div className="mt-8 flex flex-wrap items-center justify-center gap-4">
                            {canRegister && (
                                <m.span
                                    whileHover={{ scale: 1.02 }}
                                    whileTap={{ scale: 0.98 }}
                                    transition={{ duration: 0.2 }}
                                >
                                    <Button size="lg" asChild>
                                        <Link href={heroPrimaryUrl}>
                                            {heroPrimaryCta}
                                        </Link>
                                    </Button>
                                </m.span>
                            )}
                            {showContact && (
                                <m.span
                                    whileHover={{ scale: 1.02 }}
                                    whileTap={{ scale: 0.98 }}
                                    transition={{ duration: 0.2 }}
                                >
                                    <Button variant="outline" size="lg" asChild>
                                        <Link
                                            href={`${prefix}${contact.show.url()}`}
                                        >
                                            {heroSecondaryCta}
                                        </Link>
                                    </Button>
                                </m.span>
                            )}
                        </div>
                    </m.div>
                </section>

                {featuresSections.map((sec, i) => (
                    <section
                        key={`features-${i}`}
                        className="border-t border-border pt-12 lg:pt-16"
                    >
                        {sec.title && (
                            <h2 className="mb-2 text-center text-2xl font-semibold text-foreground">
                                {sec.title}
                            </h2>
                        )}
                        {sec.subtitle && (
                            <p className="mx-auto mb-8 max-w-2xl text-center text-muted-foreground">
                                {sec.subtitle}
                            </p>
                        )}
                        <div className="mx-auto grid max-w-5xl gap-8 sm:grid-cols-2 lg:grid-cols-3">
                            {(sec.items ?? []).map((item, j) => (
                                <m.div
                                    key={j}
                                    {...fadeInUpView}
                                    transition={{
                                        duration: 0.4,
                                        ease: 'easeOut' as const,
                                        delay: j * 0.06,
                                    }}
                                >
                                    <Card className="flex h-full flex-col gap-0 overflow-hidden p-0">
                                        {item.icon_url && (
                                            <img
                                                src={item.icon_url}
                                                alt=""
                                                className="h-40 w-full shrink-0 object-cover"
                                            />
                                        )}
                                        <CardHeader className="py-6">
                                            {item.title && (
                                                <CardTitle>
                                                    {item.title}
                                                </CardTitle>
                                            )}
                                            {item.description && (
                                                <CardDescription>
                                                    {item.description}
                                                </CardDescription>
                                            )}
                                        </CardHeader>
                                    </Card>
                                </m.div>
                            ))}
                        </div>
                    </section>
                ))}

                {testimonialsSections.map((sec, i) => (
                    <section
                        key={`testimonials-${i}`}
                        className="border-t border-border pt-12 lg:pt-16"
                    >
                        {sec.title && (
                            <h2 className="mb-8 text-center text-2xl font-semibold text-foreground">
                                {sec.title}
                            </h2>
                        )}
                        <div className="mx-auto grid max-w-4xl gap-6 sm:grid-cols-2">
                            {(sec.items ?? []).map((item, j) => (
                                <blockquote
                                    key={j}
                                    className="rounded-lg border border-border bg-muted/30 p-6"
                                >
                                    {item.description && (
                                        <p className="text-foreground">
                                            {item.description}
                                        </p>
                                    )}
                                    {item.title && (
                                        <footer className="mt-3 text-sm text-muted-foreground">
                                            — {item.title}
                                        </footer>
                                    )}
                                </blockquote>
                            ))}
                        </div>
                    </section>
                ))}

                {latestPostsSections.map((sec, i) => (
                    <section
                        key={`latest_posts-${i}`}
                        className="border-t border-border pt-12 lg:pt-16"
                    >
                        {sec.title && (
                            <h2 className="mb-2 text-center text-2xl font-semibold text-foreground">
                                {sec.title}
                            </h2>
                        )}
                        {sec.subtitle && (
                            <p className="mx-auto mb-8 max-w-2xl text-center text-muted-foreground">
                                {sec.subtitle}
                            </p>
                        )}
                        {latest_posts.length > 0 && (
                            <div className="mx-auto grid max-w-5xl gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                {latest_posts.map((post) => (
                                    <Link
                                        key={post.slug}
                                        href={`${prefix}${blog.show.url({ slug: post.slug })}`}
                                        className="block transition hover:opacity-90"
                                    >
                                        <Card className="flex h-full flex-col gap-0 overflow-hidden p-0">
                                            {post.thumbnail_url && (
                                                <img
                                                    src={post.thumbnail_url}
                                                    alt=""
                                                    className="h-44 w-full shrink-0 object-cover"
                                                />
                                            )}
                                            <CardHeader className="py-6">
                                                <CardTitle className="line-clamp-2">
                                                    {post.title}
                                                </CardTitle>
                                                {post.excerpt && (
                                                    <CardDescription className="line-clamp-2">
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
                                ))}
                            </div>
                        )}
                        {latest_posts.length > 0 && showBlog && (
                            <p className="mt-6 text-center">
                                <Button variant="outline" asChild>
                                    <Link href={`${prefix}${blog.index.url()}`}>
                                        {messages.blog_title ?? 'Blog'}
                                    </Link>
                                </Button>
                            </p>
                        )}
                    </section>
                ))}

                {ctaSections.map((sec, i) => (
                    <section
                        key={`cta-${i}`}
                        className="border-t border-border pt-12 lg:pt-16"
                    >
                        <div
                            className={
                                sec.image_url
                                    ? 'relative mx-auto flex max-w-4xl flex-col overflow-hidden rounded-xl bg-muted/50 sm:flex-row'
                                    : 'mx-auto max-w-2xl rounded-xl border border-border bg-muted/30 px-6 py-10 text-center'
                            }
                        >
                            {sec.image_url && (
                                <div className="relative h-48 shrink-0 sm:h-auto sm:w-1/3">
                                    <img
                                        src={sec.image_url}
                                        alt=""
                                        className="h-full w-full object-cover"
                                    />
                                </div>
                            )}
                            <div
                                className={
                                    sec.image_url
                                        ? 'relative flex flex-1 flex-col justify-center px-6 py-8 text-center sm:py-12 sm:text-start'
                                        : ''
                                }
                            >
                                {sec.title && (
                                    <h2 className="text-xl font-semibold text-foreground sm:text-2xl">
                                        {sec.title}
                                    </h2>
                                )}
                                {sec.subtitle && (
                                    <p className="mt-2 text-muted-foreground">
                                        {sec.subtitle}
                                    </p>
                                )}
                                {sec.cta_text && sec.cta_url && (
                                    <div className="mt-4">
                                        <Button asChild>
                                            <Link
                                                href={
                                                    sec.cta_url.startsWith(
                                                        'http',
                                                    )
                                                        ? sec.cta_url
                                                        : `${prefix}${sec.cta_url}`
                                                }
                                            >
                                                {sec.cta_text}
                                            </Link>
                                        </Button>
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>
                ))}

                {(showPages || showBlog || showContact) && (
                    <section className="border-t border-border pt-12 lg:pt-16">
                        <h2 className="mb-6 text-center text-sm font-medium tracking-wider text-muted-foreground uppercase">
                            {messages.explore ?? 'Explore'}
                        </h2>
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {(
                                nav_pages as Array<{
                                    slug: string;
                                    title: string;
                                }>
                            ).map((p) => (
                                <m.div key={p.slug} {...fadeInUpView}>
                                    <Link
                                        href={`${prefix}${page.show.url({ slug: p.slug })}`}
                                        className="block transition hover:opacity-90"
                                    >
                                        <Card className="h-full">
                                            <CardHeader>
                                                <CardTitle>{p.title}</CardTitle>
                                                <CardDescription>
                                                    {messages.about_us_description ??
                                                        'Learn more.'}
                                                </CardDescription>
                                            </CardHeader>
                                        </Card>
                                    </Link>
                                </m.div>
                            ))}
                            {showBlog && (
                                <m.div {...fadeInUpView}>
                                    <Link
                                        href={`${prefix}${blog.index.url()}`}
                                        className="block transition hover:opacity-90"
                                    >
                                        <Card className="h-full">
                                            <CardHeader>
                                                <CardTitle>
                                                    {messages.blog_title ??
                                                        'Blog'}
                                                </CardTitle>
                                                <CardDescription>
                                                    {messages.blog_description ??
                                                        'Read our latest articles and updates.'}
                                                </CardDescription>
                                            </CardHeader>
                                        </Card>
                                    </Link>
                                </m.div>
                            )}
                            {showContact && (
                                <m.div {...fadeInUpView}>
                                    <Link
                                        href={`${prefix}${contact.show.url()}`}
                                        className="block transition hover:opacity-90"
                                    >
                                        <Card className="h-full">
                                            <CardHeader>
                                                <CardTitle>
                                                    {messages.contact_title ??
                                                        'Contact'}
                                                </CardTitle>
                                                <CardDescription>
                                                    {messages.contact_description ??
                                                        'Get in touch with our team.'}
                                                </CardDescription>
                                            </CardHeader>
                                        </Card>
                                    </Link>
                                </m.div>
                            )}
                        </div>
                    </section>
                )}
            </article>
        </PublicLayout>
    );
}
