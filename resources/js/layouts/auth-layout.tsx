import AuthLayoutTemplate from '@/layouts/auth/auth-simple-layout';

export default function AuthLayout({
    children,
    title,
    description,
    backHref,
    backLabel,
    ...props
}: {
    children: React.ReactNode;
    title: string;
    description: string;
    backHref?: string;
    backLabel?: string;
}) {
    return (
        <AuthLayoutTemplate
            title={title}
            description={description}
            backHref={backHref}
            backLabel={backLabel}
            {...props}
        >
            {children}
        </AuthLayoutTemplate>
    );
}
