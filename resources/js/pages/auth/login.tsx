import { Form, Head, usePage } from '@inertiajs/react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

type Props = {
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
};

export default function Login({
    status,
    canResetPassword,
    canRegister,
}: Props) {
    const { translations, locale } = usePage().props as {
        translations?: Record<string, string>;
        locale?: string;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';

    return (
        <AuthLayout
            title={t['auth.login_title'] ?? 'Log in to your account'}
            description={
                t['auth.login_description'] ??
                'Enter your email and password below to log in'
            }
        >
            <Head title={t['auth.login'] ?? 'Log in'} />

            <Form
                {...store.form()}
                action={`${prefix}${store.form().action}`}
                resetOnSuccess={['password']}
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="email">
                                    {t['auth.email'] ?? 'Email address'}
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoComplete="email"
                                    placeholder={
                                        t['auth.placeholder_email'] ??
                                        'email@example.com'
                                    }
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <div className="flex items-center">
                                    <Label htmlFor="password">
                                        {t['auth.password'] ?? 'Password'}
                                    </Label>
                                    {canResetPassword && (
                                        <TextLink
                                            href={`${prefix}${request().url}`}
                                            className="ml-auto text-sm"
                                        >
                                            {t['auth.forgot_password'] ??
                                                'Forgot password?'}
                                        </TextLink>
                                    )}
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autoComplete="current-password"
                                    placeholder={
                                        t['auth.placeholder_password'] ??
                                        'Password'
                                    }
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="flex items-center space-x-3">
                                <Checkbox id="remember" name="remember" />
                                <Label htmlFor="remember">
                                    {t['auth.remember_me'] ?? 'Remember me'}
                                </Label>
                            </div>

                            <Button
                                type="submit"
                                className="mt-4 w-full"
                                disabled={processing}
                                data-test="login-button"
                            >
                                {processing && <Spinner />}
                                {t['auth.login'] ?? 'Log in'}
                            </Button>
                        </div>

                        {canRegister && (
                            <div className="text-center text-sm text-muted-foreground">
                                {t['auth.no_account'] ??
                                    "Don't have an account?"}{' '}
                                <TextLink href={`${prefix}${register.url()}`}>
                                    {t['auth.sign_up'] ?? 'Sign up'}
                                </TextLink>
                            </div>
                        )}
                    </>
                )}
            </Form>

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}
        </AuthLayout>
    );
}
