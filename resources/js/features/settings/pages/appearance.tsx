import { Head, usePage } from '@inertiajs/react';
import AppearanceToggleTabs from '@/components/appearance-toggle-tabs';
import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
export default function Appearance() {
    const { translations } = usePage().props as {
        translations?: Record<string, string>;
    };
    const t = translations ?? {};

    return (
        <AppLayout>
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
                    <AppearanceToggleTabs />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
