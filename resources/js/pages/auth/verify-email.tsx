// Components
import { Form, Head, router, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { login, logout } from '@/routes';
import { send } from '@/routes/verification';

export default function VerifyEmail({ status }: { status?: string }) {
    const { translations, locale } = usePage().props as {
        translations?: Record<string, string>;
        locale?: string;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';

    return (
        <AuthLayout
            title={t['auth.verify_title'] ?? 'Verify email'}
            description={
                t['auth.verify_description'] ??
                'Please verify your email address by clicking on the link we just emailed to you.'
            }
            backHref={`${prefix}${login.url()}`}
            backLabel={t['auth.login'] ?? 'Back to login'}
        >
            <Head title={t['auth.verify_title'] ?? 'Email verification'} />

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {t['auth.verification_link_sent_registration'] ??
                        'A new verification link has been sent to the email address you provided during registration.'}
                </div>
            )}

            <Form
                {...send.form()}
                action={`${prefix}${send.form().action}`}
                className="space-y-6 text-center"
            >
                {({ processing }) => (
                    <>
                        <Button disabled={processing} variant="secondary">
                            {processing && <Spinner />}
                            {t['auth.resend_verification_email'] ??
                                'Resend verification email'}
                        </Button>

                        <Button
                            type="button"
                            variant="link"
                            className="mx-auto text-sm"
                            onClick={() =>
                                router.post(`${prefix}${logout().url}`)
                            }
                        >
                            {t['auth.log_out'] ?? 'Log out'}
                        </Button>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
