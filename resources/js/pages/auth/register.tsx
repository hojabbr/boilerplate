import { Form, Head, usePage } from '@inertiajs/react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { home, login } from '@/routes';
import { store } from '@/routes/register';

export default function Register() {
    const { translations, locale } = usePage().props as {
        translations?: Record<string, string>;
        locale?: string;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';

    return (
        <AuthLayout
            title={t['auth.register_title'] ?? 'Create an account'}
            description={
                t['auth.register_description'] ??
                'Enter your details below to create your account'
            }
            backHref={prefix ? prefix : home.url()}
            backLabel={t['nav.home'] ?? 'Back to Home'}
        >
            <Head title={t['auth.register'] ?? 'Register'} />
            <Form
                {...store.form()}
                action={`${prefix}${store.form().action}`}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="name">
                                    {t['settings.label_name'] ?? 'Name'}
                                </Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoComplete="name"
                                    name="name"
                                    placeholder={
                                        t['auth.placeholder_full_name'] ??
                                        'Full name'
                                    }
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">
                                    {t['auth.email'] ?? 'Email address'}
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    autoComplete="email"
                                    name="email"
                                    placeholder={
                                        t['auth.placeholder_email'] ??
                                        'email@example.com'
                                    }
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">
                                    {t['auth.password'] ?? 'Password'}
                                </Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder={
                                        t['auth.placeholder_password'] ??
                                        'Password'
                                    }
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    {t['auth.placeholder_confirm_password'] ??
                                        'Confirm password'}
                                </Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder={
                                        t[
                                            'auth.placeholder_confirm_password'
                                        ] ?? 'Confirm password'
                                    }
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                data-test="register-user-button"
                            >
                                {processing && <Spinner />}
                                {t['auth.create_account'] ?? 'Create account'}
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            {t['auth.already_have_account'] ??
                                'Already have an account?'}{' '}
                            <TextLink href={`${prefix}${login.url()}`}>
                                {t['auth.login'] ?? 'Log in'}
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
