import * as VueI18n from "vue-i18n";

/**
 * Load translation files from a directory dynamically.
 * @param {string} path - The base path to search for translations.
 * @param {string[]} locales - The list of locales to load.
 * @returns {Promise<object>} - An object containing translations for all locales.
 */
async function loadTranslations(basePath, locales) {
    const messages = {};

    // Load all available JSON files using import.meta.glob
    const availableFiles = import.meta.glob('/resources/js/lang/**/*.json');
    const moduleFiles = import.meta.glob('/Modules/**/Resources/lang/**/*.json');

    const allFiles = { ...availableFiles, ...moduleFiles };

    for (const locale of locales) {
        messages[locale] = messages[locale] || {};

        for (const [filePath, loader] of Object.entries(allFiles)) {
            // Extract the locale and namespace from the file path
            const match = filePath.match(new RegExp(`${basePath}/(\\w+)/(.+?)\\.json`));

            if (match) {
                const [_, fileLocale, namespace] = match;

                if (fileLocale === locale) {
                    const translations = await loader();
                    messages[locale][namespace] = translations.default || translations;
                }
            }
        }
    }

    return messages;
}

/**
 * Initialize Vue I18n with support for modular translations.
 */
async function setupI18n() {
    const defaultLocale = "en";
    const availableLocales = ["en", "id"];

    const messages = await loadTranslations('/resources/js/lang', availableLocales);

    const i18n = VueI18n.createI18n({
        legacy: false,
        locale: defaultLocale,
        fallbackLocale: "en",
        messages,
    });

    return i18n;
}

export default setupI18n;
