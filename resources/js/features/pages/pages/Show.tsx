import { FileText } from 'lucide-react';
import { m } from 'motion/react';
import { useState } from 'react';
import Lightbox from 'yet-another-react-lightbox';
import Zoom from 'yet-another-react-lightbox/plugins/zoom';
import 'yet-another-react-lightbox/styles.css';
import { fadeInUpView } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import { Button } from '@/components/ui/button';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';
import PublicLayout, {
    EMPTY_PUBLIC_FEATURES,
    EMPTY_PUBLIC_SETTINGS,
    type PublicFeatures,
    type PublicSettings,
} from '@/layouts/public-layout';
import { decodeHtml } from '@/lib/utils';

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

interface Seo {
    title: string;
    description?: string | null;
    image?: string | null;
}

export default function PageShow({
    page,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
}: {
    page: PageData;
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
}) {
    const [lightboxOpen, setLightboxOpen] = useState(false);
    const [lightboxIndex, setLightboxIndex] = useState(0);
    const gallery = page.gallery ?? [];
    const slides = gallery.map((item) => ({
        src: item.full_url || item.url,
    }));
    const heroImage = gallery[0];
    const showGallery = gallery.length > 1;

    return (
        <PublicLayout
            contentVariant="full-bleed"
            settings={settings}
            features={features}
        >
            <SeoHead
                title={seo?.title ?? page.meta_title ?? page.title}
                description={seo?.description ?? page.meta_description}
                image={seo?.image}
            />
            {heroImage ? (
                <div className="relative flex min-h-[50vh] w-full items-center justify-center overflow-hidden bg-muted">
                    <img
                        src={heroImage.full_url || heroImage.url}
                        alt=""
                        className="absolute inset-0 h-full w-full object-cover"
                    />
                    <div
                        className="absolute inset-0 bg-black/45 dark:bg-black/55"
                        aria-hidden
                    />
                    <h1 className="relative z-10 max-w-4xl px-6 text-center text-3xl font-semibold tracking-tight text-white drop-shadow-md sm:text-4xl lg:text-5xl">
                        {page.title}
                    </h1>
                </div>
            ) : (
                <div className="mx-auto w-full max-w-3xl px-4 pt-16 sm:px-6">
                    <h1 className="mb-4 flex items-center gap-3 text-2xl font-semibold text-foreground">
                        <FileText
                            className="size-8 shrink-0 text-primary/60"
                            aria-hidden
                        />
                        {page.title}
                    </h1>
                </div>
            )}
            <article className="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
                <m.div {...fadeInUpView}>
                    {heroImage && <div className="mt-6" />}
                    {page.body != null && page.body !== '' && (
                        <div
                            className="prose max-w-none prose-neutral dark:prose-invert"
                            dangerouslySetInnerHTML={{
                                __html: decodeHtml(page.body),
                            }}
                        />
                    )}
                </m.div>
                {showGallery && (
                    <m.div className="mt-10" {...fadeInUpView}>
                        <h2 className="mb-4 text-lg font-semibold text-foreground">
                            Gallery
                        </h2>
                        <Carousel
                            opts={{
                                align: 'start',
                                loop: true,
                            }}
                            className="w-full"
                        >
                            <CarouselContent className="-ms-2">
                                {gallery.map((item, index) => (
                                    <CarouselItem
                                        key={item.id}
                                        className="basis-full sm:basis-1/2 md:basis-1/3"
                                    >
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            className="h-auto w-full p-0"
                                            onClick={() => {
                                                setLightboxIndex(index);
                                                setLightboxOpen(true);
                                            }}
                                        >
                                            <img
                                                src={item.thumb_url || item.url}
                                                alt=""
                                                className="aspect-video w-full rounded-lg object-cover"
                                            />
                                        </Button>
                                    </CarouselItem>
                                ))}
                            </CarouselContent>
                            <CarouselPrevious className="start-2" />
                            <CarouselNext className="end-2" />
                        </Carousel>
                    </m.div>
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
