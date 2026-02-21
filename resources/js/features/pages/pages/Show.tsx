import { usePage } from '@inertiajs/react';
import { m } from 'motion/react';
import { useState } from 'react';
import Lightbox from 'yet-another-react-lightbox';
import Zoom from 'yet-another-react-lightbox/plugins/zoom';
import 'yet-another-react-lightbox/styles.css';
import { BackButton } from '@/components/common/BackButton';
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
import { home } from '@/routes';

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
    const { locale, translations } = usePage().props as {
        locale: string;
        translations?: Record<string, string>;
    };
    const t = translations ?? {};
    const prefix = locale ? `/${locale}` : '';
    const breadcrumbs = [
        { title: t['nav.home'] ?? 'Home', href: prefix ? prefix : home.url() },
        { title: page.title, href: '#' },
    ];

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
            breadcrumbs={breadcrumbs}
            settings={settings}
            features={features}
        >
            <SeoHead
                title={seo?.title ?? page.meta_title ?? page.title}
                description={seo?.description ?? page.meta_description}
                image={seo?.image}
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
            <article className="mx-auto max-w-3xl">
                <m.div {...fadeInUpView}>
                    {heroImage && (
                        <div className="-mx-4 mb-6 overflow-hidden sm:mx-0 sm:rounded-xl">
                            <img
                                src={heroImage.full_url || heroImage.url}
                                alt=""
                                className="h-56 w-full object-cover sm:h-72"
                            />
                        </div>
                    )}
                    <h1 className="mb-4 text-2xl font-semibold text-foreground">
                        {page.title}
                    </h1>
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
