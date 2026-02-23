import type { Auth } from '@/types/auth';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            locale: string;
            dir: 'ltr' | 'rtl';
            supportedLocales: Record<
                string,
                {
                    name: string;
                    script?: string;
                    native: string;
                    regional?: string;
                    dir?: 'ltr' | 'rtl';
                }
            >;
            supported_locale_codes?: string[];
            locale_switch_urls?: Array<{
                code: string;
                name: string;
                native: string;
                url: string;
            }>;
            [key: string]: unknown;
        };
    }
}
