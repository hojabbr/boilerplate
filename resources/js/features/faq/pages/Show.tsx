import { HelpCircle } from 'lucide-react';
import { m } from 'motion/react';
import { pageEnter } from '@/components/common/motion-presets';
import { SeoHead } from '@/components/common/SeoHead';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import PublicLayout, {
    EMPTY_PUBLIC_FEATURES,
    EMPTY_PUBLIC_SETTINGS,
    type PublicFeatures,
    type PublicSettings,
} from '@/layouts/public-layout';

interface FaqItem {
    question: string;
    answer: string;
}

interface Seo {
    title: string;
    description?: string;
}

const EMPTY_FAQ_ITEMS: FaqItem[] = [];

export default function FaqShow({
    settings = EMPTY_PUBLIC_SETTINGS,
    features = EMPTY_PUBLIC_FEATURES,
    seo,
    items = EMPTY_FAQ_ITEMS,
}: {
    settings?: PublicSettings;
    features?: PublicFeatures;
    seo?: Seo;
    items: FaqItem[];
}) {
    return (
        <PublicLayout settings={settings} features={features}>
            <SeoHead
                title={seo?.title ?? 'FAQ'}
                description={seo?.description}
            />
            <m.div
                className="section-spacing mx-auto max-w-3xl px-4 pt-16 sm:px-6"
                {...pageEnter}
            >
                <div className="mb-12">
                    <h1 className="display-title mb-4 flex items-center gap-3 text-foreground">
                        <HelpCircle
                            className="size-8 shrink-0 text-primary/60"
                            aria-hidden
                        />
                        {seo?.title ?? 'FAQ'}
                    </h1>
                    {seo?.description && (
                        <p className="display-subtitle text-muted-foreground">
                            {seo.description}
                        </p>
                    )}
                </div>
                <Accordion
                    type="single"
                    collapsible
                    className="w-full rounded-2xl bg-card p-2 shadow-sm"
                >
                    {items.map((item) => (
                        <AccordionItem
                            key={item.question}
                            value={item.question}
                            className="rounded-xl px-4 data-[state=open]:bg-muted/50"
                        >
                            <AccordionTrigger className="text-start font-semibold hover:no-underline">
                                {item.question}
                            </AccordionTrigger>
                            <AccordionContent className="text-muted-foreground">
                                {item.answer}
                            </AccordionContent>
                        </AccordionItem>
                    ))}
                </Accordion>
            </m.div>
        </PublicLayout>
    );
}
