import i18n from 'i18next';
import LanguageDetector from 'i18next-browser-languagedetector';
import { initReactI18next } from 'react-i18next';

import enCore from './locales/en/core.json';
import esCore from './locales/es/core.json';

const supportedLngs = [
    'en',
    'it',
    'de',
    'es',
    'fr',
    'ru',
    'zh',
    'fa',
    'ar',
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
        resources: {
            en: { core: enCore },
            es: { core: esCore },
        },
        defaultNS: 'core',
    });

export default i18n;
