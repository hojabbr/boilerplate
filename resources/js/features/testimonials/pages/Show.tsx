import { Quote } from 'lucide-react';
import { m } from 'motion/react';
import { fadeInUpView, pageEnter } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import { Card, CardContent } from '@/components/ui/card';
import PublicLayout, {
    EMPTY_PUBLIC_FEATURES,
    EMPTY_PUBLIC_SETTINGS,
    type PublicFeatures,
    type PublicSettings,
} from '@/layouts/public-layout';

interface TestimonialItem {
    quote: string;
    author: string;
    role?: string;
}

interface Seo {
    title: string;
    description?: string;
}

export default function TestimonialsShow({
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
    items = [],
}: {
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
    items: TestimonialItem[];
}) {
    return (
        <PublicLayout settings={settings} features={features}>
            <SeoHead
                title={seo?.title ?? 'Testimonials'}
                description={seo?.description}
            />
            <m.div
                className="section-spacing mx-auto max-w-5xl px-4 pt-16 sm:px-6"
                {...pageEnter}
            >
                <div className="mb-12 text-center">
                    <h1 className="display-title mb-4 flex items-center justify-center gap-3 text-foreground">
                        <Quote
                            className="size-8 shrink-0 text-primary/60"
                            aria-hidden
                        />
                        What our customers say
                    </h1>
                    <p className="display-subtitle mx-auto max-w-xl text-muted-foreground">
                        Read what our customers and partners have to say about
                        working with us.
                    </p>
                </div>
                <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    {items.map((item, j) => (
                        <m.div
                            key={j}
                            {...fadeInUpView}
                            transition={{
                                delay: j * 0.08,
                                duration: 0.4,
                                ease: 'easeOut',
                            }}
                        >
                            <Card
                                variant="glass"
                                className="card-inner-glow flex h-full flex-col rounded-2xl border border-border p-6 shadow-lg"
                            >
                                <Quote
                                    className="mb-4 size-8 text-primary/60"
                                    aria-hidden
                                />
                                <CardContent className="flex flex-1 flex-col p-0">
                                    <blockquote className="flex-1 text-lg leading-relaxed text-foreground/90">
                                        "{item.quote}"
                                    </blockquote>
                                    <footer className="mt-6 shrink-0 border-t border-border/60 pt-4">
                                        <cite className="font-semibold text-foreground not-italic">
                                            {item.author}
                                        </cite>
                                        {item.role && (
                                            <p className="mt-0.5 text-sm text-muted-foreground">
                                                {item.role}
                                            </p>
                                        )}
                                    </footer>
                                </CardContent>
                            </Card>
                        </m.div>
                    ))}
                </div>
            </m.div>
        </PublicLayout>
    );
}
