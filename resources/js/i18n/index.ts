import i18n from 'i18next';
import LanguageDetector from 'i18next-browser-languagedetector';
import { initReactI18next } from 'react-i18next';

/**
 * Frontend i18n is used only to sync language with the backend (e.g. changeLanguage(locale)).
 * All UI strings come from Laravel lang/*.json via Inertia shared props (translations/messages).
 */
const supportedLngs = [
    'en',
    'de',
    'es',
    'fr',
    'it',
    'ru',
    'ar',
    'fa',
    'ja',
    'zh',
    'ko',
] as const;

void i18n
    .use(LanguageDetector)
    .use(initReactI18next)
    .init({
        supportedLngs: [...supportedLngs],
        fallbackLng: 'en',
        detection: {
            order: ['querystring', 'cookie', 'localStorage', 'navigator'],
        },
        interpolation: {
            escapeValue: false,
        },
    });

export default i18n;
