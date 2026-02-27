import { Form } from '@inertiajs/react';
import { Mail, MessageCircle, Phone } from 'lucide-react';
import { m } from 'motion/react';
import { LabeledInputField } from '@/components/common/LabeledInputField';
import { pageEnter } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import SocialLinks from '@/components/common/SocialLinks';
import InputError from '@/components/input-error';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import PublicLayout, {
    EMPTY_PUBLIC_FEATURES,
    EMPTY_PUBLIC_SETTINGS,
    type PublicFeatures,
    type PublicSettings,
} from '@/layouts/public-layout';

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

const EMPTY_CONTACT_MESSAGES: ContactMessages = {};

export default function ContactShow({
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    success,
    contactStoreUrl,
    seo,
    messages = EMPTY_CONTACT_MESSAGES,
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
    const labelEmail = messages.label_email ?? 'Email';
    const labelPhone = messages.label_phone ?? 'Phone';

    return (
        <PublicLayout settings={settings} features={features}>
            <SeoHead
                title={seo?.title ?? messages.heading ?? 'Contact'}
                description={seo?.description}
            />
            <m.div
                className="section-spacing mx-auto max-w-xl px-4 pt-16 sm:px-6"
                {...pageEnter}
            >
                <Card className="overflow-hidden rounded-2xl border border-border shadow-sm">
                    <div className="px-6 pt-6">
                        <h1 className="display-title mb-1 flex items-center gap-3 text-foreground">
                            <MessageCircle
                                className="size-8 shrink-0 text-primary/60"
                                aria-hidden
                            />
                            {messages.heading ?? 'Contact us'}
                        </h1>
                        {seo?.description && (
                            <p className="display-subtitle mt-2 text-muted-foreground">
                                {seo.description}
                            </p>
                        )}
                    </div>
                    <CardContent className="px-6 pt-6 pb-6">
                        {success && (
                            <Alert className="mb-4">
                                <AlertDescription>{success}</AlertDescription>
                            </Alert>
                        )}
                        <Form
                            action={contactStoreUrl}
                            method="post"
                            options={{ preserveScroll: true }}
                            className="space-y-4"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <LabeledInputField
                                        id="name"
                                        label={messages.form_name ?? 'Name'}
                                        type="text"
                                        name="name"
                                        required
                                        autoComplete="name"
                                        defaultValue=""
                                        error={errors.name}
                                    />
                                    <LabeledInputField
                                        id="email"
                                        label={messages.form_email ?? 'Email'}
                                        type="email"
                                        name="email"
                                        required
                                        autoComplete="email"
                                        defaultValue=""
                                        error={errors.email}
                                    />
                                    <LabeledInputField
                                        id="subject"
                                        label={
                                            messages.form_subject ?? 'Subject'
                                        }
                                        type="text"
                                        name="subject"
                                        required
                                        autoComplete="off"
                                        defaultValue=""
                                        error={errors.subject}
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
                                            defaultValue=""
                                        />
                                        <InputError
                                            message={errors.message}
                                            className="text-destructive"
                                        />
                                    </div>
                                    <Button type="submit" disabled={processing}>
                                        {messages.form_send ?? 'Send'}
                                    </Button>
                                </>
                            )}
                        </Form>
                        {(settings.email ||
                            settings.phone ||
                            (settings.social_links &&
                                Object.keys(settings.social_links).length >
                                    0)) && (
                            <div className="mt-8 space-y-4 border-t border-border/60 pt-6">
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
                                    Object.keys(settings.social_links).length >
                                        0 && (
                                        <div className="text-sm">
                                            <p className="mb-2 text-muted-foreground">
                                                {messages.label_social ??
                                                    'Follow us'}
                                            </p>
                                            <SocialLinks
                                                social_links={
                                                    settings.social_links
                                                }
                                                variant="footer"
                                                className="flex flex-wrap justify-start gap-2"
                                            />
                                        </div>
                                    )}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </m.div>
        </PublicLayout>
    );
}
