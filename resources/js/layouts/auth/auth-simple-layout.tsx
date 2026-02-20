import { Link, usePage } from '@inertiajs/react';
import { m } from 'motion/react';
import AppLogoIcon from '@/components/app-logo-icon';
import { BackButton } from '@/components/common/BackButton';
import { pageEnter } from '@/components/common/motion-presets';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
    backHref,
    backLabel,
}: AuthLayoutProps) {
    const { locale } = usePage().props as { locale?: string };
    const prefix = locale ? `/${locale}` : '';

    return (
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
            <div className="w-full max-w-sm">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link
                            href={prefix ? prefix : home.url()}
                            className="flex flex-col items-center gap-2 font-medium"
                        >
                            <div className="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                                <AppLogoIcon className="size-9 fill-current text-foreground" />
                            </div>
                            <span className="sr-only">{title}</span>
                        </Link>

                        <div className="space-y-2 text-center">
                            <h1 className="text-xl font-medium">{title}</h1>
                            <p className="text-center text-sm text-muted-foreground">
                                {description}
                            </p>
                        </div>
                    </div>
                    <m.div {...pageEnter}>{children}</m.div>
                    {backHref && (
                        <div className="flex justify-center pt-2">
                            <BackButton
                                href={backHref}
                                label={backLabel ?? 'Back'}
                            />
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
