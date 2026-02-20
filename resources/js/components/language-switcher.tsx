import { usePage } from '@inertiajs/react';
import { Globe } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';

type LocaleSwitchUrl = {
    code: string;
    name: string;
    native: string;
    url: string;
};

export default function LanguageSwitcher({
    className,
    variant = 'ghost',
    size = 'icon',
}: {
    className?: string;
    variant?:
        | 'ghost'
        | 'outline'
        | 'link'
        | 'default'
        | 'secondary'
        | 'destructive';
    size?: 'default' | 'sm' | 'lg' | 'icon';
}) {
    const { locale, locale_switch_urls } = usePage().props as {
        locale: string;
        locale_switch_urls?: LocaleSwitchUrl[];
    };

    const urls = locale_switch_urls ?? [];

    if (urls.length <= 1) {
        return null;
    }

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant={variant}
                    size={size}
                    className={cn(className)}
                    aria-label="Change language"
                >
                    <Globe className="size-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="min-w-[10rem]">
                {urls.map(({ code, native: nativeName, name, url }) => (
                    <DropdownMenuItem key={code} asChild>
                        <a
                            href={url}
                            className={cn(locale === code && 'bg-muted')}
                            aria-current={locale === code ? 'true' : undefined}
                        >
                            {nativeName || name || code}
                        </a>
                    </DropdownMenuItem>
                ))}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
