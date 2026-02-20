export interface CmsPage {
    title: string;
    body: string;
    meta_title?: string | null;
    meta_description?: string | null;
    gallery?: {
        id: number;
        url: string;
        full_url?: string;
        thumb_url?: string;
    }[];
}
