import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppShell } from '@/components/app-shell';
import type { AppLayoutProps } from '@/types';

export default function AppHeaderLayout({ children }: AppLayoutProps) {
    return (
        <AppShell>
            <AppHeader />
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
