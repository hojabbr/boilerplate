import type { SVGAttributes } from 'react';

interface AppLogoIconProps extends SVGAttributes<SVGElement> {
    src?: string | null;
    alt?: string;
}

export default function AppLogoIcon({ src, alt, ...props }: AppLogoIconProps) {
    if (src) {
        return (
            <img
                src={src}
                alt={alt ?? ''}
                className={props.className}
                style={props.style}
            />
        );
    }

    return (
        <img
            src="/favicon/favicon.svg"
            alt={alt ?? ''}
            className={props.className}
            style={props.style}
        />
    );
}
