import { Head, usePage } from '@inertiajs/react';
import { m } from 'motion/react';
import { fadeInUpView } from '@/components/common/motion-presets';
import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

export default function Dashboard() {
    const { translations, messages, locale } = usePage().props as {
        translations?: Record<string, string>;
        messages?: { title?: string };
        locale?: string;
    };
    const prefix = locale ? `/${locale}` : '';
    const title =
        messages?.title ?? translations?.['common.dashboard'] ?? 'Dashboard';
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title,
            href: `${prefix}${dashboard.url()}`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={title} />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    {(
                        [
                            'dashboard-card-1',
                            'dashboard-card-2',
                            'dashboard-card-3',
                        ] as const
                    ).map((cardKey, i) => (
                        <m.div
                            key={cardKey}
                            className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
                            {...fadeInUpView}
                            transition={{
                                duration: 0.4,
                                ease: 'easeOut' as const,
                                delay: i * 0.06,
                            }}
                        >
                            <PlaceholderPattern className="absolute inset-0 size-full stroke-foreground/20" />
                        </m.div>
                    ))}
                </div>
                <m.div
                    className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
                    {...fadeInUpView}
                >
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-foreground/20" />
                </m.div>
            </div>
        </AppLayout>
    );
}
