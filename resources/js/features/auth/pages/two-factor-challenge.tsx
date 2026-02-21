import { Form, Head, usePage } from '@inertiajs/react';
import { REGEXP_ONLY_DIGITS } from 'input-otp';
import { useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import { OTP_MAX_LENGTH } from '@/hooks/use-two-factor-auth';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import { store } from '@/routes/two-factor/login';

const OTP_SLOT_KEYS = Array.from(
    { length: OTP_MAX_LENGTH },
    (_, i) => `otp-slot-${i}` as const,
);

export default function TwoFactorChallenge() {
    const { translations, locale } = usePage().props as {
        translations?: Record<string, string>;
        locale?: string;
    };
    const t = useMemo(() => translations ?? {}, [translations]);
    const prefix = locale ? `/${locale}` : '';
    const [showRecoveryInput, setShowRecoveryInput] = useState<boolean>(false);
    const [code, setCode] = useState<string>('');

    const authConfigContent = useMemo<{
        title: string;
        description: string;
        toggleText: string;
    }>(() => {
        if (showRecoveryInput) {
            return {
                title: t['auth.two_factor_recovery_title'] ?? 'Recovery Code',
                description:
                    t['auth.two_factor_recovery_description'] ??
                    'Please confirm access to your account by entering one of your emergency recovery codes.',
                toggleText:
                    t['auth.toggle_authentication_code'] ??
                    'login using an authentication code',
            };
        }

        return {
            title: t['auth.two_factor_code_title'] ?? 'Authentication Code',
            description:
                t['auth.two_factor_code_description'] ??
                'Enter the authentication code provided by your authenticator application.',
            toggleText:
                t['auth.toggle_recovery_code'] ?? 'login using a recovery code',
        };
    }, [showRecoveryInput, t]);

    const toggleRecoveryMode = (clearErrors: () => void): void => {
        setShowRecoveryInput(!showRecoveryInput);
        clearErrors();
        setCode('');
    };

    return (
        <AuthLayout
            title={authConfigContent.title}
            description={authConfigContent.description}
            backHref={`${prefix}${login.url()}`}
            backLabel={t['auth.login'] ?? 'Back to login'}
        >
            <Head
                title={
                    t['auth.two_factor_title'] ?? 'Two-Factor Authentication'
                }
            />

            <div className="space-y-6">
                <Form
                    {...store.form()}
                    action={`${prefix}${store.form().action}`}
                    className="space-y-4"
                    resetOnError
                    resetOnSuccess={!showRecoveryInput}
                >
                    {({ errors, processing, clearErrors }) => (
                        <>
                            {showRecoveryInput ? (
                                <>
                                    <Input
                                        name="recovery_code"
                                        type="text"
                                        placeholder={
                                            t[
                                                'auth.placeholder_recovery_code'
                                            ] ?? 'Enter recovery code'
                                        }
                                        required
                                    />
                                    <InputError
                                        message={errors.recovery_code}
                                    />
                                </>
                            ) : (
                                <div className="flex flex-col items-center justify-center space-y-3 text-center">
                                    <div className="flex w-full items-center justify-center">
                                        <InputOTP
                                            name="code"
                                            maxLength={OTP_MAX_LENGTH}
                                            value={code}
                                            onChange={(value) => setCode(value)}
                                            disabled={processing}
                                            pattern={REGEXP_ONLY_DIGITS}
                                        >
                                            <InputOTPGroup>
                                                {OTP_SLOT_KEYS.map(
                                                    (key, index) => (
                                                        <InputOTPSlot
                                                            key={key}
                                                            index={index}
                                                        />
                                                    ),
                                                )}
                                            </InputOTPGroup>
                                        </InputOTP>
                                    </div>
                                    <InputError message={errors.code} />
                                </div>
                            )}

                            <Button
                                type="submit"
                                className="w-full"
                                disabled={processing}
                            >
                                {t['auth.continue'] ?? 'Continue'}
                            </Button>

                            <div className="text-center text-sm text-muted-foreground">
                                <span>or you can </span>
                                <button
                                    type="button"
                                    className="cursor-pointer text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                    onClick={() =>
                                        toggleRecoveryMode(clearErrors)
                                    }
                                >
                                    {authConfigContent.toggleText}
                                </button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </AuthLayout>
    );
}
