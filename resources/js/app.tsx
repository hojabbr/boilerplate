import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';
import { initializeTheme } from './hooks/use-appearance';
import { configureEcho } from '@laravel/echo-react';
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
        const locale = (props.initialPage.props as { locale?: string }).locale;
        if (locale) {
            void i18n.changeLanguage(locale);
            document.documentElement.lang = locale;
            document.documentElement.setAttribute(
                'dir',
                rtlLocales.has(locale) ? 'rtl' : 'ltr',
            );
        }

        const root = createRoot(el);

        root.render(
            <StrictMode>
                <App {...props} />
            </StrictMode>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
