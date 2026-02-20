import { Link } from '@inertiajs/react';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-react';
import { buttonVariants } from '@/components/ui/button';
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
    PaginationNext,
    PaginationPrevious,
} from '@/components/ui/pagination';
import { cn } from '@/lib/utils';

export interface PaginatorLinkItem {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatorLinksProps {
    links: PaginatorLinkItem[];
    currentPage: number;
    lastPage: number;
    /** Optional "Page X of Y" label; true = default label, string = custom, false = hide */
    pageLabel?: string | boolean;
    /** Previous button label (for RTL/i18n). Default "Previous". */
    previousText?: string;
    /** Next button label (for RTL/i18n). Default "Next". */
    nextText?: string;
}

/**
 * Renders Laravel pagination using shadcn/ui Pagination and Inertia Link.
 * Expects the `links` array from a Laravel LengthAwarePaginator (e.g. posts.links).
 * @see https://ui.shadcn.com/docs/components/radix/pagination
 */
export function PaginatorLinks({
    links,
    currentPage,
    lastPage,
    pageLabel = true,
    previousText = 'Previous',
    nextText = 'Next',
}: PaginatorLinksProps) {
    if (lastPage <= 1) {
        return null;
    }

    const prevLink = links[0];
    const nextLink = links[links.length - 1];
    const pageLinks = links.slice(1, -1);

    const disabledButtonClass = cn(
        buttonVariants({ variant: 'ghost', size: 'default' }),
        'pointer-events-none gap-1 px-2.5 opacity-50 sm:ps-2.5',
    );
    const disabledNextClass = cn(
        buttonVariants({ variant: 'ghost', size: 'default' }),
        'pointer-events-none gap-1 px-2.5 opacity-50 sm:pe-2.5',
    );

    return (
        <nav
            className="flex flex-col items-center gap-4"
            aria-label="pagination"
        >
            {pageLabel && (
                <p className="text-sm text-muted-foreground">
                    Page {currentPage} of {lastPage}
                </p>
            )}
            <Pagination>
                <PaginationContent>
                    <PaginationItem>
                        {prevLink?.url ? (
                            <PaginationPrevious asChild text={previousText}>
                                <Link href={prevLink.url}>
                                    <ChevronLeftIcon className="size-4 rtl:rotate-180" />
                                    <span className="hidden sm:block">
                                        {previousText}
                                    </span>
                                </Link>
                            </PaginationPrevious>
                        ) : (
                            <span
                                className={disabledButtonClass}
                                aria-disabled="true"
                                aria-label="Go to previous page"
                            >
                                <ChevronLeftIcon className="size-4 rtl:rotate-180" />
                                <span className="hidden sm:block">
                                    {previousText}
                                </span>
                            </span>
                        )}
                    </PaginationItem>
                    {pageLinks.map((link, index) => {
                        if (link.label === '...') {
                            const prevUrl =
                                pageLinks[index - 1]?.url ?? 'start';
                            const nextUrl = pageLinks[index + 1]?.url ?? 'end';
                            return (
                                <PaginationItem
                                    key={`ellipsis-${prevUrl}-${nextUrl}`}
                                >
                                    <PaginationEllipsis />
                                </PaginationItem>
                            );
                        }
                        const pageNum = link.label;
                        return (
                            <PaginationItem key={link.url ?? `page-${pageNum}`}>
                                {link.url ? (
                                    <PaginationLink
                                        isActive={link.active}
                                        asChild
                                    >
                                        <Link href={link.url}>{pageNum}</Link>
                                    </PaginationLink>
                                ) : (
                                    <PaginationLink
                                        isActive={link.active}
                                        aria-current={
                                            link.active ? 'page' : undefined
                                        }
                                    >
                                        {pageNum}
                                    </PaginationLink>
                                )}
                            </PaginationItem>
                        );
                    })}
                    <PaginationItem>
                        {nextLink?.url ? (
                            <PaginationNext asChild text={nextText}>
                                <Link href={nextLink.url}>
                                    <span className="hidden sm:block">
                                        {nextText}
                                    </span>
                                    <ChevronRightIcon className="size-4 rtl:rotate-180" />
                                </Link>
                            </PaginationNext>
                        ) : (
                            <span
                                className={disabledNextClass}
                                aria-disabled="true"
                                aria-label="Go to next page"
                            >
                                <span className="hidden sm:block">
                                    {nextText}
                                </span>
                                <ChevronRightIcon className="size-4 rtl:rotate-180" />
                            </span>
                        )}
                    </PaginationItem>
                </PaginationContent>
            </Pagination>
        </nav>
    );
}
