import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import { m } from 'motion/react';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { pageEnter } from '@/components/common/motion-presets';
import NavSearch from '@/components/common/NavSearch';
import SocialLinks from '@/components/common/SocialLinks';
import LanguageSwitcher from '@/components/language-switcher';
import ThemeSwitcher from '@/components/theme-switcher';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { dashboard, home, login, register } from '@/routes';
import blog from '@/routes/blog';
import contact from '@/routes/contact';
import page from '@/routes/page';
import type { BreadcrumbItem } from '@/types';

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
    login?: boolean;
    registration?: boolean;
}

/** Module-level empty defaults to avoid new object reference every render */
export const EMPTY_PUBLIC_SETTINGS: PublicSettings = {};
export const EMPTY_PUBLIC_FEATURES: PublicFeatures = {};

interface PublicLayoutProps {
    children: React.ReactNode;
    breadcrumbs?: BreadcrumbItem[];
    settings?: PublicSettings;
    features?: PublicFeatures;
    canRegister?: boolean;
}

type NavItem = {
    label: string;
    href: string;
    show: boolean;
    desktopClass?: string;
};

export default function PublicLayout({
    children,
    breadcrumbs,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
}: PublicLayoutProps) {
    const pageProps = usePage().props as {
        auth: { user: unknown };
        locale: string;
        translations?: Record<string, string>;
        nav_pages?: Array<{ slug: string; title: string }>;
        footer_pages?: Array<{ slug: string; title: string }>;
        features?: PublicFeatures;
    };
    const {
        auth,
        locale,
        translations,
        nav_pages = [],
        footer_pages = [],
    } = pageProps;
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';
    const resolvedFeatures =
        features ?? pageProps.features ?? EMPTY_PUBLIC_FEATURES;
    const showPages = resolvedFeatures.page ?? false;
    const showBlog = resolvedFeatures.blog ?? false;
    const showContact = resolvedFeatures.contactForm ?? false;
    const showLogin = resolvedFeatures.login === true;
    const showRegister = resolvedFeatures.registration === true;
    const siteName =
        settings.company_name || (t['common.app_fallback'] ?? 'App');

    const mainNavItems: NavItem[] = [
        {
            label: t['nav.home'] ?? 'Home',
            href: prefix ? prefix : home.url(),
            show: true,
        },
        ...(showPages
            ? (nav_pages as Array<{ slug: string; title: string }>).map(
                  (p) => ({
                      label: p.title,
                      href: `${prefix}${page.show.url({ slug: p.slug })}`,
                      show: true,
                  }),
              )
            : []),
        {
            label: t['nav.blog'] ?? 'Blog',
            href: `${prefix}${blog.index.url()}`,
            show: showBlog,
        },
        {
            label: t['nav.contact'] ?? 'Contact',
            href: `${prefix}${contact.show.url()}`,
            show: showContact,
        },
    ];

    return (
        <div className="flex min-h-screen flex-col bg-background text-foreground">
            <header className="sticky top-0 z-10 border-b border-border bg-background/95 backdrop-blur">
                <div className="mx-auto flex h-14 max-w-6xl flex-shrink-0 flex-nowrap items-center justify-between gap-2 px-4 sm:gap-4 lg:px-0">
                    <Link
                        href={prefix ? prefix : home.url()}
                        className="min-w-0 shrink-0 truncate text-lg font-semibold text-foreground"
                    >
                        {siteName}
                    </Link>

                    {/* Desktop nav: visible from lg (1024px) up so hamburger is used for sm/md */}
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
                                        className={
                                            item.desktopClass ?? undefined
                                        }
                                        asChild
                                    >
                                        <Link href={item.href}>
                                            {item.label}
                                        </Link>
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
                                        <Link
                                            href={`${prefix}${register.url()}`}
                                        >
                                            {t['nav.register'] ?? 'Register'}
                                        </Link>
                                    </Button>
                                )}
                            </>
                        )}
                    </nav>

                    {/* Mobile: hamburger + sheet (visible below lg) */}
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
                                    aria-label={
                                        t['nav.open_menu'] ?? 'Open menu'
                                    }
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
                                                    className="w-full justify-start"
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
                                                {t['nav.dashboard'] ??
                                                    'Dashboard'}
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
                                                        {t['nav.login'] ??
                                                            'Log in'}
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
            <main className="flex-1 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <m.div className="mx-auto max-w-6xl" {...pageEnter}>
                    {breadcrumbs && breadcrumbs.length > 0 && (
                        <div className="mb-4">
                            <Breadcrumbs breadcrumbs={breadcrumbs} />
                        </div>
                    )}
                    {children}
                </m.div>
            </main>
            <footer className="py-6">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <nav
                        className="flex flex-wrap justify-center gap-x-6 gap-y-1 text-sm text-muted-foreground"
                        aria-label="Footer"
                    >
                        {(
                            footer_pages as Array<{
                                slug: string;
                                title: string;
                            }>
                        ).map((p) => (
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
                    </nav>
                    {settings.social_links &&
                        Object.keys(settings.social_links).length > 0 && (
                            <SocialLinks
                                social_links={settings.social_links}
                                variant="footer"
                                className="mt-4"
                            />
                        )}
                    <p className="mt-4 text-center text-sm text-muted-foreground">
                        © {new Date().getFullYear()} {siteName}. All rights
                        reserved.
                    </p>
                </div>
            </footer>
        </div>
    );
}
