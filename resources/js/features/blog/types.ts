export interface BlogPost {
    slug: string;
    title: string;
    excerpt: string;
    published_at: string | null;
    thumbnail_url?: string | null;
}

export interface BlogPostDetail extends BlogPost {
    body?: string;
    meta_description?: string | null;
    gallery?: MediaItem[];
    videos?: MediaItem[];
    documents?: DocumentItem[];
}

export interface MediaItem {
    id: number;
    url: string;
    full_url?: string;
    thumb_url?: string;
    type: string;
    alt?: string;
    title?: string;
    mime_type?: string;
}

export interface DocumentItem {
    id: number;
    url: string;
    file_name: string;
    type: string;
}

export interface PaginatorLinkItem {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PostsPaginator {
    data: BlogPost[];
    current_page: number;
    last_page: number;
    per_page: number;
    links: PaginatorLinkItem[];
}
