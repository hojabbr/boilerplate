import type { Auth } from '@/types/auth';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            locale: string;
            supportedLocales: Record<
                string,
                {
                    name: string;
                    script?: string;
                    native: string;
                    regional?: string;
                }
            >;
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
