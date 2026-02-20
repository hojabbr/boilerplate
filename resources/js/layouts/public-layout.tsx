import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import NavSearch from '@/components/common/NavSearch';
import LanguageSwitcher from '@/components/language-switcher';
import ThemeSwitcher from '@/components/theme-switcher';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
import { dashboard, home, login, register } from '@/routes';
import blog from '@/routes/blog';
import contact from '@/routes/contact';
import page from '@/routes/page';

export interface PublicSettings {
    company_name?: string;
    tagline?: string;
    email?: string;
    phone?: string;
}

export interface PublicFeatures {
    pages?: boolean;
    blog?: boolean;
    contactForm?: boolean;
}

/** Module-level empty defaults to avoid new object reference every render */
export const EMPTY_PUBLIC_SETTINGS: PublicSettings = {};
export const EMPTY_PUBLIC_FEATURES: PublicFeatures = {};

interface PublicLayoutProps {
    children: React.ReactNode;
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
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    canRegister = true,
}: PublicLayoutProps) {
    const { auth, locale, translations } = usePage().props as {
        auth: { user: unknown };
        locale: string;
        translations?: Record<string, string>;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';
    const showPages = features.pages ?? false;
    const showBlog = features.blog ?? false;
    const showContact = features.contactForm ?? false;
    const siteName =
        settings.company_name || (t['common.app_fallback'] ?? 'App');

    const mainNavItems: NavItem[] = [
        {
            label: t['nav.home'] ?? 'Home',
            href: prefix ? prefix : home.url(),
            show: true,
        },
        {
            label: t['nav.about'] ?? 'About',
            href: `${prefix}${page.show.url({ slug: 'about-us' })}`,
            show: showPages,
            desktopClass: 'hidden sm:inline-flex',
        },
        {
            label: t['nav.privacy'] ?? 'Privacy',
            href: `${prefix}${page.show.url({ slug: 'privacy-policy' })}`,
            show: showPages,
            desktopClass: 'hidden md:inline-flex',
        },
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
                <div className="mx-auto flex h-14 max-w-6xl flex-wrap items-center justify-between gap-4 px-4 sm:h-16 sm:px-6 lg:px-8">
                    <Link
                        href={prefix ? prefix : home.url()}
                        className="text-lg font-semibold text-foreground"
                    >
                        {siteName}
                    </Link>

                    {/* Desktop nav: visible from md up */}
                    <nav
                        className="hidden flex-1 items-center justify-end gap-1 sm:gap-2 md:flex"
                        aria-label="Main"
                    >
                        {mainNavItems.map(
                            (item) =>
                                item.show && (
                                    <Button
                                        key={item.label}
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
                                <Button variant="ghost" size="sm" asChild>
                                    <Link href={`${prefix}${login.url()}`}>
                                        {t['nav.login'] ?? 'Log in'}
                                    </Link>
                                </Button>
                                {canRegister && (
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

                    {/* Mobile: hamburger + sheet */}
                    <div className="flex items-center gap-1 md:hidden">
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
                                                    key={item.label}
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
                                            {canRegister && (
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
                <div className="mx-auto max-w-6xl">{children}</div>
            </main>
            <footer className="border-t border-border py-6">
                <div className="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <nav
                        className="flex flex-wrap justify-center gap-x-6 gap-y-1 text-sm text-muted-foreground"
                        aria-label="Footer"
                    >
                        {showPages && (
                            <>
                                <Link
                                    href={`${prefix}${page.show.url({ slug: 'privacy-policy' })}`}
                                    className="hover:text-foreground"
                                >
                                    {t['nav.privacy'] ?? 'Privacy'}
                                </Link>
                                <Link
                                    href={`${prefix}${page.show.url({ slug: 'terms-of-use' })}`}
                                    className="hover:text-foreground"
                                >
                                    {t['nav.terms'] ?? 'Terms'}
                                </Link>
                                <Link
                                    href={`${prefix}${page.show.url({ slug: 'about-us' })}`}
                                    className="hover:text-foreground"
                                >
                                    {t['nav.about'] ?? 'About'}
                                </Link>
                            </>
                        )}
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
                    <p className="mt-4 text-center text-sm text-muted-foreground">
                        © {new Date().getFullYear()} {siteName}. All rights
                        reserved.
                    </p>
                </div>
            </footer>
        </div>
    );
}
