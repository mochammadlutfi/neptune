import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import Cookies from 'js-cookie'
import axios from 'axios'

const APP_COOKIE_KEY = 'APP'
const LAYOUT_COOKIE_KEY = 'LAYOUT'

/**
 * Composable untuk mengelola pengaturan aplikasi dan layout
 * Menggabungkan fungsi dari base.js store dengan persistent state
 */
export const useSetting = defineStore('setting', () => {
    // ===== STATE =====
    const initialized = ref(false)
    const loading = ref(false)
    
    // Pengaturan aplikasi utama
    const app = ref({
        app_name: "",
        date_format: "",
        time_format: "",
        timezone: "",
        logo_light: null,
        logo_dark: null,
        locale: null,
        locales: [],
        currency: null,
    })
    
    // Pengaturan layout dan UI
    const layout = ref({
        header: true,
        sidebar: true,
        sideOverlay: true,
        footer: true
    })
    
    const settings = ref({
        colorTheme: '',
        sidebarLeft: true,
        sidebarMini: false,
        sidebarDark: true,
        sidebarVisibleDesktop: true,
        sidebarVisibleMobile: false,
        sideOverlayVisible: false,
        sideOverlayHoverable: false,
        pageOverlay: true,
        headerFixed: true,
        headerDark: false,
        headerSearch: false,
        headerLoader: false,
        pageLoader: false,
        rtlSupport: false,
        sideTransitions: true,
        mainContent: ''
    })
    
    // ===== GETTERS =====
    const isInitialized = computed(() => {
        return app.value.app_name !== "" && initialized.value
    })
    
    const locales = computed(() => {
        return app.value.locales
    })
    
    const locale = computed(() => {
        return app.value.locale
    })
    
    const appName = computed(() => {
        return app.value.app_name
    })
    
    const dateFormat = computed(() => {
        return app.value.date_format
    })
    
    const timeFormat = computed(() => {
        return app.value.time_format
    })
    
    const timezone = computed(() => {
        return app.value.timezone
    })
    
    const currency = computed(() => {
        return app.value.currency
    })
    
    const logoLight = computed(() => {
        return app.value.logo_light
    })
    
    const logoDark = computed(() => {
        return app.value.logo_dark
    })
    
    // ===== ACTIONS =====
    
    /**
     * Inisialisasi aplikasi dengan memuat pengaturan dari cookie atau API
     */
    const initApp = async () => {
        loading.value = true
        
        try {
            // Coba muat dari cookie terlebih dahulu
            const cookieData = Cookies.get(APP_COOKIE_KEY)
            const layoutData = Cookies.get(LAYOUT_COOKIE_KEY)
            
            if (cookieData) {
                app.value = JSON.parse(cookieData)
            }
            
            if (layoutData) {
                const parsedLayout = JSON.parse(layoutData)
                layout.value = { ...layout.value, ...parsedLayout.layout }
                settings.value = { ...settings.value, ...parsedLayout.settings }
            }
            
            // Jika tidak ada data di cookie, fetch dari API
            if (!cookieData) {
                await fetchSettings()
            }
            
            initialized.value = true
        } catch (error) {
            console.error('Error initializing app:', error)
        } finally {
            loading.value = false
        }
    }
    
    /**
     * Mengambil pengaturan dari API server
     */
    const fetchSettings = async () => {
        try {
            const response = await axios.get('/base')
            app.value = response.data
            saveAppSettingsToCookie()
        } catch (error) {
            console.error('Failed to load settings from API:', error)
            throw error
        }
    }
    
    /**
     * Reinisialisasi aplikasi dengan menghapus cache dan memuat ulang dari API
     */
    const reinitApp = async () => {
        initialized.value = false
        Cookies.remove(APP_COOKIE_KEY)
        Cookies.remove(LAYOUT_COOKIE_KEY)
        await fetchSettings()
        initialized.value = true
    }
    
    /**
     * Menyimpan pengaturan aplikasi ke cookie
     */
    const saveAppSettingsToCookie = () => {
        Cookies.set(APP_COOKIE_KEY, JSON.stringify(app.value), {
            expires: 7
        })
    }
    
    /**
     * Menyimpan pengaturan layout ke cookie
     */
    const saveLayoutSettingsToCookie = () => {
        const layoutData = {
            layout: layout.value,
            settings: settings.value
        }
        Cookies.set(LAYOUT_COOKIE_KEY, JSON.stringify(layoutData), {
            expires: 30
        })
    }
    
    /**
     * Mengatur locale aplikasi
     * @param {string} newLocale - Locale baru yang akan diset
     */
    const setLocale = (newLocale) => {
        app.value.locale = newLocale
        saveAppSettingsToCookie()
    }
    
    /**
     * Update pengaturan aplikasi
     * @param {Object} newSettings - Pengaturan baru
     */
    const updateAppSettings = (newSettings) => {
        app.value = { ...app.value, ...newSettings }
        saveAppSettingsToCookie()
    }
    
    /**
     * Update pengaturan layout
     * @param {Object} newLayout - Layout settings baru
     */
    const updateLayoutSettings = (newLayout) => {
        layout.value = { ...layout.value, ...newLayout }
        saveLayoutSettingsToCookie()
    }
    
    /**
     * Update pengaturan UI
     * @param {Object} newSettings - UI settings baru
     */
    const updateUISettings = (newSettings) => {
        settings.value = { ...settings.value, ...newSettings }
        saveLayoutSettingsToCookie()
    }
    
    /**
     * Toggle sidebar visibility
     */
    const toggleSidebar = () => {
        settings.value.sidebarVisibleDesktop = !settings.value.sidebarVisibleDesktop
        saveLayoutSettingsToCookie()
    }
    
    /**
     * Toggle sidebar mini mode
     */
    const toggleSidebarMini = () => {
        settings.value.sidebarMini = !settings.value.sidebarMini
        saveLayoutSettingsToCookie()
    }
    
    /**
     * Set theme color
     * @param {string} theme - Nama theme
     */
    const setTheme = (theme) => {
        settings.value.colorTheme = theme
        saveLayoutSettingsToCookie()
    }
    
    /**
     * Reset semua pengaturan ke default
     */
    const resetSettings = () => {
        initialized.value = false
        loading.value = false
        
        app.value = {
            app_name: "",
            date_format: "",
            time_format: "",
            timezone: "",
            logo_light: null,
            logo_dark: null,
            locale: null,
            locales: [],
            currency: null,
        }
        
        layout.value = {
            header: true,
            sidebar: true,
            sideOverlay: true,
            footer: true
        }
        
        settings.value = {
            colorTheme: '',
            sidebarLeft: true,
            sidebarMini: false,
            sidebarDark: true,
            sidebarVisibleDesktop: true,
            sidebarVisibleMobile: false,
            sideOverlayVisible: false,
            sideOverlayHoverable: false,
            pageOverlay: true,
            headerFixed: true,
            headerDark: false,
            headerSearch: false,
            headerLoader: false,
            pageLoader: false,
            rtlSupport: false,
            sideTransitions: true,
            mainContent: ''
        }
        
        // Hapus cookies
        Cookies.remove(APP_COOKIE_KEY)
        Cookies.remove(LAYOUT_COOKIE_KEY)
    }
    
    return {
        // State
        initialized,
        loading,
        app,
        layout,
        settings,
        
        // Getters
        isInitialized,
        locales,
        locale,
        appName,
        dateFormat,
        timeFormat,
        timezone,
        currency,
        logoLight,
        logoDark,
        
        // Actions
        initApp,
        fetchSettings,
        reinitApp,
        saveAppSettingsToCookie,
        saveLayoutSettingsToCookie,
        setLocale,
        updateAppSettings,
        updateLayoutSettings,
        updateUISettings,
        toggleSidebar,
        toggleSidebarMini,
        setTheme,
        resetSettings
    }
}, {
    persist: {
        key: 'neptune-settings',
        storage: localStorage,
        paths: ['app', 'layout', 'settings']
    }
})

// Export default untuk backward compatibility
export default useSetting