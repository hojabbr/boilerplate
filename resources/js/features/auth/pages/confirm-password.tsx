import { Form, Head, usePage } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { home } from '@/routes';
import { store } from '@/routes/password/confirm';

export default function ConfirmPassword() {
    const { translations, locale } = usePage().props as {
        translations?: Record<string, string>;
        locale?: string;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';

    return (
        <AuthLayout
            title={t['auth.confirm_title'] ?? 'Confirm your password'}
            description={
                t['auth.confirm_description'] ??
                'This is a secure area of the application. Please confirm your password before continuing.'
            }
            backHref={prefix ? prefix : home.url()}
            backLabel={t['nav.home'] ?? 'Back to Home'}
        >
            <Head title={t['auth.confirm_title'] ?? 'Confirm password'} />

            <Form
                {...store.form()}
                action={`${prefix}${store.form().action}`}
                resetOnSuccess={['password']}
            >
                {({ processing, errors }) => (
                    <div className="space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="password">
                                {t['auth.password'] ?? 'Password'}
                            </Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                placeholder={
                                    t['auth.placeholder_password'] ?? 'Password'
                                }
                                autoComplete="current-password"
                            />

                            <InputError message={errors.password} />
                        </div>

                        <div className="flex items-center">
                            <Button
                                className="w-full"
                                disabled={processing}
                                data-test="confirm-password-button"
                            >
                                {processing && <Spinner />}
                                {t['auth.confirm_password_button'] ??
                                    'Confirm password'}
                            </Button>
                        </div>
                    </div>
                )}
            </Form>
        </AuthLayout>
    );
}
