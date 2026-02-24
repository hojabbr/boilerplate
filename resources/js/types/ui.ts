import type { ReactNode } from 'react';

export type AppLayoutProps = {
    children: ReactNode;
};

export type AuthLayoutProps = {
    children?: ReactNode;
    name?: string;
    title?: string;
    description?: string;
};
