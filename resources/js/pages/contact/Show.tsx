import { Head, useForm } from '@inertiajs/react';
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

export default function ContactShow({
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    success,
    contactStoreUrl,
}: {
    settings?: PublicSettings & {
        email?: string | null;
        phone?: string | null;
    };
    features?: PublicFeatures;
    success?: string;
    contactStoreUrl: string;
}) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        subject: '',
        message: '',
    });

    return (
        <PublicLayout settings={settings} features={features}>
            <Head title="Contact" />
            <div className="mx-auto max-w-xl">
                <h1 className="mb-6 text-2xl font-semibold text-foreground">
                    Contact us
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
                        <Label htmlFor="name">Name</Label>
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
                        <Label htmlFor="email">Email</Label>
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
                        <Label htmlFor="subject">Subject</Label>
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
                        <Label htmlFor="message">Message</Label>
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
                        Send
                    </Button>
                </form>
                {(settings.email || settings.phone) && (
                    <div className="mt-8 border-t border-border pt-6">
                        <p className="text-sm text-muted-foreground">
                            {settings.email && <>Email: {settings.email}</>}
                            {settings.email && settings.phone && ' • '}
                            {settings.phone && <>Phone: {settings.phone}</>}
                        </p>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
