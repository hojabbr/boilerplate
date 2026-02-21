import { createInertiaApp } from '@inertiajs/react';
import { configureEcho } from '@laravel/echo-react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { LazyMotion, domAnimation, MotionConfig } from 'motion/react';
import { StrictMode } from 'react';
import { hydrateRoot } from 'react-dom/client';
import { DirectionProvider } from '@/components/ui/direction';
import '../css/app.css';
import { initializeTheme } from './hooks/use-appearance';
import i18n from './i18n';

configureEcho({
    broadcaster: 'reverb',
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const rtlLocales = new Set(['ar', 'fa']);

const pageGlob = {
    ...import.meta.glob('./pages/**/*.tsx'),
    ...import.meta.glob('./features/*/pages/**/*.tsx'),
};

function pagePath(name: string): string {
    if (name === 'welcome') return './features/landing/pages/welcome.tsx';
    const parts = name.split('/');
    const feature = parts[0];
    const pageFile = parts.length > 1 ? parts.slice(1).join('/') : name;
    const featurePath = `./features/${feature}/pages/${pageFile}.tsx`;
    return featurePath in pageGlob ? featurePath : `./pages/${name}.tsx`;
}

createInertiaApp({
    title: (title) => title || appName,
    resolve: (name) => resolvePageComponent(pagePath(name), pageGlob),
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

        hydrateRoot(
            el,
            <StrictMode>
                <LazyMotion features={domAnimation} strict>
                    <MotionConfig reducedMotion="user">
                        <DirectionProvider dir={resolvedDir}>
                            <App {...props} />
                        </DirectionProvider>
                    </MotionConfig>
                </LazyMotion>
            </StrictMode>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
