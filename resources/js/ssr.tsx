import { createInertiaApp } from '@inertiajs/react';
import createServer from '@inertiajs/react/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ReactDOMServer from 'react-dom/server';

const appName = import.meta.env.VITE_APP_NAME;

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

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: ReactDOMServer.renderToString,
            title: (title) => title || appName,
            resolve: (name) => resolvePageComponent(pagePath(name), pageGlob),
            setup: ({ App, props }) => {
                return <App {...props} />;
            },
        }),
    { cluster: true },
);
