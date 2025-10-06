/**
 * Utilitas untuk mengelola translasi theme.json
 * Mendukung ekstraksi, penerjemahan, dan penggabungan kembali teks yang dapat diterjemahkan
 */

export class ThemeTranslationUtils {
    /**
     * Ekstrak semua teks yang dapat diterjemahkan dari theme.json
     * @param {Object} themeJson - Objek theme.json
     * @returns {Object} Objek dengan key-value teks yang dapat diterjemahkan
     */
    static extractTranslatableTexts(themeJson) {
        const translatableTexts = {};
        
        // Ekstrak metadata dasar
        if (themeJson.name) {
            translatableTexts['theme.name'] = themeJson.name;
        }
        
        if (themeJson.description) {
            translatableTexts['theme.description'] = themeJson.description;
        }
        
        // Ekstrak labels
        if (themeJson.labels) {
            this._extractFromObject(themeJson.labels, 'labels', translatableTexts);
        }
        
        // Ekstrak metadata tags dan category
        if (themeJson.metadata) {
            if (themeJson.metadata.tags && Array.isArray(themeJson.metadata.tags)) {
                themeJson.metadata.tags.forEach((tag, index) => {
                    translatableTexts[`metadata.tags.${index}`] = tag;
                });
            }
            
            if (themeJson.metadata.category) {
                translatableTexts['metadata.category'] = themeJson.metadata.category;
            }
            
            if (themeJson.metadata.supportedModes && Array.isArray(themeJson.metadata.supportedModes)) {
                themeJson.metadata.supportedModes.forEach((mode, index) => {
                    translatableTexts[`metadata.supportedModes.${index}`] = mode;
                });
            }
        }
        
        return translatableTexts;
    }
    
    /**
     * Ekstrak teks dari objek secara rekursif
     * @param {Object} obj - Objek untuk diekstrak
     * @param {string} prefix - Prefix untuk key
     * @param {Object} result - Objek hasil
     * @private
     */
    static _extractFromObject(obj, prefix, result) {
        for (const [key, value] of Object.entries(obj)) {
            const fullKey = `${prefix}.${key}`;
            
            if (typeof value === 'string') {
                result[fullKey] = value;
            } else if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
                this._extractFromObject(value, fullKey, result);
            } else if (Array.isArray(value)) {
                value.forEach((item, index) => {
                    if (typeof item === 'string') {
                        result[`${fullKey}.${index}`] = item;
                    }
                });
            }
        }
    }
    
    /**
     * Terapkan translasi ke theme.json
     * @param {Object} themeJson - Objek theme.json asli
     * @param {Object} translations - Objek translasi dengan key-value
     * @returns {Object} Theme.json yang sudah diterjemahkan
     */
    static applyTranslations(themeJson, translations) {
        const translatedTheme = JSON.parse(JSON.stringify(themeJson)); // Deep clone
        
        for (const [key, translatedValue] of Object.entries(translations)) {
            this._setNestedValue(translatedTheme, key, translatedValue);
        }
        
        return translatedTheme;
    }
    
    /**
     * Set nilai nested dalam objek menggunakan dot notation
     * @param {Object} obj - Objek target
     * @param {string} path - Path dengan dot notation
     * @param {any} value - Nilai yang akan di-set
     * @private
     */
    static _setNestedValue(obj, path, value) {
        const keys = path.split('.');
        let current = obj;
        
        for (let i = 0; i < keys.length - 1; i++) {
            const key = keys[i];
            
            // Handle array indices
            if (!isNaN(parseInt(keys[i + 1]))) {
                if (!current[key]) {
                    current[key] = [];
                }
                current = current[key];
            } else {
                if (!current[key]) {
                    current[key] = {};
                }
                current = current[key];
            }
        }
        
        const lastKey = keys[keys.length - 1];
        
        // Handle array index
        if (!isNaN(parseInt(lastKey))) {
            current[parseInt(lastKey)] = value;
        } else {
            current[lastKey] = value;
        }
    }
    
    /**
     * Validasi struktur theme.json
     * @param {Object} themeJson - Objek theme.json untuk divalidasi
     * @returns {Object} Hasil validasi dengan status dan pesan error
     */
    static validateThemeStructure(themeJson) {
        const errors = [];
        const warnings = [];
        
        // Validasi field wajib
        if (!themeJson.name) {
            errors.push('Field "name" wajib ada');
        }
        
        if (!themeJson.cssVars) {
            errors.push('Field "cssVars" wajib ada');
        } else {
            if (!themeJson.cssVars.light && !themeJson.cssVars.dark) {
                errors.push('Minimal harus ada cssVars untuk mode "light" atau "dark"');
            }
        }
        
        // Validasi field opsional tapi direkomendasikan
        if (!themeJson.description) {
            warnings.push('Field "description" direkomendasikan untuk ada');
        }
        
        if (!themeJson.labels) {
            warnings.push('Field "labels" direkomendasikan untuk translasi');
        }
        
        if (!themeJson.metadata) {
            warnings.push('Field "metadata" direkomendasikan untuk informasi tambahan');
        }
        
        return {
            isValid: errors.length === 0,
            errors,
            warnings
        };
    }
    
    /**
     * Buat template translasi kosong berdasarkan theme.json
     * @param {Object} themeJson - Objek theme.json
     * @param {string} targetLanguage - Kode bahasa target (misal: 'id', 'en')
     * @returns {Object} Template translasi kosong
     */
    static createTranslationTemplate(themeJson, targetLanguage = 'id') {
        const translatableTexts = this.extractTranslatableTexts(themeJson);
        const template = {
            sourceLanguage: themeJson.language || 'en',
            targetLanguage,
            translations: {}
        };
        
        // Buat template kosong untuk setiap teks yang dapat diterjemahkan
        for (const key of Object.keys(translatableTexts)) {
            template.translations[key] = '';
        }
        
        return template;
    }
    
    /**
     * Gabungkan beberapa file translasi
     * @param {Array} translationFiles - Array objek translasi
     * @returns {Object} Translasi yang sudah digabungkan
     */
    static mergeTranslations(translationFiles) {
        const merged = {
            translations: {}
        };
        
        for (const file of translationFiles) {
            if (file.translations) {
                Object.assign(merged.translations, file.translations);
            }
            
            // Ambil metadata dari file terakhir
            if (file.sourceLanguage) merged.sourceLanguage = file.sourceLanguage;
            if (file.targetLanguage) merged.targetLanguage = file.targetLanguage;
        }
        
        return merged;
    }
    
    /**
     * Ekspor translasi ke format yang berbeda
     * @param {Object} translations - Objek translasi
     * @param {string} format - Format ekspor ('json', 'csv', 'xlsx')
     * @returns {string|Object} Data dalam format yang diminta
     */
    static exportTranslations(translations, format = 'json') {
        switch (format.toLowerCase()) {
            case 'json':
                return JSON.stringify(translations, null, 2);
                
            case 'csv':
                return this._exportToCsv(translations);
                
            case 'xlsx':
                // Untuk implementasi XLSX, perlu library tambahan seperti xlsx
                console.warn('Format XLSX belum diimplementasikan');
                return this._exportToCsv(translations);
                
            default:
                throw new Error(`Format ${format} tidak didukung`);
        }
    }
    
    /**
     * Ekspor ke format CSV
     * @param {Object} translations - Objek translasi
     * @returns {string} Data CSV
     * @private
     */
    static _exportToCsv(translations) {
        const headers = ['Key', 'Source Text', 'Translated Text', 'Status'];
        const rows = [headers.join(',')];
        
        for (const [key, translatedValue] of Object.entries(translations.translations || {})) {
            const sourceText = ''; // Bisa ditambahkan jika ada referensi ke teks asli
            const status = translatedValue ? 'Translated' : 'Pending';
            
            const row = [
                `"${key}"`,
                `"${sourceText}"`,
                `"${translatedValue}"`,
                `"${status}"`
            ];
            
            rows.push(row.join(','));
        }
        
        return rows.join('\n');
    }
    
    /**
     * Import translasi dari format CSV
     * @param {string} csvData - Data CSV
     * @returns {Object} Objek translasi
     */
    static importFromCsv(csvData) {
        const lines = csvData.split('\n');
        const translations = {};
        
        // Skip header
        for (let i = 1; i < lines.length; i++) {
            const line = lines[i].trim();
            if (!line) continue;
            
            // Simple CSV parsing (untuk parsing yang lebih robust, gunakan library)
            const columns = line.split(',').map(col => col.replace(/^"|"$/g, ''));
            
            if (columns.length >= 3) {
                const [key, , translatedText] = columns;
                if (key && translatedText) {
                    translations[key] = translatedText;
                }
            }
        }
        
        return {
            translations
        };
    }
    
    /**
     * Deteksi perubahan dalam theme.json dan update translasi yang ada
     * @param {Object} oldTheme - Theme.json lama
     * @param {Object} newTheme - Theme.json baru
     * @param {Object} existingTranslations - Translasi yang sudah ada
     * @returns {Object} Translasi yang sudah diupdate dengan perubahan
     */
    static updateTranslationsForChanges(oldTheme, newTheme, existingTranslations) {
        const oldTexts = this.extractTranslatableTexts(oldTheme);
        const newTexts = this.extractTranslatableTexts(newTheme);
        
        const updatedTranslations = { ...existingTranslations };
        
        // Tambahkan key baru
        for (const key of Object.keys(newTexts)) {
            if (!oldTexts[key] && !updatedTranslations.translations[key]) {
                updatedTranslations.translations[key] = ''; // Kosong, perlu diterjemahkan
            }
        }
        
        // Tandai key yang dihapus (opsional: bisa dihapus atau dibiarkan)
        const removedKeys = [];
        for (const key of Object.keys(oldTexts)) {
            if (!newTexts[key]) {
                removedKeys.push(key);
            }
        }
        
        return {
            updatedTranslations,
            addedKeys: Object.keys(newTexts).filter(key => !oldTexts[key]),
            removedKeys,
            changedKeys: Object.keys(newTexts).filter(key => 
                oldTexts[key] && oldTexts[key] !== newTexts[key]
            )
        };
    }
}

export default ThemeTranslationUtils;