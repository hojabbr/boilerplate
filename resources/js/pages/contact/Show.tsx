import { useForm, usePage } from '@inertiajs/react';
import { Mail, Phone } from 'lucide-react';
import { m } from 'motion/react';
import { BackButton } from '@/components/common/BackButton';
import { LabeledInputField } from '@/components/common/LabeledInputField';
import { pageEnter } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import SocialLinks from '@/components/common/SocialLinks';
import InputError from '@/components/input-error';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import PublicLayout, {
    EMPTY_PUBLIC_FEATURES,
    EMPTY_PUBLIC_SETTINGS,
    type PublicFeatures,
    type PublicSettings,
} from '@/layouts/public-layout';
import { home } from '@/routes';
import contact from '@/routes/contact';

interface Seo {
    title: string;
    description?: string;
}

interface ContactMessages {
    heading?: string;
    form_name?: string;
    form_email?: string;
    form_subject?: string;
    form_message?: string;
    form_send?: string;
    label_email?: string;
    label_phone?: string;
    label_social?: string;
}

export default function ContactShow({
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    success,
    contactStoreUrl,
    seo,
    messages = {},
}: {
    settings?: PublicSettings & {
        email?: string | null;
        phone?: string | null;
        social_links?: Record<string, string>;
    };
    features?: PublicFeatures;
    success?: string;
    contactStoreUrl: string;
    seo?: Seo;
    messages?: ContactMessages;
}) {
    const { locale, translations } = usePage().props as {
        locale: string;
        translations?: Record<string, string>;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';
    const breadcrumbs = [
        { title: t['nav.home'] ?? 'Home', href: prefix ? prefix : home.url() },
        {
            title: messages.heading ?? t['nav.contact'] ?? 'Contact',
            href: `${prefix}${contact.show.url()}`,
        },
    ];

    const form = useForm('post', contactStoreUrl, {
        name: '',
        email: '',
        subject: '',
        message: '',
    });
    const labelEmail = messages.label_email ?? 'Email';
    const labelPhone = messages.label_phone ?? 'Phone';

    return (
        <PublicLayout
            breadcrumbs={breadcrumbs}
            settings={settings}
            features={features}
        >
            <SeoHead
                title={seo?.title ?? 'Contact'}
                description={seo?.description}
            />
            <div className="mb-4">
                <BackButton
                    href={prefix ? prefix : home.url()}
                    label={
                        t['nav.home']
                            ? `Back to ${t['nav.home']}`
                            : 'Back to Home'
                    }
                />
            </div>
            <m.div className="mx-auto max-w-xl" {...pageEnter}>
                <h1 className="mb-6 text-2xl font-semibold text-foreground">
                    {messages.heading ?? 'Contact us'}
                </h1>
                {success && (
                    <Alert className="mb-4">
                        <AlertDescription>{success}</AlertDescription>
                    </Alert>
                )}
                <form
                    action={contactStoreUrl}
                    method="post"
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post(contactStoreUrl, { preserveScroll: true });
                    }}
                    className="space-y-4"
                >
                    <LabeledInputField
                        id="name"
                        label={messages.form_name ?? 'Name'}
                        type="text"
                        name="name"
                        required
                        autoComplete="name"
                        value={form.data.name}
                        onChange={(e) => form.setData('name', e.target.value)}
                        error={form.errors.name}
                    />
                    <LabeledInputField
                        id="email"
                        label={messages.form_email ?? 'Email'}
                        type="email"
                        name="email"
                        required
                        autoComplete="email"
                        value={form.data.email}
                        onChange={(e) => form.setData('email', e.target.value)}
                        error={form.errors.email}
                    />
                    <LabeledInputField
                        id="subject"
                        label={messages.form_subject ?? 'Subject'}
                        type="text"
                        name="subject"
                        required
                        autoComplete="off"
                        value={form.data.subject}
                        onChange={(e) =>
                            form.setData('subject', e.target.value)
                        }
                        error={form.errors.subject}
                    />
                    <div className="grid gap-2">
                        <Label htmlFor="message">
                            {messages.form_message ?? 'Message'}
                        </Label>
                        <Textarea
                            id="message"
                            name="message"
                            rows={5}
                            required
                            value={form.data.message}
                            onChange={(e) =>
                                form.setData('message', e.target.value)
                            }
                        />
                        <InputError
                            message={form.errors.message}
                            className="text-destructive"
                        />
                    </div>
                    <Button type="submit" disabled={form.processing}>
                        {messages.form_send ?? 'Send'}
                    </Button>
                </form>
                {(settings.email ||
                    settings.phone ||
                    (settings.social_links &&
                        Object.keys(settings.social_links).length > 0)) && (
                    <div className="mt-8 space-y-4 border-t border-border pt-6">
                        {(settings.email || settings.phone) && (
                            <div className="flex flex-col gap-3 text-sm text-muted-foreground">
                                {settings.email && (
                                    <a
                                        href={`mailto:${settings.email}`}
                                        className="flex items-center gap-3 rounded hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                                    >
                                        <Mail
                                            className="size-4 shrink-0"
                                            aria-hidden
                                        />
                                        <span>
                                            {labelEmail}:{' '}
                                            <span className="text-foreground underline underline-offset-2">
                                                {settings.email}
                                            </span>
                                        </span>
                                    </a>
                                )}
                                {settings.phone && (
                                    <a
                                        href={`tel:${settings.phone.replace(/\s/g, '')}`}
                                        className="flex items-center gap-3 rounded hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                                    >
                                        <Phone
                                            className="size-4 shrink-0"
                                            aria-hidden
                                        />
                                        <span>
                                            {labelPhone}:{' '}
                                            <span className="text-foreground underline underline-offset-2">
                                                {settings.phone}
                                            </span>
                                        </span>
                                    </a>
                                )}
                            </div>
                        )}
                        {settings.social_links &&
                            Object.keys(settings.social_links).length > 0 && (
                                <div className="text-sm">
                                    <p className="mb-2 text-muted-foreground">
                                        {messages.label_social ?? 'Follow us'}
                                    </p>
                                    <SocialLinks
                                        social_links={settings.social_links}
                                        variant="footer"
                                        className="flex flex-wrap justify-start gap-2"
                                    />
                                </div>
                            )}
                    </div>
                )}
            </m.div>
        </PublicLayout>
    );
}
