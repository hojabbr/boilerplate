import { Head, Link, usePage } from '@inertiajs/react';
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

interface WelcomeProps {
    canRegister?: boolean;
    settings?: PublicSettings;
    features?: PublicFeatures;
}

export default function Welcome({
    canRegister = true,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
}: WelcomeProps) {
    const { locale } = usePage().props as { locale: string };
    const prefix = locale ? `/${locale}` : '';
    const showPages = features.pages ?? false;
    const showBlog = features.blog ?? false;
    const showContact = features.contactForm ?? false;
    const tagline = settings.tagline || 'Build something great.';
    const companyName = settings.company_name || 'App';

    return (
        <PublicLayout
            settings={settings}
            features={features}
            canRegister={canRegister}
        >
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
                    rel="stylesheet"
                />
            </Head>
            <article className="flex flex-col gap-12 py-8 sm:py-12 lg:gap-16 lg:py-16">
                <section className="mx-auto w-full max-w-3xl text-center">
                    <h1 className="text-3xl font-semibold tracking-tight text-foreground sm:text-4xl lg:text-5xl">
                        Welcome to {companyName}
                    </h1>
                    <p className="mt-4 text-lg text-muted-foreground sm:text-xl">
                        {tagline}
                    </p>
                    <div className="mt-8 flex flex-wrap items-center justify-center gap-4">
                        {canRegister && (
                            <Button size="lg" asChild>
                                <Link href={register()}>Get started</Link>
                            </Button>
                        )}
                        {showContact && (
                            <Button variant="outline" size="lg" asChild>
                                <Link href={`${prefix}${contact.show.url()}`}>
                                    Contact us
                                </Link>
                            </Button>
                        )}
                    </div>
                </section>
                {(showPages || showBlog || showContact) && (
                    <section className="border-t border-border pt-12 lg:pt-16">
                        <h2 className="mb-6 text-center text-sm font-medium tracking-wider text-muted-foreground uppercase">
                            Explore
                        </h2>
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {showPages && (
                                <Link
                                    href={`${prefix}${page.show.url({ slug: 'about-us' })}`}
                                    className="block transition hover:opacity-90"
                                >
                                    <Card className="h-full">
                                        <CardHeader>
                                            <CardTitle>About us</CardTitle>
                                            <CardDescription>
                                                Learn more about our company and
                                                mission.
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
                                            <CardTitle>Blog</CardTitle>
                                            <CardDescription>
                                                Read our latest articles and
                                                updates.
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
                                            <CardTitle>Contact</CardTitle>
                                            <CardDescription>
                                                Get in touch with our team.
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
