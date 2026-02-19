import { Head } from '@inertiajs/react';
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

export default function BlogShow({
    post,
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
}: {
    post: Post;
    settings?: PublicSettings;
    features?: PublicFeatures;
}) {
    const [lightboxOpen, setLightboxOpen] = useState(false);
    const [lightboxIndex, setLightboxIndex] = useState(0);

    const gallery = post.gallery ?? [];
    const videos = post.videos ?? [];
    const documents = post.documents ?? [];
    const slides = buildSlides(gallery, videos);
    const hasMedia = slides.length > 0;

    return (
        <PublicLayout settings={settings} features={features}>
            <Head title={post.title}>
                {post.meta_description && (
                    <meta name="description" content={post.meta_description} />
                )}
            </Head>
            <article className="mx-auto max-w-3xl">
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
                        className="prose prose-sm dark:prose-invert mb-6"
                        dangerouslySetInnerHTML={{ __html: post.excerpt }}
                    />
                )}
                <div
                    className="prose dark:prose-invert"
                    dangerouslySetInnerHTML={{ __html: post.body }}
                />

                {hasMedia && (
                    <section className="mt-8" aria-label="Media gallery">
                        <h2 className="mb-4 text-lg font-medium text-foreground">
                            Gallery
                        </h2>
                        <div className="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
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
                                        alt={item.alt ?? item.title ?? ''}
                                        className="aspect-square w-full rounded-lg object-cover"
                                    />
                                </Button>
                            ))}
                            {videos.map((item, index) => (
                                <Button
                                    key={item.id}
                                    type="button"
                                    variant="ghost"
                                    className="h-auto p-0"
                                    onClick={() => {
                                        setLightboxIndex(
                                            gallery.length + index,
                                        );
                                        setLightboxOpen(true);
                                    }}
                                >
                                    <video
                                        src={item.url}
                                        className="aspect-square w-full rounded-lg object-cover"
                                        muted
                                        playsInline
                                        preload="metadata"
                                    />
                                </Button>
                            ))}
                        </div>
                        <Button
                            type="button"
                            variant="link"
                            className="mt-3"
                            onClick={() => {
                                setLightboxIndex(0);
                                setLightboxOpen(true);
                            }}
                        >
                            View all ({slides.length})
                        </Button>
                    </section>
                )}

                {documents.length > 0 && (
                    <section className="mt-8" aria-label="Documents">
                        <h2 className="mb-4 text-lg font-medium text-foreground">
                            Documents
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
                    </section>
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
