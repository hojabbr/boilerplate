import { Head } from '@inertiajs/react';
import { useState } from 'react';
import Lightbox from 'yet-another-react-lightbox';
import Zoom from 'yet-another-react-lightbox/plugins/zoom';
import 'yet-another-react-lightbox/styles.css';
import { Button } from '@/components/ui/button';
import PublicLayout, {
    EMPTY_PUBLIC_FEATURES,
    EMPTY_PUBLIC_SETTINGS,
    type PublicFeatures,
    type PublicSettings,
} from '@/layouts/public-layout';

interface GalleryItem {
    id: number;
    url: string;
    full_url: string;
    thumb_url: string;
}

interface PageData {
    title: string;
    body: string;
    meta_title: string | null;
    meta_description: string | null;
    gallery?: GalleryItem[];
}

export default function PageShow({
    page,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
}: {
    page: PageData;
    settings?: PublicSettings;
    features?: PublicFeatures;
}) {
    const [lightboxOpen, setLightboxOpen] = useState(false);
    const [lightboxIndex, setLightboxIndex] = useState(0);
    const title = page.meta_title || page.title;
    const gallery = page.gallery ?? [];
    const slides = gallery.map((item) => ({
        src: item.full_url || item.url,
    }));

    return (
        <PublicLayout settings={settings} features={features}>
            <Head title={title}>
                {page.meta_description && (
                    <meta name="description" content={page.meta_description} />
                )}
            </Head>
            <article className="mx-auto max-w-3xl">
                <h1 className="mb-4 text-2xl font-semibold text-foreground">
                    {page.title}
                </h1>
                <div
                    className="prose dark:prose-invert"
                    dangerouslySetInnerHTML={{ __html: page.body }}
                />
                {gallery.length > 0 && (
                    <div className="mt-8 grid grid-cols-2 gap-2 sm:grid-cols-3 md:gap-4">
                        {gallery.map((item, index) => (
                            <Button
                                key={item.id}
                                type="button"
                                variant="ghost"
                                className="h-auto p-0"
                                onClick={() => {
                                    setLightboxIndex(index);
                                    setLightboxOpen(true);
                                }}
                            >
                                <img
                                    src={item.thumb_url || item.url}
                                    alt=""
                                    className="size-full rounded-lg object-cover"
                                />
                            </Button>
                        ))}
                    </div>
                )}
            </article>
            {gallery.length > 0 && (
                <Lightbox
                    open={lightboxOpen}
                    close={() => setLightboxOpen(false)}
                    index={lightboxIndex}
                    slides={slides}
                    plugins={[Zoom]}
                />
            )}
        </PublicLayout>
    );
}
