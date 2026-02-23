import i18n from 'i18next';
import LanguageDetector from 'i18next-browser-languagedetector';
import { initReactI18next } from 'react-i18next';

/**
 * Frontend i18n syncs language with the backend (e.g. changeLanguage(locale)).
 * UI strings come from Laravel lang/*.json via Inertia shared props (translations).
 * Supported locales: fallback below for initial load; real list comes from Inertia shared
 * props (supported_locale_codes) and is applied in app.tsx setup() so the list is DB-driven.
 */
const supportedLngsFallback = ['en'] as const;

void i18n
    .use(LanguageDetector)
    .use(initReactI18next)
    .init({
        supportedLngs: [...supportedLngsFallback],
        fallbackLng: 'en',
        detection: {
            order: ['querystring', 'cookie', 'localStorage', 'navigator'],
        },
        interpolation: {
            escapeValue: false,
        },
    });

export default i18n;
