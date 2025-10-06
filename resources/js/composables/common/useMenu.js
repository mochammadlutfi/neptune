import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuth } from '@/composables/auth'

/**
 * Composable untuk mengelola menu navigasi aplikasi
 * Menggantikan menuStore.js dengan persistent state dan fitur yang lebih lengkap
 */
export const useMenu = defineStore('menu', () => {
    // ===== STATE =====
    const menus = ref([])
    const isLoaded = ref(false)
    const isLoading = ref(false)
    const error = ref(null)
    
    // Navigation state
    const activePath = ref('')
    const activeMenu = ref(null)
    const breadcrumbs = ref([])
    const collapsedMenus = ref([])
    
    // Menu preferences
    const menuPreferences = ref({
        sidebarCollapsed: false,
        favoriteMenus: [],
        recentMenus: [],
        menuOrder: 'default' // 'default', 'alphabetical', 'custom'
    })
    
    // ===== GETTERS =====
    
    /**
     * Menu yang dapat diakses berdasarkan permission user
     */
    const accessibleMenus = computed(() => {
        const { userPermissions } = useAuth()
        console.log(userPermissions.value)
        if (!userPermissions.value) return []
        
        return menus.value.filter(menu => {
            // Jika menu tidak memerlukan permission, tampilkan
            if (!menu.permission) return true
            
            // Check permission user
            return userPermissions.value?.includes(menu.permission) || false
        })
    })
    
    /**
     * Menu berdasarkan module/kategori
     */
    const menusByModule = computed(() => {
        const grouped = {}
        accessibleMenus.value.forEach(menu => {
            const module = menu.module || 'general'
            if (!grouped[module]) {
                grouped[module] = []
            }
            grouped[module].push(menu)
        })
        return grouped
    })
    
    /**
     * Menu operasional (production, equipment, hse, dll)
     */
    const operationalMenus = computed(() => {
        return accessibleMenus.value.filter(menu => 
            menu.category === 'operational' || menu.module === 'production'
        )
    })
    
    /**
     * Menu administrasi (settings, users, reports)
     */
    const administrativeMenus = computed(() => {
        return accessibleMenus.value.filter(menu => 
            menu.category === 'administrative' || menu.module === 'admin'
        )
    })
    
    /**
     * Menu favorit user
     */
    const favoriteMenus = computed(() => {
        return accessibleMenus.value.filter(menu => 
            menuPreferences.value.favoriteMenus.includes(menu.id)
        )
    })
    
    /**
     * Menu yang baru diakses
     */
    const recentMenus = computed(() => {
        const recentIds = menuPreferences.value.recentMenus
        return recentIds.map(id => 
            accessibleMenus.value.find(menu => menu.id === id)
        ).filter(Boolean)
    })
    
    /**
     * Menu yang sedang aktif berdasarkan path
     */
    const currentActiveMenu = computed(() => {
        return accessibleMenus.value.find(menu => 
            menu.route === activePath.value || 
            activePath.value.startsWith(menu.route)
        )
    })
    
    // ===== ACTIONS =====
    
    /**
     * Mengambil menu dari backend API
     */
    const fetchMenus = async () => {
        const { isLoggedIn } = useAuth()
        
        // Pastikan user sudah login
        if (!isLoggedIn.value) {
            console.warn('Cannot fetch menus: User not authenticated')
            return
        }
        
        // Jangan fetch jika sudah loading atau sudah loaded
        if (isLoading.value || isLoaded.value) {
            return
        }
        
        isLoading.value = true
        error.value = null
        
        try {
            const response = await axios.get('/menu')
            menus.value = response.data
            isLoaded.value = true
            
            console.log('Menus loaded successfully:', menus.value.length, 'items')
        } catch (err) {
            error.value = err.message
            console.error('Failed to fetch menus:', err)
            
            // Handle authentication errors
            if (err.response?.status === 401 || err.response?.status === 403) {
                console.warn('Menu fetch failed due to authentication error')
                const { logout } = useAuth()
                logout()
            }
        } finally {
            isLoading.value = false
        }
    }
    
    /**
     * Set active path untuk highlight menu
     * @param {string} path - Route path yang aktif
     */
    const setActivePath = (path) => {
        activePath.value = path
        
        // Update recent menus
        const menu = currentActiveMenu.value
        if (menu) {
            addToRecentMenus(menu.id)
        }
        
        // Generate breadcrumbs
        generateBreadcrumbs(path)
    }
    
    /**
     * Generate breadcrumbs berdasarkan active path
     * @param {string} path - Route path
     */
    const generateBreadcrumbs = (path) => {
        const pathSegments = path.split('/').filter(Boolean)
        const crumbs = []
        
        let currentPath = ''
        pathSegments.forEach(segment => {
            currentPath += `/${segment}`
            const menu = accessibleMenus.value.find(m => m.route === currentPath)
            
            if (menu) {
                crumbs.push({
                    label: menu.label,
                    route: menu.route,
                    icon: menu.icon
                })
            } else {
                // Fallback untuk segment yang tidak ada di menu
                crumbs.push({
                    label: segment.charAt(0).toUpperCase() + segment.slice(1),
                    route: currentPath
                })
            }
        })
        
        breadcrumbs.value = crumbs
    }
    
    /**
     * Toggle collapse status menu
     * @param {string|number} menuId - ID menu yang akan di-toggle
     */
    const toggleMenuCollapse = (menuId) => {
        const index = collapsedMenus.value.indexOf(menuId)
        if (index > -1) {
            collapsedMenus.value.splice(index, 1)
        } else {
            collapsedMenus.value.push(menuId)
        }
    }
    
    /**
     * Check apakah menu dalam status collapsed
     * @param {string|number} menuId - ID menu
     * @returns {boolean}
     */
    const isMenuCollapsed = (menuId) => {
        return collapsedMenus.value.includes(menuId)
    }
    
    /**
     * Tambah menu ke favorit
     * @param {string|number} menuId - ID menu
     */
    const addToFavorites = (menuId) => {
        if (!menuPreferences.value.favoriteMenus.includes(menuId)) {
            menuPreferences.value.favoriteMenus.push(menuId)
        }
    }
    
    /**
     * Hapus menu dari favorit
     * @param {string|number} menuId - ID menu
     */
    const removeFromFavorites = (menuId) => {
        const index = menuPreferences.value.favoriteMenus.indexOf(menuId)
        if (index > -1) {
            menuPreferences.value.favoriteMenus.splice(index, 1)
        }
    }
    
    /**
     * Toggle status favorit menu
     * @param {string|number} menuId - ID menu
     */
    const toggleFavorite = (menuId) => {
        if (menuPreferences.value.favoriteMenus.includes(menuId)) {
            removeFromFavorites(menuId)
        } else {
            addToFavorites(menuId)
        }
    }
    
    /**
     * Tambah menu ke recent menus
     * @param {string|number} menuId - ID menu
     */
    const addToRecentMenus = (menuId) => {
        const recentMenus = menuPreferences.value.recentMenus
        
        // Hapus jika sudah ada
        const existingIndex = recentMenus.indexOf(menuId)
        if (existingIndex > -1) {
            recentMenus.splice(existingIndex, 1)
        }
        
        // Tambah di awal array
        recentMenus.unshift(menuId)
        
        // Batasi maksimal 10 recent menus
        if (recentMenus.length > 10) {
            recentMenus.splice(10)
        }
    }
    
    /**
     * Cari menu berdasarkan query
     * @param {string} query - Query pencarian
     * @returns {Array} Menu yang cocok dengan query
     */
    const searchMenus = (query) => {
        if (!query.trim()) return accessibleMenus.value
        
        const searchTerm = query.toLowerCase()
        return accessibleMenus.value.filter(menu => 
            menu.label.toLowerCase().includes(searchTerm) ||
            menu.description?.toLowerCase().includes(searchTerm) ||
            menu.keywords?.some(keyword => 
                keyword.toLowerCase().includes(searchTerm)
            )
        )
    }
    
    /**
     * Get menu berdasarkan permission
     * @param {Object} ability - CASL ability object
     * @returns {Array} Menu yang dapat diakses
     */
    const getMenusByPermission = (ability) => {
        return menus.value.filter(menu => {
            if (!menu.permission) return true
            return ability.can('view', menu.permission)
        })
    }
    
    /**
     * Get menu berdasarkan module
     * @param {string} module - Nama module
     * @returns {Array} Menu dalam module tersebut
     */
    const getMenusByModule = (module) => {
        return accessibleMenus.value.filter(menu => menu.module === module)
    }
    
    /**
     * Update preferensi menu
     * @param {Object} preferences - Preferensi baru
     */
    const updateMenuPreferences = (preferences) => {
        menuPreferences.value = { ...menuPreferences.value, ...preferences }
    }
    
    /**
     * Reset semua state menu (dipanggil saat logout)
     */
    const resetMenu = () => {
        menus.value = []
        isLoaded.value = false
        isLoading.value = false
        error.value = null
        activePath.value = ''
        activeMenu.value = null
        breadcrumbs.value = []
        collapsedMenus.value = []
        
        // Reset preferences ke default
        menuPreferences.value = {
            sidebarCollapsed: false,
            favoriteMenus: [],
            recentMenus: [],
            menuOrder: 'default'
        }
    }
    
    return {
        // State
        menus,
        isLoaded,
        isLoading,
        error,
        activePath,
        activeMenu,
        breadcrumbs,
        collapsedMenus,
        menuPreferences,
        
        // Getters
        accessibleMenus,
        menusByModule,
        operationalMenus,
        administrativeMenus,
        favoriteMenus,
        recentMenus,
        currentActiveMenu,
        
        // Actions
        fetchMenus,
        setActivePath,
        generateBreadcrumbs,
        toggleMenuCollapse,
        isMenuCollapsed,
        addToFavorites,
        removeFromFavorites,
        toggleFavorite,
        addToRecentMenus,
        searchMenus,
        getMenusByPermission,
        getMenusByModule,
        updateMenuPreferences,
        resetMenu,
        
        // Backward compatibility
        $reset: resetMenu
    }
}, {
    persist: {
        key: 'neptune-menu',
        storage: localStorage,
        paths: ['menuPreferences', 'collapsedMenus', 'activePath']
    }
})

// Export default untuk backward compatibility
export default useMenu