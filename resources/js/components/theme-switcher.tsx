import { Monitor, Moon, Sun } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { Appearance } from '@/hooks/use-appearance';
import { useAppearance } from '@/hooks/use-appearance';
import { cn } from '@/lib/utils';

const options: { value: Appearance; icon: typeof Sun; label: string }[] = [
    { value: 'light', icon: Sun, label: 'Light' },
    { value: 'dark', icon: Moon, label: 'Dark' },
    { value: 'system', icon: Monitor, label: 'System' },
];

export default function ThemeSwitcher({
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
    const { appearance, updateAppearance } = useAppearance();
    const current = options.find((o) => o.value === appearance) ?? options[2];
    const CurrentIcon = current.icon;

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant={variant}
                    size={size}
                    className={cn(className)}
                    aria-label="Toggle theme"
                >
                    <CurrentIcon className="size-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="min-w-[8rem]">
                {options.map(({ value, icon: Icon, label }) => (
                    <DropdownMenuItem
                        key={value}
                        onClick={() => updateAppearance(value)}
                        className={cn(appearance === value && 'bg-muted')}
                    >
                        <Icon className="size-4" />
                        <span>{label}</span>
                    </DropdownMenuItem>
                ))}
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
