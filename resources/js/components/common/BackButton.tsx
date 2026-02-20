import { Link } from '@inertiajs/react';
import { ChevronLeftIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

interface BackButtonProps {
    href: string;
    label?: string;
    className?: string;
}

/**
 * Consistent back navigation: ghost button with chevron (RTL-aware).
 * Use for drill-down pages (e.g. "Back to Blog", "Back to Home").
 */
export function BackButton({ href, label, className }: BackButtonProps) {
    const ariaLabel = label ?? 'Go back';
    return (
        <Button
            variant="ghost"
            size="sm"
            className={cn('-ms-1 gap-1', className)}
            asChild
        >
            <Link href={href} aria-label={ariaLabel}>
                <ChevronLeftIcon className="size-4 rtl:rotate-180" />
                {label != null && (
                    <span className="hidden sm:inline">{label}</span>
                )}
            </Link>
        </Button>
    );
}
