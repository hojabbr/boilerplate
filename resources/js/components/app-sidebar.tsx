import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid } from 'lucide-react';
import LanguageSwitcher from '@/components/language-switcher';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import ThemeSwitcher from '@/components/theme-switcher';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
import AppLogo from './app-logo';

export function AppSidebar() {
    const { locale, translations } = usePage().props as {
        locale?: string;
        translations?: Record<string, string>;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';
    const mainNavItems: NavItem[] = [
        {
            title: t['sidebar.dashboard'] ?? 'Dashboard',
            href: `${prefix}${dashboard.url()}`,
            icon: LayoutGrid,
        },
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={`${prefix}${dashboard.url()}`} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <div className="flex items-center justify-center gap-1 py-2">
                    <ThemeSwitcher variant="ghost" size="icon" />
                    <LanguageSwitcher variant="ghost" size="icon" />
                </div>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
