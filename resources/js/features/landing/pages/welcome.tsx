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
const EMPTY_LATEST_POSTS: LatestPost[] = [];

function WelcomeHero({
    heroSection,
    heroHeading,
    heroSubtitle,
    heroPrimaryCta,
    heroSecondaryCta,
    heroPrimaryUrl,
    canRegister,
    showContact,
    prefix,
}: {
    heroSection?: Section | null;
    heroHeading: string;
    heroSubtitle: string;
    heroPrimaryCta: string;
    heroSecondaryCta: string;
    heroPrimaryUrl: string;
    canRegister: boolean;
    showContact: boolean;
    prefix: string;
}) {
    return (
        <section
            className={
                heroSection?.image_url
                    ? 'relative mx-auto w-full overflow-hidden rounded-xl bg-muted/50'
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
                                <Link href={`${prefix}${contact.show.url()}`}>
                                    {heroSecondaryCta}
                                </Link>
                            </Button>
                        </m.span>
                    )}
                </div>
            </m.div>
        </section>
    );
}

function WelcomeFeaturesSection({ section }: { section: Section }) {
    return (
        <section className="pt-12 lg:pt-16">
            {section.title && (
                <h2 className="mb-2 text-center text-2xl font-semibold text-foreground">
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

function WelcomeTestimonialsSection({ section }: { section: Section }) {
    return (
        <section className="pt-12 lg:pt-16">
            {section.title && (
                <h2 className="mb-8 text-center text-2xl font-semibold text-foreground">
                    {section.title}
                </h2>
            )}
            <div className="mx-auto grid gap-6 sm:grid-cols-2">
                {(section.items ?? []).map((item, j) => (
                    <blockquote
                        key={
                            item.title ?? item.description ?? `testimonial-${j}`
                        }
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
                <h2 className="mb-2 text-center text-2xl font-semibold text-foreground">
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
                        <h2 className="text-xl font-semibold text-foreground sm:text-2xl">
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

function WelcomeExploreSection({
    showPages,
    showBlog,
    showContact,
    nav_pages,
    messages,
    prefix,
}: {
    showPages: boolean;
    showBlog: boolean;
    showContact: boolean;
    nav_pages: Array<{ slug: string; title: string }>;
    messages: WelcomeMessages;
    prefix: string;
}) {
    if (!showPages && !showBlog && !showContact) {
        return null;
    }
    return (
        <section className="pt-12 lg:pt-16">
            <h2 className="mb-6 text-center text-sm font-medium tracking-wider text-muted-foreground uppercase">
                {messages.explore ?? 'Explore'}
            </h2>
            <div className="grid items-stretch gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {nav_pages.map((p) => (
                    <m.div
                        key={p.slug}
                        {...fadeInUpView}
                        className="flex h-full flex-col"
                    >
                        <Link
                            href={`${prefix}${page.show.url({ slug: p.slug })}`}
                            className="flex h-full flex-col transition hover:opacity-90"
                        >
                            <Card className="flex h-full flex-col">
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
                    <m.div {...fadeInUpView} className="flex h-full flex-col">
                        <Link
                            href={`${prefix}${blog.index.url()}`}
                            className="flex h-full flex-col transition hover:opacity-90"
                        >
                            <Card className="flex h-full flex-col">
                                <CardHeader>
                                    <CardTitle>
                                        {messages.blog_title ?? 'Blog'}
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
                    <m.div {...fadeInUpView} className="flex h-full flex-col">
                        <Link
                            href={`${prefix}${contact.show.url()}`}
                            className="flex h-full flex-col transition hover:opacity-90"
                        >
                            <Card className="flex h-full flex-col">
                                <CardHeader>
                                    <CardTitle>
                                        {messages.contact_title ?? 'Contact'}
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
                <WelcomeHero
                    heroSection={heroSection}
                    heroHeading={heroHeading}
                    heroSubtitle={heroSubtitle}
                    heroPrimaryCta={heroPrimaryCta}
                    heroSecondaryCta={heroSecondaryCta}
                    heroPrimaryUrl={heroPrimaryUrl}
                    canRegister={canRegister}
                    showContact={showContact}
                    prefix={prefix}
                />
                {featuresSections.map((sec) => (
                    <WelcomeFeaturesSection
                        key={sectionKey(sec)}
                        section={sec}
                    />
                ))}
                {testimonialsSections.map((sec) => (
                    <WelcomeTestimonialsSection
                        key={sectionKey(sec)}
                        section={sec}
                    />
                ))}
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
                <WelcomeExploreSection
                    showPages={showPages}
                    showBlog={showBlog}
                    showContact={showContact}
                    nav_pages={nav_pages}
                    messages={messages}
                    prefix={prefix}
                />
            </article>
        </PublicLayout>
    );
}
