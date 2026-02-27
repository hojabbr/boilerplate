import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Layers, Quote, Sparkles, Zap } from 'lucide-react';
import { m } from 'motion/react';
import { fadeInUp, fadeInUpView } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
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
    faq_title?: string;
    faq_description?: string;
    testimonials_title?: string;
    testimonials_description?: string;
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

/** Stable key for a section (no array index) so React keys are safe on reorder/filter. */
function sectionKey(sec: Section): string {
    return [
        sec.type,
        sec.title ?? '',
        sec.subtitle ?? '',
        sec.image_url ?? '',
        sec.cta_url ?? '',
    ].join('|');
}

interface LatestPost {
    slug: string;
    title: string;
    excerpt: string;
    published_at: string | null;
    thumbnail_url?: string | null;
}

interface TestimonialItem {
    quote: string;
    author: string;
    role?: string | null;
}

interface WelcomeProps {
    canRegister?: boolean;
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
    messages?: WelcomeMessages;
    sections?: Section[];
    latest_posts?: LatestPost[];
    testimonials?: TestimonialItem[];
}

const EMPTY_WELCOME_MESSAGES: WelcomeMessages = {};
const EMPTY_SECTIONS: Section[] = [];
const EMPTY_LATEST_POSTS: LatestPost[] = [];
const EMPTY_TESTIMONIALS: TestimonialItem[] = [];

function WelcomeHero({
    heroSection,
    heroHeading,
    heroSubtitle,
}: {
    heroSection?: Section | null;
    heroHeading: string;
    heroSubtitle: string;
}) {
    return (
        <section
            className={
                heroSection?.image_url
                    ? 'relative flex min-h-[50vh] w-full items-center justify-center overflow-hidden rounded-none bg-muted/50'
                    : 'mx-auto w-full max-w-3xl px-4 text-center sm:px-6 lg:px-8'
            }
        >
            {heroSection?.image_url && (
                <>
                    <img
                        src={heroSection.image_url}
                        alt=""
                        className="absolute inset-0 h-full w-full object-cover"
                    />
                    <div
                        className="absolute inset-0 bg-black/40 dark:bg-black/55"
                        aria-hidden
                    />
                </>
            )}
            <m.div
                className={
                    heroSection?.image_url
                        ? 'relative z-0 mx-auto max-w-3xl px-6 py-12 text-center sm:py-16'
                        : ''
                }
                {...fadeInUp}
            >
                <h1
                    className={
                        heroSection?.image_url
                            ? 'flex flex-wrap items-center justify-center gap-3 text-3xl font-semibold tracking-tight text-white drop-shadow-md sm:text-4xl lg:text-5xl'
                            : 'flex flex-wrap items-center justify-center gap-3 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl lg:text-5xl'
                    }
                >
                    <span
                        className={
                            heroSection?.image_url
                                ? 'inline-flex shrink-0 items-center justify-center rounded-full bg-white/20 p-3'
                                : 'inline-flex shrink-0 items-center justify-center rounded-full bg-primary/10 p-3'
                        }
                        aria-hidden
                    >
                        <Sparkles
                            className={
                                heroSection?.image_url
                                    ? 'size-6 text-white'
                                    : 'size-6 text-primary/70'
                            }
                            aria-hidden
                        />
                    </span>
                    {heroHeading}
                </h1>
                <p
                    className={
                        heroSection?.image_url
                            ? 'mt-4 text-lg text-white/95 drop-shadow sm:text-xl'
                            : 'mt-4 text-lg text-muted-foreground sm:text-xl'
                    }
                >
                    {heroSubtitle}
                </p>
            </m.div>
        </section>
    );
}

function WelcomeFeaturesSection({ section }: { section: Section }) {
    return (
        <section className="pt-12 lg:pt-16">
            {section.title && (
                <h2 className="mb-2 flex items-center justify-center gap-3 text-center text-2xl font-semibold text-foreground">
                    <span
                        className="inline-flex shrink-0 items-center justify-center rounded-full bg-primary/10 p-3"
                        aria-hidden
                    >
                        <Layers
                            className="size-6 text-primary/70"
                            aria-hidden
                        />
                    </span>
                    {section.title}
                </h2>
            )}
            {section.subtitle && (
                <p className="mx-auto mb-8 max-w-2xl text-center text-muted-foreground">
                    {section.subtitle}
                </p>
            )}
            <div className="mx-auto grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                {(section.items ?? []).map((item, j) => (
                    <m.div
                        key={item.title ?? item.icon_url ?? `feature-item-${j}`}
                        {...fadeInUpView}
                        transition={{
                            duration: 0.4,
                            ease: 'easeOut' as const,
                            delay: j * 0.06,
                        }}
                    >
                        <Card className="flex h-full flex-col gap-0 overflow-hidden rounded-2xl border border-border p-0 shadow-sm">
                            {item.icon_url && (
                                <img
                                    src={item.icon_url}
                                    alt=""
                                    className="h-40 w-full shrink-0 object-cover"
                                />
                            )}
                            <CardHeader className="py-6">
                                {item.title && (
                                    <CardTitle>{item.title}</CardTitle>
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
    );
}

function WelcomeDbTestimonialsSection({
    testimonials,
    title,
}: {
    testimonials: TestimonialItem[];
    title: string;
}) {
    if (testimonials.length === 0) {
        return null;
    }
    return (
        <section className="pt-12 lg:pt-16">
            <h2 className="mb-8 flex items-center justify-center gap-3 text-center text-2xl font-semibold text-foreground">
                <span
                    className="inline-flex shrink-0 items-center justify-center rounded-full bg-primary/10 p-3"
                    aria-hidden
                >
                    <Quote className="size-6 text-primary/70" aria-hidden />
                </span>
                {title}
            </h2>
            <div className="mx-auto grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {testimonials.map((t, j) => (
                    <m.div
                        key={`${t.author}-${j}`}
                        {...fadeInUpView}
                        transition={{
                            duration: 0.4,
                            ease: 'easeOut' as const,
                            delay: j * 0.06,
                        }}
                    >
                        <Card className="flex h-full flex-col rounded-2xl border border-border p-6 shadow-sm">
                            <Quote
                                className="mb-4 size-8 text-primary/60"
                                aria-hidden
                            />
                            <CardContent className="flex flex-1 flex-col p-0">
                                <blockquote className="flex-1 text-lg leading-relaxed text-foreground/90">
                                    "{t.quote}"
                                </blockquote>
                                <footer className="mt-6 shrink-0 border-t border-border/60 pt-4">
                                    <cite className="font-semibold text-foreground not-italic">
                                        {t.author}
                                    </cite>
                                    {t.role && (
                                        <p className="mt-0.5 text-sm text-muted-foreground">
                                            {t.role}
                                        </p>
                                    )}
                                </footer>
                            </CardContent>
                        </Card>
                    </m.div>
                ))}
            </div>
        </section>
    );
}

function WelcomeLatestPostsSection({
    section,
    latest_posts,
    showBlog,
    blogTitle,
    prefix,
}: {
    section: Section;
    latest_posts: LatestPost[];
    showBlog: boolean;
    blogTitle: string;
    prefix: string;
}) {
    return (
        <section className="pt-12 lg:pt-16">
            {section.title && (
                <h2 className="mb-2 flex items-center justify-center gap-3 text-center text-2xl font-semibold text-foreground">
                    <span
                        className="inline-flex shrink-0 items-center justify-center rounded-full bg-primary/10 p-3"
                        aria-hidden
                    >
                        <BookOpen
                            className="size-6 text-primary/70"
                            aria-hidden
                        />
                    </span>
                    {section.title}
                </h2>
            )}
            {section.subtitle && (
                <p className="mx-auto mb-8 max-w-2xl text-center text-muted-foreground">
                    {section.subtitle}
                </p>
            )}
            {latest_posts.length > 0 && (
                <div className="mx-auto grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {latest_posts.map((post) => (
                        <Link
                            key={post.slug}
                            href={`${prefix}${blog.show.url({ slug: post.slug })}`}
                            className="block transition hover:opacity-90"
                        >
                            <Card className="flex h-full flex-col gap-0 overflow-hidden rounded-2xl border border-border p-0 shadow-sm">
                                {post.thumbnail_url && (
                                    <img
                                        src={post.thumbnail_url}
                                        alt=""
                                        className="h-56 w-full shrink-0 object-cover"
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
                            {blogTitle}
                        </Link>
                    </Button>
                </p>
            )}
        </section>
    );
}

function WelcomeCtaSection({
    section,
    prefix,
}: {
    section: Section;
    prefix: string;
}) {
    return (
        <section className="pt-12 lg:pt-16">
            <div
                className={
                    section.image_url
                        ? 'relative mx-auto flex flex-col overflow-hidden rounded-xl bg-muted/50 sm:flex-row'
                        : 'mx-auto max-w-2xl rounded-xl border border-border bg-muted/30 px-6 py-10 text-center'
                }
            >
                {section.image_url && (
                    <div className="relative h-48 shrink-0 sm:h-auto sm:w-1/3">
                        <img
                            src={section.image_url}
                            alt=""
                            className="h-full w-full object-cover"
                        />
                    </div>
                )}
                <div
                    className={
                        section.image_url
                            ? 'relative flex flex-1 flex-col justify-center px-6 py-8 text-center sm:py-12 sm:text-start'
                            : ''
                    }
                >
                    {section.title && (
                        <h2 className="flex items-center gap-3 text-xl font-semibold text-foreground sm:text-2xl">
                            <span
                                className="inline-flex shrink-0 items-center justify-center rounded-full bg-primary/10 p-2.5"
                                aria-hidden
                            >
                                <Zap
                                    className="size-5 text-primary/70"
                                    aria-hidden
                                />
                            </span>
                            {section.title}
                        </h2>
                    )}
                    {section.subtitle && (
                        <p className="mt-2 text-muted-foreground">
                            {section.subtitle}
                        </p>
                    )}
                    {section.cta_text && section.cta_url && (
                        <div className="mt-4">
                            <Button asChild>
                                <Link
                                    href={
                                        section.cta_url.startsWith('http')
                                            ? section.cta_url
                                            : `${prefix}${section.cta_url}`
                                    }
                                >
                                    {section.cta_text}
                                </Link>
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
}

export default function Welcome({
    canRegister = true,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
    messages = EMPTY_WELCOME_MESSAGES,
    sections = EMPTY_SECTIONS,
    latest_posts = EMPTY_LATEST_POSTS,
    testimonials = EMPTY_TESTIMONIALS,
}: WelcomeProps) {
    const { locale, canonical_url: canonicalUrl } = usePage().props as {
        locale: string;
        canonical_url?: string;
    };
    const prefix = locale ? `/${locale}` : '';
    const showBlog = features.blog ?? false;
    const tagline =
        settings.tagline ||
        (messages.tagline_fallback ?? 'Build something great.');
    const companyName = settings.company_name ?? 'App';

    const heroSection = sections.find((s) => s.type === 'hero');
    const featuresSections = sections.filter((s) => s.type === 'features');
    const latestPostsSections = sections.filter(
        (s) => s.type === 'latest_posts',
    );
    const ctaSections = sections.filter((s) => s.type === 'cta');

    const heroHeading =
        heroSection?.title ?? messages.heading ?? `Welcome to ${companyName}`;
    const heroSubtitle = heroSection?.subtitle ?? tagline;

    const websiteSchema: Record<string, unknown> = {
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: companyName,
        description: tagline,
        ...(canonicalUrl ? { url: canonicalUrl } : {}),
    };

    const organizationSchema: Record<string, unknown> = {
        '@context': 'https://schema.org',
        '@type': 'Organization',
        name: companyName,
        description: tagline,
        ...(canonicalUrl ? { url: canonicalUrl } : {}),
        ...(settings.email ? { email: settings.email } : {}),
        ...(settings.phone ? { telephone: settings.phone } : {}),
    };

    return (
        <PublicLayout
            contentVariant="full-bleed"
            settings={settings}
            features={features}
            canRegister={canRegister}
        >
            <SeoHead
                title={seo?.title ?? companyName}
                description={seo?.description ?? tagline}
                jsonLd={[websiteSchema, organizationSchema]}
            />
            <article className="flex flex-col pt-0">
                <WelcomeHero
                    heroSection={heroSection}
                    heroHeading={heroHeading}
                    heroSubtitle={heroSubtitle}
                />
                <div className="mx-auto flex w-full max-w-6xl flex-col gap-12 px-4 pb-8 sm:px-6 sm:pb-12 lg:gap-16 lg:px-8 lg:pb-16">
                    {featuresSections.map((sec) => (
                        <WelcomeFeaturesSection
                            key={sectionKey(sec)}
                            section={sec}
                        />
                    ))}
                    {features.testimonials && (
                        <WelcomeDbTestimonialsSection
                            testimonials={testimonials}
                            title={
                                messages.testimonials_title ??
                                'What our customers say'
                            }
                        />
                    )}
                    {latestPostsSections.map((sec) => (
                        <WelcomeLatestPostsSection
                            key={sectionKey(sec)}
                            section={sec}
                            latest_posts={latest_posts}
                            showBlog={showBlog}
                            blogTitle={messages.blog_title ?? 'Blog'}
                            prefix={prefix}
                        />
                    ))}
                    {ctaSections.map((sec) => (
                        <WelcomeCtaSection
                            key={sectionKey(sec)}
                            section={sec}
                            prefix={prefix}
                        />
                    ))}
                </div>
            </article>
        </PublicLayout>
    );
}
