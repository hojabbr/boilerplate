import { useForm } from '@inertiajs/react';
import { SeoHead } from '@/components/common/SeoHead';
import InputError from '@/components/input-error';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
    };
    features?: PublicFeatures;
    success?: string;
    contactStoreUrl: string;
    seo?: Seo;
    messages?: ContactMessages;
}) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        subject: '',
        message: '',
    });
    const labelEmail = messages.label_email ?? 'Email';
    const labelPhone = messages.label_phone ?? 'Phone';

    return (
        <PublicLayout settings={settings} features={features}>
            <SeoHead
                title={seo?.title ?? 'Contact'}
                description={seo?.description}
            />
            <div className="mx-auto max-w-xl">
                <h1 className="mb-6 text-2xl font-semibold text-foreground">
                    {messages.heading ?? 'Contact us'}
                </h1>
                {success && (
                    <Alert className="mb-4">
                        <AlertDescription>{success}</AlertDescription>
                    </Alert>
                )}
                {/* action/method provide no-JS submit; preventDefault + post() for Inertia SPA */}
                <form
                    action={contactStoreUrl}
                    method="post"
                    onSubmit={(e) => {
                        e.preventDefault();
                        post(contactStoreUrl);
                    }}
                    className="space-y-4"
                >
                    <div className="grid gap-2">
                        <Label htmlFor="name">
                            {messages.form_name ?? 'Name'}
                        </Label>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                        />
                        <InputError
                            message={errors.name}
                            className="text-destructive"
                        />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="email">
                            {messages.form_email ?? 'Email'}
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                        />
                        <InputError
                            message={errors.email}
                            className="text-destructive"
                        />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="subject">
                            {messages.form_subject ?? 'Subject'}
                        </Label>
                        <Input
                            id="subject"
                            type="text"
                            value={data.subject}
                            onChange={(e) => setData('subject', e.target.value)}
                        />
                        <InputError
                            message={errors.subject}
                            className="text-destructive"
                        />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="message">
                            {messages.form_message ?? 'Message'}
                        </Label>
                        <Textarea
                            id="message"
                            rows={5}
                            value={data.message}
                            onChange={(e) => setData('message', e.target.value)}
                        />
                        <InputError
                            message={errors.message}
                            className="text-destructive"
                        />
                    </div>
                    <Button type="submit" disabled={processing}>
                        {messages.form_send ?? 'Send'}
                    </Button>
                </form>
                {(settings.email || settings.phone) && (
                    <div className="mt-8 border-t border-border pt-6">
                        <p className="text-sm text-muted-foreground">
                            {settings.email && (
                                <>
                                    {labelEmail}: {settings.email}
                                </>
                            )}
                            {settings.email && settings.phone && ' • '}
                            {settings.phone && (
                                <>
                                    {labelPhone}: {settings.phone}
                                </>
                            )}
                        </p>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
