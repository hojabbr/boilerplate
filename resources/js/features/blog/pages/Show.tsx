import { BookOpen } from 'lucide-react';
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
import { fadeInUpView } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import { Badge } from '@/components/ui/badge';
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
    card_url: string;
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

interface BlogTag {
    id: number;
    name: string;
    slug: string;
}

interface Post {
    title: string;
    excerpt: string;
    body: string;
    meta_description: string | null;
    published_at: string | null;
    tags?: BlogTag[];
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
            contentVariant="full-bleed"
            settings={settings}
            features={features}
        >
            <SeoHead
                title={seo?.title ?? post.title}
                description={seo?.description ?? post.meta_description}
                image={seo?.image}
                type={seo?.type ?? 'article'}
            />
            {heroImage ? (
                <div className="relative flex min-h-[50vh] w-full items-center justify-center overflow-hidden bg-muted">
                    <img
                        src={heroImage.full_url || heroImage.url}
                        alt={heroImage.alt ?? heroImage.title ?? ''}
                        className="absolute inset-0 h-full w-full object-cover"
                    />
                    <div
                        className="absolute inset-0 bg-black/45 dark:bg-black/55"
                        aria-hidden
                    />
                    <h1 className="relative z-10 max-w-4xl px-6 text-center text-3xl font-semibold tracking-tight text-white drop-shadow-md sm:text-4xl lg:text-5xl">
                        {post.title}
                    </h1>
                </div>
            ) : (
                <div className="mx-auto w-full max-w-3xl px-4 pt-16 sm:px-6">
                    <h1 className="mb-2 flex items-center gap-3 text-2xl font-semibold text-foreground">
                        <BookOpen
                            className="size-7 shrink-0 text-primary/60"
                            aria-hidden
                        />
                        {post.title}
                    </h1>
                </div>
            )}
            <article className="mx-auto w-full max-w-3xl px-4 sm:px-6 lg:px-8">
                <m.div {...fadeInUpView}>
                    {heroImage && (
                        <div className="mt-6 flex flex-wrap items-center gap-3">
                            {post.tags && post.tags.length > 0 && (
                                <ul
                                    className="flex flex-wrap gap-1.5"
                                    aria-label="Tags"
                                >
                                    {post.tags.map((tag) => (
                                        <li key={tag.id}>
                                            <Badge
                                                variant="secondary"
                                                className="font-normal"
                                            >
                                                {tag.name}
                                            </Badge>
                                        </li>
                                    ))}
                                </ul>
                            )}
                            {post.published_at && (
                                <p className="text-sm text-muted-foreground">
                                    {new Date(
                                        post.published_at,
                                    ).toLocaleDateString()}
                                </p>
                            )}
                        </div>
                    )}
                    {!heroImage && (
                        <>
                            {post.tags && post.tags.length > 0 && (
                                <ul
                                    className="mb-3 flex flex-wrap gap-1.5"
                                    aria-label="Tags"
                                >
                                    {post.tags.map((tag) => (
                                        <li key={tag.id}>
                                            <Badge
                                                variant="secondary"
                                                className="font-normal"
                                            >
                                                {tag.name}
                                            </Badge>
                                        </li>
                                    ))}
                                </ul>
                            )}
                            {post.published_at && (
                                <p className="mb-4 text-sm text-muted-foreground">
                                    {new Date(
                                        post.published_at,
                                    ).toLocaleDateString()}
                                </p>
                            )}
                        </>
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
                            className="prose max-w-none prose-neutral dark:prose-invert [&_.blog-internal-link]:font-medium [&_.blog-internal-link]:text-foreground [&_.blog-internal-link]:italic [&_.blog-internal-link]:underline [&_.blog-internal-link]:underline-offset-2"
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
                                                src={
                                                    item.card_url ||
                                                    item.thumb_url ||
                                                    item.url
                                                }
                                                alt={
                                                    item.alt ?? item.title ?? ''
                                                }
                                                className="aspect-[8/5] w-full rounded-lg object-cover"
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
