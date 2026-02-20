import {
    ExternalLink,
    Facebook,
    Github,
    Instagram,
    Linkedin,
    type LucideIcon,
    Twitter,
    Youtube,
} from 'lucide-react';

const LABELS: Record<string, string> = {
    twitter: 'Twitter',
    x: 'X',
    linkedin: 'LinkedIn',
    github: 'GitHub',
    facebook: 'Facebook',
    instagram: 'Instagram',
    youtube: 'YouTube',
};

const ICONS: Record<string, LucideIcon> = {
    twitter: Twitter,
    x: Twitter,
    linkedin: Linkedin,
    github: Github,
    facebook: Facebook,
    instagram: Instagram,
    youtube: Youtube,
};

function labelForKey(key: string): string {
    return (
        LABELS[key.toLowerCase()] ??
        key.charAt(0).toUpperCase() + key.slice(1).toLowerCase()
    );
}

function iconForKey(key: string): LucideIcon {
    return ICONS[key.toLowerCase()] ?? ExternalLink;
}

interface SocialLinksProps {
    social_links: Record<string, string>;
    className?: string;
    /** 'footer' = compact icon-style row; 'inline' = text links with separators */
    variant?: 'footer' | 'inline';
}

export default function SocialLinks({
    social_links,
    className = '',
    variant = 'footer',
}: SocialLinksProps) {
    const entries = Object.entries(social_links).filter(
        ([, url]) => url && typeof url === 'string' && url.trim() !== '',
    );
    if (entries.length === 0) {
        return null;
    }

    const linkClass =
        variant === 'footer'
            ? 'text-muted-foreground hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring rounded p-1'
            : 'text-muted-foreground hover:text-foreground underline underline-offset-2';

    return (
        <nav
            className={
                variant === 'footer'
                    ? `flex flex-wrap justify-center gap-2 ${className}`
                    : className
            }
            aria-label="Social links"
        >
            {entries.map(([key, url]) => {
                const Icon = iconForKey(key);
                return (
                    <a
                        key={key}
                        href={url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className={linkClass}
                        title={labelForKey(key)}
                    >
                        {variant === 'footer' ? (
                            <Icon className="size-4" aria-hidden />
                        ) : (
                            labelForKey(key)
                        )}
                    </a>
                );
            })}
        </nav>
    );
}
