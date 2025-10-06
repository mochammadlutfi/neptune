import { ref, computed, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

// Global state for localization
const currentLocale = ref('en')
const supportedLocales = ref({})
const isLoading = ref(false)
const error = ref(null)

/**
 * Composable for managing localization in NEPTUNE system
 * Provides methods for locale switching, formatting, and activity message handling
 */
export function useLocalization() {
    const { locale, t } = useI18n()

    /**
     * Initialize localization system
     * Fetches current locale and supported locales from backend
     */
    const initializeLocalization = async () => {
        isLoading.value = true
        error.value = null

        try {
            // Get current locale information
            const currentResponse = await axios.get('/api/localization/current')
            if (currentResponse.data.success) {
                currentLocale.value = currentResponse.data.data.current_locale
                locale.value = currentLocale.value
            }

            // Get supported locales
            const supportedResponse = await axios.get('/api/localization/supported')
            if (supportedResponse.data.success) {
                supportedLocales.value = supportedResponse.data.data
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to initialize localization'
            console.error('Localization initialization error:', err)
        } finally {
            isLoading.value = false
        }
    }

    /**
     * Set new locale for the user
     * @param {string} newLocale - The locale code (e.g., 'en', 'id')
     */
    const setLocale = async (newLocale) => {
        if (!isValidLocale(newLocale)) {
            throw new Error(`Unsupported locale: ${newLocale}`)
        }

        isLoading.value = true
        error.value = null

        try {
            const response = await axios.post('/api/localization/set', {
                locale: newLocale
            })

            if (response.data.success) {
                currentLocale.value = newLocale
                locale.value = newLocale
                
                // Store in localStorage for persistence
                localStorage.setItem('neptune_locale', newLocale)
                
                return response.data.data
            } else {
                throw new Error(response.data.message)
            }
        } catch (err) {
            error.value = err.response?.data?.message || 'Failed to set locale'
            throw err
        } finally {
            isLoading.value = false
        }
    }

    /**
     * Get localized activity messages
     * @param {Array} keys - Array of message keys
     * @param {Object} params - Parameters for message interpolation
     * @param {string} targetLocale - Optional specific locale
     */
    const getActivityMessages = async (keys, params = {}, targetLocale = null) => {
        try {
            const response = await axios.post('/api/localization/activity-messages', {
                keys,
                params,
                locale: targetLocale
            })

            if (response.data.success) {
                return response.data.data
            } else {
                throw new Error(response.data.message)
            }
        } catch (err) {
            console.error('Failed to get activity messages:', err)
            // Return fallback messages
            const fallback = {}
            keys.forEach(key => {
                fallback[key] = key
            })
            return fallback
        }
    }

    /**
     * Format number according to current locale
     * @param {number} number - Number to format
     * @param {number} decimals - Number of decimal places
     * @param {string} targetLocale - Optional specific locale
     */
    const formatNumber = async (number, decimals = 2, targetLocale = null) => {
        try {
            const response = await axios.post('/api/localization/format-number', {
                number,
                decimals,
                locale: targetLocale
            })

            if (response.data.success) {
                return response.data.data.formatted
            } else {
                throw new Error(response.data.message)
            }
        } catch (err) {
            console.error('Failed to format number:', err)
            // Fallback to basic formatting
            return Number(number).toFixed(decimals)
        }
    }

    /**
     * Format currency according to current locale
     * @param {number} amount - Amount to format
     * @param {string} targetLocale - Optional specific locale
     */
    const formatCurrency = async (amount, targetLocale = null) => {
        try {
            const response = await axios.post('/api/localization/format-currency', {
                amount,
                locale: targetLocale
            })

            if (response.data.success) {
                return response.data.data.formatted
            } else {
                throw new Error(response.data.message)
            }
        } catch (err) {
            console.error('Failed to format currency:', err)
            // Fallback to basic formatting
            return `$${Number(amount).toFixed(2)}`
        }
    }

    /**
     * Format date according to current locale
     * @param {string|Date} date - Date to format
     * @param {boolean} includeTime - Whether to include time
     * @param {string} targetLocale - Optional specific locale
     */
    const formatDate = async (date, includeTime = false, targetLocale = null) => {
        try {
            const response = await axios.post('/api/localization/format-date', {
                date: date instanceof Date ? date.toISOString() : date,
                include_time: includeTime,
                locale: targetLocale
            })

            if (response.data.success) {
                return response.data.data.formatted
            } else {
                throw new Error(response.data.message)
            }
        } catch (err) {
            console.error('Failed to format date:', err)
            // Fallback to basic formatting
            const dateObj = new Date(date)
            return includeTime ? dateObj.toLocaleString() : dateObj.toLocaleDateString()
        }
    }

    /**
     * Get vessel-specific locale
     * @param {number} vesselId - Vessel ID
     */
    const getVesselLocale = async (vesselId) => {
        try {
            const response = await axios.get('/api/localization/vessel-locale', {
                params: { vessel_id: vesselId }
            })

            if (response.data.success) {
                return response.data.data
            } else {
                throw new Error(response.data.message)
            }
        } catch (err) {
            console.error('Failed to get vessel locale:', err)
            return null
        }
    }

    /**
     * Get report locale preferences
     * @param {string} reportType - Type of report (dvr, dcr, general)
     */
    const getReportLocale = async (reportType = 'general') => {
        try {
            const response = await axios.get('/api/localization/report-locale', {
                params: { report_type: reportType }
            })

            if (response.data.success) {
                return response.data.data
            } else {
                throw new Error(response.data.message)
            }
        } catch (err) {
            console.error('Failed to get report locale:', err)
            return null
        }
    }

    /**
     * Check if locale is valid/supported
     * @param {string} locale - Locale code to validate
     */
    const isValidLocale = (locale) => {
        return Object.keys(supportedLocales.value).includes(locale)
    }

    /**
     * Get locale display name
     * @param {string} locale - Locale code
     */
    const getLocaleDisplayName = (locale) => {
        return supportedLocales.value[locale]?.name || locale
    }

    /**
     * Get locale flag/icon
     * @param {string} locale - Locale code
     */
    const getLocaleFlag = (locale) => {
        return supportedLocales.value[locale]?.flag || '🌐'
    }

    /**
     * Toggle between supported locales
     */
    const toggleLocale = async () => {
        const localeKeys = Object.keys(supportedLocales.value)
        if (localeKeys.length < 2) return

        const currentIndex = localeKeys.indexOf(currentLocale.value)
        const nextIndex = (currentIndex + 1) % localeKeys.length
        const nextLocale = localeKeys[nextIndex]

        await setLocale(nextLocale)
    }

    // Computed properties
    const currentLocaleInfo = computed(() => {
        return supportedLocales.value[currentLocale.value] || {}
    })

    const availableLocales = computed(() => {
        return Object.entries(supportedLocales.value).map(([code, info]) => ({
            code,
            ...info
        }))
    })

    // Watch for locale changes and update document language
    watch(currentLocale, (newLocale) => {
        if (typeof document !== 'undefined') {
            document.documentElement.lang = newLocale
        }
    })

    // Initialize on first use
    if (Object.keys(supportedLocales.value).length === 0) {
        initializeLocalization()
    }

    return {
        // State
        currentLocale: computed(() => currentLocale.value),
        supportedLocales: computed(() => supportedLocales.value),
        isLoading: computed(() => isLoading.value),
        error: computed(() => error.value),
        currentLocaleInfo,
        availableLocales,

        // Methods
        initializeLocalization,
        setLocale,
        getActivityMessages,
        formatNumber,
        formatCurrency,
        formatDate,
        getVesselLocale,
        getReportLocale,
        isValidLocale,
        getLocaleDisplayName,
        getLocaleFlag,
        toggleLocale,

        // Vue i18n integration
        t
    }
}

/**
 * Plugin for Vue app to provide global localization
 */
export const LocalizationPlugin = {
    install(app) {
        const localization = useLocalization()
        app.config.globalProperties.$localization = localization
        app.provide('localization', localization)
    }
}