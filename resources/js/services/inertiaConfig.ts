/**
 * Central place for Inertia-related config (e.g. CSRF token, custom headers).
 * Inertia is configured in app.tsx; use this for shared fetch/axios defaults if needed.
 */
export const getCsrfToken = (): string | null => {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta?.getAttribute('content') ?? null;
};

export const inertiaHeaders = (): Record<string, string> => {
    const token = getCsrfToken();
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
    if (token) {
        headers['X-CSRF-TOKEN'] = token;
    }
    return headers;
};
