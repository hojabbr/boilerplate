import { usePage } from '@inertiajs/react';
import { m } from 'motion/react';
import { useState } from 'react';
import Lightbox from 'yet-another-react-lightbox';
import Captions from 'yet-another-react-lightbox/plugins/captions';
import Fullscreen from 'yet-another-react-lightbox/plugins/fullscreen';
import Slideshow from 'yet-another-react-lightbox/plugins/slideshow';
import Thumbnails from 'yet-another-react-lightbox/plugins/thumbnails';
import Video from 'yet-another-react-lightbox/plugins/video';
import Zoom from 'yet-another-react-lightbox/plugins/zoom';
import 'yet-another-react-lightbox/styles.css';
import 'yet-another-react-lightbox/plugins/thumbnails.css';
import 'yet-another-react-lightbox/plugins/captions.css';
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
import blog from '@/routes/blog';

interface GalleryItem {
    id: number;
    url: string;
    full_url: string;
    thumb_url: string;
    type: 'image';
    alt?: string;
    title?: string;
}

interface VideoItem {
    id: number;
    url: string;
    type: 'video';
    mime_type: string;
}

interface DocumentItem {
    id: number;
    url: string;
    file_name: string;
    type: 'file';
}

interface Post {
    title: string;
    excerpt: string;
    body: string;
    meta_description: string | null;
    published_at: string | null;
    gallery: GalleryItem[];
    videos: VideoItem[];
    documents: DocumentItem[];
}

type Slide =
    | { src: string; alt?: string; title?: string }
    | {
          type: 'video';
          sources: { src: string; type: string }[];
      };

function buildSlides(gallery: GalleryItem[], videos: VideoItem[]): Slide[] {
    const imageSlides: Slide[] = gallery.map((item) => ({
        src: item.full_url || item.url,
        alt: item.alt,
        title: item.title,
    }));
    const videoSlides: Slide[] = videos.map((item) => ({
        type: 'video',
        sources: [{ src: item.url, type: item.mime_type || 'video/mp4' }],
    }));
    return [...imageSlides, ...videoSlides];
}

interface Seo {
    title: string;
    description?: string | null;
    image?: string | null;
    type?: 'website' | 'article';
}

interface BlogShowMessages {
    media_gallery?: string;
    documents?: string;
}

const EMPTY_BLOG_SHOW_MESSAGES: BlogShowMessages = {};

export default function BlogShow({
    post,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
    messages = EMPTY_BLOG_SHOW_MESSAGES,
}: {
    post: Post;
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
    messages?: BlogShowMessages;
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
            title: t['nav.blog'] ?? 'Blog',
            href: `${prefix}${blog.index.url()}`,
        },
        { title: post.title, href: '#' },
    ];

    const [lightboxOpen, setLightboxOpen] = useState(false);
    const [lightboxIndex, setLightboxIndex] = useState(0);

    const gallery = post.gallery ?? [];
    const videos = post.videos ?? [];
    const documents = post.documents ?? [];
    const slides = buildSlides(gallery, videos);
    const hasMedia = slides.length > 0;
    const showGallery = slides.length > 1;
    const mediaGalleryLabel = messages.media_gallery ?? 'Gallery';
    const documentsLabel = messages.documents ?? 'Documents';
    const heroImage = gallery[0];

    return (
        <PublicLayout
            breadcrumbs={breadcrumbs}
            settings={settings}
            features={features}
        >
            <SeoHead
                title={seo?.title ?? post.title}
                description={seo?.description ?? post.meta_description}
                image={seo?.image}
                type={seo?.type ?? 'article'}
            />
            <div className="mb-4">
                <BackButton
                    href={`${prefix}${blog.index.url()}`}
                    label={
                        t['nav.blog']
                            ? `Back to ${t['nav.blog']}`
                            : 'Back to Blog'
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
                    <h1 className="mb-2 text-2xl font-semibold text-foreground">
                        {post.title}
                    </h1>
                    {post.published_at && (
                        <p className="mb-4 text-sm text-muted-foreground">
                            {new Date(post.published_at).toLocaleDateString()}
                        </p>
                    )}
                    {post.excerpt && (
                        <div
                            className="prose prose-sm mb-6 max-w-none prose-neutral dark:prose-invert"
                            dangerouslySetInnerHTML={{
                                __html: decodeHtml(post.excerpt),
                            }}
                        />
                    )}
                    {post.body != null && post.body !== '' && (
                        <div
                            className="prose max-w-none prose-neutral dark:prose-invert"
                            dangerouslySetInnerHTML={{
                                __html: decodeHtml(post.body),
                            }}
                        />
                    )}
                </m.div>

                {showGallery && (
                    <m.div className="mt-10" {...fadeInUpView}>
                        <h2 className="mb-4 text-lg font-semibold text-foreground">
                            {mediaGalleryLabel}
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
                                                alt={
                                                    item.alt ?? item.title ?? ''
                                                }
                                                className="aspect-video w-full rounded-lg object-cover"
                                            />
                                        </Button>
                                    </CarouselItem>
                                ))}
                                {videos.map((item, index) => (
                                    <CarouselItem
                                        key={item.id}
                                        className="basis-full sm:basis-1/2 md:basis-1/3"
                                    >
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            className="h-auto w-full p-0"
                                            onClick={() => {
                                                setLightboxIndex(
                                                    gallery.length + index,
                                                );
                                                setLightboxOpen(true);
                                            }}
                                        >
                                            <video
                                                src={item.url}
                                                className="aspect-video w-full rounded-lg object-cover"
                                                muted
                                                playsInline
                                                preload="metadata"
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

                {documents.length > 0 && (
                    <m.section
                        className="mt-10"
                        aria-label={documentsLabel}
                        {...fadeInUpView}
                    >
                        <h2 className="mb-4 text-lg font-semibold text-foreground">
                            {documentsLabel}
                        </h2>
                        <ul className="space-y-2">
                            {documents.map((item) => (
                                <li key={item.id}>
                                    <Button
                                        variant="link"
                                        className="h-auto p-0 font-normal"
                                        asChild
                                    >
                                        <a
                                            href={item.url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            {item.file_name}
                                        </a>
                                    </Button>
                                </li>
                            ))}
                        </ul>
                    </m.section>
                )}
            </article>

            {hasMedia && (
                <Lightbox
                    open={lightboxOpen}
                    close={() => setLightboxOpen(false)}
                    index={lightboxIndex}
                    slides={slides}
                    plugins={[
                        Captions,
                        Fullscreen,
                        Slideshow,
                        Thumbnails,
                        Video,
                        Zoom,
                    ]}
                    captions={{ descriptionTextAlign: 'center' }}
                />
            )}
        </PublicLayout>
    );
}
