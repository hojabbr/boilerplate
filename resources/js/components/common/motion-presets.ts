const easeOut = 'easeOut' as const;

/** For layout wrappers: simple fade + slight y on mount */
export const pageEnter = {
    initial: { opacity: 0, y: 8 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.3, ease: easeOut },
};

/** For hero / above-fold content: fade + y on mount */
export const fadeInUp = {
    initial: { opacity: 0, y: 12 },
    animate: { opacity: 1, y: 0 },
    transition: { duration: 0.35, ease: easeOut },
};

/** For scroll-in content: animate when entering viewport */
export const fadeInUpView = {
    initial: { opacity: 0, y: 20 },
    whileInView: { opacity: 1, y: 0 },
    viewport: { once: true, margin: '-40px' },
    transition: { duration: 0.4, ease: easeOut },
};
