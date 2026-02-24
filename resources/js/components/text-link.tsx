import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import type { ComponentProps } from 'react';
import { cn } from '@/lib/utils';

type Props = ComponentProps<typeof Link> & {
    /** Show arrow icon for CTA-style links */
    showArrow?: boolean;
};

export default function TextLink({
    className = '',
    children,
    showArrow = false,
    ...props
}: Props) {
    return (
        <Link
            className={cn(
                'inline-flex items-center gap-1.5 text-foreground underline decoration-border underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current!',
                className,
            )}
            {...props}
        >
            {children}
            {showArrow && (
                <ArrowRight
                    className="size-4 shrink-0 rtl:rotate-180"
                    aria-hidden
                />
            )}
        </Link>
    );
}
