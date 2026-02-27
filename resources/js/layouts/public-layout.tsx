import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import { m } from 'motion/react';
import { useEffect, useState } from 'react';
import AppLogoIcon from '@/components/app-logo-icon';
import { pageEnter } from '@/components/common/motion-presets';
import NavSearch from '@/components/common/NavSearch';
import SocialLinks from '@/components/common/SocialLinks';
import LanguageSwitcher from '@/components/language-switcher';
import ThemeSwitcher from '@/components/theme-switcher';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { cn } from '@/lib/utils';
import { dashboard, home, login, register } from '@/routes';
import blog from '@/routes/blog';
import contact from '@/routes/contact';
import faq from '@/routes/faq';
import page from '@/routes/page';
import testimonials from '@/routes/testimonials';
export interface PublicSettings {
    company_name?: string;
    tagline?: string;
    email?: string;
    phone?: string;
    /** Key-value map of social network key (e.g. twitter, linkedin) to URL */
    social_links?: Record<string, string>;
}

export interface PublicFeatures {
    page?: boolean;
    blog?: boolean;
    contactForm?: boolean;
    faq?: boolean;
    testimonials?: boolean;
    login?: boolean;
    registration?: boolean;
}

/** Module-level empty defaults to avoid new object reference every render */
export const EMPTY_PUBLIC_SETTINGS: PublicSettings = {};
export const EMPTY_PUBLIC_FEATURES: PublicFeatures = {};

interface PublicLayoutProps {
    children: React.ReactNode;
    /** When 'full-bleed', main has no top/side padding and inner wrapper is full width for hero-style pages. */
    contentVariant?: 'default' | 'full-bleed';
    settings?: PublicSettings;
    features?: PublicFeatures;
    canRegister?: boolean;
}

type NavItem = {
    label: string;
    href: string;
    show: boolean;
    isActive?: boolean;
    desktopClass?: string;
};

function PublicHeader({
    mainNavItems,
    prefix,
    t,
    auth,
    showLogin,
    showRegister,
    siteName,
    logo_url,
}: {
    mainNavItems: NavItem[];
    prefix: string;
    t: Record<string, string>;
    auth: { user: unknown };
    showLogin: boolean;
    showRegister: boolean;
    siteName: string;
    logo_url?: string | null;
}) {
    return (
        <header className="sticky top-0 z-50 border-b border-border bg-background/95 backdrop-blur">
            <div className="mx-auto flex h-14 max-w-6xl flex-shrink-0 flex-nowrap items-center justify-between gap-2 px-4 sm:gap-4 lg:px-0">
                <Link
                    href={prefix ? prefix : home.url()}
                    className="flex min-w-0 shrink-0 items-center gap-2"
                >
                    <AppLogoIcon
                        src={logo_url}
                        alt={siteName}
                        className="h-7 w-auto"
                    />
                    <span className="truncate text-lg font-semibold text-foreground">
                        {siteName}
                    </span>
                </Link>

                <nav
                    className="hidden flex-1 items-center justify-end gap-1 sm:gap-2 lg:flex"
                    aria-label="Main"
                >
                    {mainNavItems.map(
                        (item) =>
                            item.show && (
                                <Button
                                    key={item.href}
                                    variant="ghost"
                                    size="sm"
                                    className={cn(
                                        'border-b-2 border-b-transparent',
                                        item.isActive &&
                                            'border-b-primary font-medium',
                                        item.desktopClass,
                                    )}
                                    asChild
                                >
                                    <Link href={item.href}>{item.label}</Link>
                                </Button>
                            ),
                    )}
                    <div className="flex items-center gap-1 border-s border-border ps-2">
                        <NavSearch />
                        <ThemeSwitcher
                            variant="ghost"
                            size="icon"
                            className="text-muted-foreground hover:text-foreground"
                        />
                        <LanguageSwitcher
                            variant="ghost"
                            size="icon"
                            className="text-muted-foreground hover:text-foreground"
                        />
                    </div>
                    {auth?.user ? (
                        <Button variant="outline" size="sm" asChild>
                            <Link href={`${prefix}${dashboard.url()}`}>
                                {t['nav.dashboard'] ?? 'Dashboard'}
                            </Link>
                        </Button>
                    ) : (
                        <>
                            {showLogin && (
                                <Button variant="ghost" size="sm" asChild>
                                    <Link href={`${prefix}${login.url()}`}>
                                        {t['nav.login'] ?? 'Log in'}
                                    </Link>
                                </Button>
                            )}
                            {showRegister && (
                                <Button variant="outline" size="sm" asChild>
                                    <Link href={`${prefix}${register.url()}`}>
                                        {t['nav.register'] ?? 'Register'}
                                    </Link>
                                </Button>
                            )}
                        </>
                    )}
                </nav>

                <div className="flex shrink-0 items-center gap-1 lg:hidden">
                    <NavSearch />
                    <ThemeSwitcher
                        variant="ghost"
                        size="icon"
                        className="text-muted-foreground hover:text-foreground"
                    />
                    <LanguageSwitcher
                        variant="ghost"
                        size="icon"
                        className="text-muted-foreground hover:text-foreground"
                    />
                    <Sheet>
                        <SheetTrigger asChild>
                            <Button
                                variant="ghost"
                                size="icon"
                                className="text-muted-foreground hover:text-foreground"
                                aria-label={t['nav.open_menu'] ?? 'Open menu'}
                            >
                                <Menu className="size-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="right" className="w-64">
                            <nav
                                className="flex flex-col gap-1 pt-4"
                                aria-label="Main"
                            >
                                {mainNavItems.map(
                                    (item) =>
                                        item.show && (
                                            <Button
                                                key={item.href}
                                                variant="ghost"
                                                size="sm"
                                                className={cn(
                                                    'w-full justify-start border-s-2 border-s-transparent ps-3',
                                                    item.isActive &&
                                                        'border-s-primary font-medium',
                                                )}
                                                asChild
                                            >
                                                <Link href={item.href}>
                                                    {item.label}
                                                </Link>
                                            </Button>
                                        ),
                                )}
                                {auth?.user ? (
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        className="mt-2 w-full justify-start"
                                        asChild
                                    >
                                        <Link
                                            href={`${prefix}${dashboard.url()}`}
                                        >
                                            {t['nav.dashboard'] ?? 'Dashboard'}
                                        </Link>
                                    </Button>
                                ) : (
                                    <>
                                        {showLogin && (
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                className="mt-2 w-full justify-start"
                                                asChild
                                            >
                                                <Link
                                                    href={`${prefix}${login.url()}`}
                                                >
                                                    {t['nav.login'] ?? 'Log in'}
                                                </Link>
                                            </Button>
                                        )}
                                        {showRegister && (
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                className="w-full justify-start"
                                                asChild
                                            >
                                                <Link
                                                    href={`${prefix}${register.url()}`}
                                                >
                                                    {t['nav.register'] ??
                                                        'Register'}
                                                </Link>
                                            </Button>
                                        )}
                                    </>
                                )}
                            </nav>
                        </SheetContent>
                    </Sheet>
                </div>
            </div>
        </header>
    );
}

function PublicFooter({
    footer_pages,
    prefix,
    t,
    settings,
    siteName,
    showBlog,
    showContact,
    showFaq,
    showTestimonials,
}: {
    footer_pages: Array<{ slug: string; title: string }>;
    prefix: string;
    t: Record<string, string>;
    settings: PublicSettings;
    siteName: string;
    showBlog: boolean;
    showContact: boolean;
    showFaq: boolean;
    showTestimonials: boolean;
}) {
    return (
        <footer className="py-6">
            <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <nav
                    className="flex flex-wrap justify-center gap-x-6 gap-y-1 text-sm text-muted-foreground"
                    aria-label="Footer"
                >
                    {footer_pages.map((p) => (
                        <Link
                            key={p.slug}
                            href={`${prefix}${page.show.url({ slug: p.slug })}`}
                            className="hover:text-foreground"
                        >
                            {p.title}
                        </Link>
                    ))}
                    {showBlog && (
                        <Link
                            href={`${prefix}${blog.index.url()}`}
                            className="hover:text-foreground"
                        >
                            {t['nav.blog'] ?? 'Blog'}
                        </Link>
                    )}
                    {showContact && (
                        <Link
                            href={`${prefix}${contact.show.url()}`}
                            className="hover:text-foreground"
                        >
                            {t['nav.contact'] ?? 'Contact'}
                        </Link>
                    )}
                    {showFaq && (
                        <Link
                            href={`${prefix}${faq.show.url()}`}
                            className="hover:text-foreground"
                        >
                            {t['nav.faq'] ?? 'FAQ'}
                        </Link>
                    )}
                    {showTestimonials && (
                        <Link
                            href={`${prefix}${testimonials.show.url()}`}
                            className="hover:text-foreground"
                        >
                            {t['nav.testimonials'] ?? 'Testimonials'}
                        </Link>
                    )}
                </nav>
                {settings.social_links &&
                    Object.keys(settings.social_links).length > 0 && (
                        <SocialLinks
                            social_links={settings.social_links}
                            variant="footer"
                            className="mt-4"
                        />
                    )}
                <p className="mt-4 text-center text-sm text-ancillary-foreground">
                    © {new Date().getFullYear()} {siteName}. All rights
                    reserved.
                </p>
            </div>
        </footer>
    );
}

export default function PublicLayout({
    children,
    contentVariant = 'default',
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
}: PublicLayoutProps) {
    const inertiaPage = usePage();
    const pageProps = inertiaPage.props as {
        auth: { user: unknown };
        locale: string;
        translations?: Record<string, string>;
        nav_pages?: Array<{ slug: string; title: string }>;
        footer_pages?: Array<{ slug: string; title: string }>;
        features?: PublicFeatures;
        logo_url?: string | null;
    };
    const {
        auth,
        locale,
        translations,
        nav_pages = [],
        footer_pages = [],
        logo_url,
    } = pageProps;
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';
    const resolvedFeatures =
        features ?? pageProps.features ?? EMPTY_PUBLIC_FEATURES;
    const showPages = resolvedFeatures.page ?? false;
    const showBlog = resolvedFeatures.blog ?? false;
    const showContact = resolvedFeatures.contactForm ?? false;
    const showFaq = resolvedFeatures.faq ?? false;
    const showTestimonials = resolvedFeatures.testimonials ?? false;
    const showLogin = resolvedFeatures.login === true;
    const showRegister = resolvedFeatures.registration === true;
    const siteName =
        settings.company_name || (t['common.app_fallback'] ?? 'App');

    const [currentPath, setCurrentPath] = useState('');
    useEffect(() => {
        const path = window.location.pathname;
        queueMicrotask(() => setCurrentPath(path));
    }, []);

    const isActive = (href: string) => {
        if (href === prefix || href === '/') {
            return (
                currentPath === href ||
                currentPath === prefix ||
                currentPath === '/'
            );
        }
        return (
            currentPath === href ||
            (href !== '/' && currentPath.startsWith(href + '/'))
        );
    };
    const mainNavItems: NavItem[] = [
        {
            label: t['nav.home'] ?? 'Home',
            href: prefix ? prefix : home.url(),
            show: true,
            isActive: isActive(prefix ? prefix : home.url()),
        },
        ...(showPages
            ? (nav_pages as Array<{ slug: string; title: string }>).map((p) => {
                  const href = `${prefix}${page.show.url({ slug: p.slug })}`;
                  return {
                      label: p.title,
                      href,
                      show: true,
                      isActive: isActive(href),
                  };
              })
            : []),
        {
            label: t['nav.blog'] ?? 'Blog',
            href: `${prefix}${blog.index.url()}`,
            show: showBlog,
            isActive: isActive(`${prefix}${blog.index.url()}`),
        },
        {
            label: t['nav.contact'] ?? 'Contact',
            href: `${prefix}${contact.show.url()}`,
            show: showContact,
            isActive: isActive(`${prefix}${contact.show.url()}`),
        },
        {
            label: t['nav.faq'] ?? 'FAQ',
            href: `${prefix}${faq.show.url()}`,
            show: showFaq,
            isActive: isActive(`${prefix}${faq.show.url()}`),
        },
        {
            label: t['nav.testimonials'] ?? 'Testimonials',
            href: `${prefix}${testimonials.show.url()}`,
            show: showTestimonials,
            isActive: isActive(`${prefix}${testimonials.show.url()}`),
        },
    ];

    return (
        <div className="flex min-h-screen flex-col bg-background text-foreground">
            <PublicHeader
                mainNavItems={mainNavItems}
                prefix={prefix}
                t={t}
                auth={auth}
                showLogin={showLogin}
                showRegister={showRegister}
                siteName={siteName}
                logo_url={logo_url}
            />
            <main
                className={
                    contentVariant === 'full-bleed'
                        ? 'flex-1 pt-0 pb-6 sm:pb-8'
                        : 'flex-1 px-4 py-6 sm:px-6 sm:py-8 lg:px-8'
                }
            >
                <m.div
                    className={
                        contentVariant === 'full-bleed'
                            ? 'max-w-none'
                            : 'mx-auto max-w-6xl'
                    }
                    {...pageEnter}
                    initial={false}
                >
                    {children}
                </m.div>
            </main>
            <PublicFooter
                footer_pages={
                    footer_pages as Array<{
                        slug: string;
                        title: string;
                    }>
                }
                prefix={prefix}
                t={t}
                settings={settings}
                siteName={siteName}
                showBlog={showBlog}
                showContact={showContact}
                showFaq={showFaq}
                showTestimonials={showTestimonials}
            />
        </div>
    );
}
