// Components
import { Form, Head, usePage } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import { email } from '@/routes/password';

export default function ForgotPassword({ status }: { status?: string }) {
    const { translations, locale } = usePage().props as {
        translations?: Record<string, string>;
        locale?: string;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';

    return (
        <AuthLayout
            title={t['auth.forgot_title'] ?? 'Forgot password'}
            description={
                t['auth.forgot_description'] ??
                'Enter your email to receive a password reset link'
            }
            backHref={`${prefix}${login.url()}`}
            backLabel={t['auth.login'] ?? 'Back to login'}
        >
            <Head title={t['auth.forgot_title'] ?? 'Forgot password'} />

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <div className="space-y-6">
                <Form
                    {...email.form()}
                    action={`${prefix}${email.form().action}`}
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="email">
                                    {t['auth.email'] ?? 'Email address'}
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    autoComplete="off"
                                    placeholder={
                                        t['auth.placeholder_email'] ??
                                        'email@example.com'
                                    }
                                />

                                <InputError message={errors.email} />
                            </div>

                            <div className="my-6 flex items-center justify-start">
                                <Button
                                    className="w-full"
                                    disabled={processing}
                                    data-test="email-password-reset-link-button"
                                >
                                    {processing && (
                                        <LoaderCircle className="h-4 w-4 animate-spin" />
                                    )}
                                    {t['auth.email_password_reset_link'] ??
                                        'Email password reset link'}
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <div className="inline-flex flex-wrap items-center justify-center gap-1 text-center text-sm text-muted-foreground">
                    <span>{t['auth.return_to_login'] ?? 'Or, return to'}</span>{' '}
                    <TextLink href={`${prefix}${login.url()}`}>
                        {t['auth.login'] ?? 'log in'}
                    </TextLink>
                </div>
            </div>
        </AuthLayout>
    );
}
