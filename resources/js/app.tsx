import { createInertiaApp } from '@inertiajs/react';
import { configureEcho } from '@laravel/echo-react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { DirectionProvider } from '@/components/ui/direction';
import '../css/app.css';
import { initializeTheme } from './hooks/use-appearance';
import i18n from './i18n';

configureEcho({
    broadcaster: 'reverb',
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const rtlLocales = new Set(['ar', 'fa']);

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.tsx`,
            import.meta.glob('./pages/**/*.tsx'),
        ),
    setup({ el, App, props }) {
        const { locale, dir } = props.initialPage.props as {
            locale?: string;
            dir?: 'ltr' | 'rtl';
        };
        if (locale) {
            void i18n.changeLanguage(locale);
            document.documentElement.lang = locale;
        }
        const resolvedDir =
            dir ?? (locale && rtlLocales.has(locale) ? 'rtl' : 'ltr');
        document.documentElement.setAttribute('dir', resolvedDir);

        const root = createRoot(el);

        root.render(
            <StrictMode>
                <DirectionProvider dir={resolvedDir}>
                    <App {...props} />
                </DirectionProvider>
            </StrictMode>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
