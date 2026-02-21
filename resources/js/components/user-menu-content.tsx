import { Link, router, usePage } from '@inertiajs/react';
import { LogOut, Settings } from 'lucide-react';

type PageProps = { translations?: Record<string, string> };
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { UserInfo } from '@/components/user-info';
import { useMobileNavigation } from '@/hooks/use-mobile-navigation';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';

type Props = {
    user: User;
};

export function UserMenuContent({ user }: Props) {
    const { locale, translations } = usePage().props as {
        locale?: string;
    } & PageProps;
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';
    const cleanup = useMobileNavigation();

    const handleLogout = (e: React.MouseEvent) => {
        e.preventDefault();
        cleanup();
        router.post(
            `${prefix}${logout().url}`,
            {},
            {
                onFinish: () => router.flushAll(),
            },
        );
    };

    return (
        <>
            <DropdownMenuLabel className="p-0 font-normal">
                <div className="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                    <UserInfo user={user} showEmail={true} />
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                <DropdownMenuItem asChild>
                    <Link
                        className="block w-full cursor-pointer"
                        href={`${prefix}${edit().url}`}
                        prefetch
                        onClick={cleanup}
                    >
                        <Settings className="me-2" />
                        {t['nav.settings'] ?? 'Settings'}
                    </Link>
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem asChild>
                <button
                    type="button"
                    className="flex w-full cursor-pointer items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                    onClick={handleLogout}
                    data-test="logout-button"
                >
                    <LogOut className="me-2 size-4" />
                    {t['auth.log_out'] ?? 'Log out'}
                </button>
            </DropdownMenuItem>
        </>
    );
}
