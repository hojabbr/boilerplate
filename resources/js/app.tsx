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
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) => resolvePageComponent(pagePath(name), pageGlob),
    setup({ el, App, props }) {
        const { locale, dir, supported_locale_codes } = props.initialPage
            .props as {
            locale?: string;
            dir?: 'ltr' | 'rtl';
            supported_locale_codes?: string[];
        };
        if (locale) {
            void i18n.changeLanguage(locale);
            document.documentElement.lang = locale;
        }
        if (
            supported_locale_codes &&
            Array.isArray(supported_locale_codes) &&
            supported_locale_codes.length > 0
        ) {
            i18n.store.options.supportedLngs = supported_locale_codes;
        }
        const resolvedDir = dir ?? 'ltr';
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
    progress: false,
});

// This will set light / dark mode on load...
initializeTheme();
