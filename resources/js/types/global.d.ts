import type { Auth } from '@/types/auth';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            locale: string;
            supportedLocales: Record<string, { name: string; script?: string; native: string; regional?: string }>;
            [key: string]: unknown;
        };
    }
}
