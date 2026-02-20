import { Link, usePage } from '@inertiajs/react';
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

interface WelcomeProps {
    canRegister?: boolean;
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
    messages?: WelcomeMessages;
}

const EMPTY_WELCOME_MESSAGES: WelcomeMessages = {};

export default function Welcome({
    canRegister = true,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
    messages = EMPTY_WELCOME_MESSAGES,
}: WelcomeProps) {
    const { locale } = usePage().props as { locale: string };
    const prefix = locale ? `/${locale}` : '';
    const showPages = features.pages ?? false;
    const showBlog = features.blog ?? false;
    const showContact = features.contactForm ?? false;
    const tagline =
        settings.tagline ||
        (messages.tagline_fallback ?? 'Build something great.');
    const companyName = settings.company_name ?? 'App';

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
                <section className="mx-auto w-full max-w-3xl text-center">
                    <h1 className="text-3xl font-semibold tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                        {messages.heading ?? `Welcome to ${companyName}`}
                    </h1>
                    <p className="mt-4 text-lg text-muted-foreground sm:text-xl">
                        {tagline}
                    </p>
                    <div className="mt-8 flex flex-wrap items-center justify-center gap-4">
                        {canRegister && (
                            <Button size="lg" asChild>
                                <Link href={`${prefix}${register.url()}`}>
                                    {messages.cta_get_started ?? 'Get started'}
                                </Link>
                            </Button>
                        )}
                        {showContact && (
                            <Button variant="outline" size="lg" asChild>
                                <Link href={`${prefix}${contact.show.url()}`}>
                                    {messages.cta_contact_us ?? 'Contact us'}
                                </Link>
                            </Button>
                        )}
                    </div>
                </section>
                {(showPages || showBlog || showContact) && (
                    <section className="border-t border-border pt-12 lg:pt-16">
                        <h2 className="mb-6 text-center text-sm font-medium tracking-wider text-muted-foreground uppercase">
                            {messages.explore ?? 'Explore'}
                        </h2>
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {showPages && (
                                <Link
                                    href={`${prefix}${page.show.url({ slug: 'about-us' })}`}
                                    className="block transition hover:opacity-90"
                                >
                                    <Card className="h-full">
                                        <CardHeader>
                                            <CardTitle>
                                                {messages.about_us_title ??
                                                    'About us'}
                                            </CardTitle>
                                            <CardDescription>
                                                {messages.about_us_description ??
                                                    'Learn more about our company and mission.'}
                                            </CardDescription>
                                        </CardHeader>
                                    </Card>
                                </Link>
                            )}
                            {showBlog && (
                                <Link
                                    href={`${prefix}${blog.index.url()}`}
                                    className="block transition hover:opacity-90"
                                >
                                    <Card className="h-full">
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
                            )}
                            {showContact && (
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
                            )}
                        </div>
                    </section>
                )}
            </article>
        </PublicLayout>
    );
}
