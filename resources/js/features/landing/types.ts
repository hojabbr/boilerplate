export interface SectionItem {
    title?: string | null;
    description?: string | null;
    icon_url?: string | null;
}

export interface LandingSection {
    type: string;
    title?: string | null;
    subtitle?: string | null;
    body?: string | null;
    cta_text?: string | null;
    cta_url?: string | null;
    image_url?: string | null;
    items?: SectionItem[];
}

export interface WelcomeMessages {
    heading?: string;
    tagline_fallback?: string;
    cta_get_started?: string;
    cta_contact_us?: string;
    explore?: string;
    about_us_title?: string;
    about_us_description?: string;
    blog_title?: string;
    blog_description?: string;
    contact_title?: string;
    contact_description?: string;
}
