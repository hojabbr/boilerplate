import { Head, usePage } from '@inertiajs/react';
import AppearanceTabs from '@/components/appearance-tabs';
import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit as editAppearance } from '@/routes/appearance';
import type { BreadcrumbItem } from '@/types';

export default function Appearance() {
    const { translations } = usePage().props as {
        translations?: Record<string, string>;
    };
    const t = translations ?? {};
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t['settings.appearance_title'] ?? 'Appearance settings',
            href: editAppearance().url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head
                title={t['settings.appearance_title'] ?? 'Appearance settings'}
            />

            <h1 className="sr-only">
                {t['settings.appearance_heading'] ?? 'Appearance Settings'}
            </h1>

            <SettingsLayout>
                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title={
                            t['settings.appearance_title'] ??
                            'Appearance settings'
                        }
                        description={
                            t['settings.appearance_description'] ??
                            "Update your account's appearance settings"
                        }
                    />
                    <AppearanceTabs />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
