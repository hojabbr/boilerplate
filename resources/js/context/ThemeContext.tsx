import {
    createContext,
    useCallback,
    useContext,
    useMemo,
    type ReactNode,
} from 'react';
import { useAppearance } from '@/hooks/use-appearance';
import type { Appearance, ResolvedAppearance } from '@/hooks/use-appearance';

type ThemeContextValue = {
    appearance: Appearance;
    resolvedAppearance: ResolvedAppearance;
    setAppearance: (mode: Appearance) => void;
};

const ThemeContext = createContext<ThemeContextValue | null>(null);

export function ThemeProvider({ children }: { children: ReactNode }) {
    const { appearance, resolvedAppearance, updateAppearance } =
        useAppearance();
    const setAppearance = useCallback(
        (mode: Appearance) => {
            updateAppearance(mode);
        },
        [updateAppearance],
    );
    const value = useMemo<ThemeContextValue>(
        () => ({
            appearance,
            resolvedAppearance,
            setAppearance,
        }),
        [appearance, resolvedAppearance, setAppearance],
    );
    return (
        <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>
    );
}

export function useTheme(): ThemeContextValue {
    const ctx = useContext(ThemeContext);
    if (!ctx) {
        throw new Error('useTheme must be used within ThemeProvider');
    }
    return ctx;
}
