import { m } from 'motion/react';
import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { pageEnter } from '@/components/common/motion-presets';
import type { AppLayoutProps } from '@/types';

const EMPTY_BREADCRUMBS: AppLayoutProps['breadcrumbs'] = [];

export default function AppSidebarLayout({
    children,
    breadcrumbs = EMPTY_BREADCRUMBS,
}: AppLayoutProps) {
    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                <m.div {...pageEnter}>{children}</m.div>
            </AppContent>
        </AppShell>
    );
}
