import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { userApi } from '@/api/user'
import Cookies from 'js-cookie'
import { ability, updateAbility } from '@/utils/ability'

/**
 * Pinia store untuk mengelola authentication flow
 * Menangani login, logout, token management, dan permissions
 */
export const useAuth = defineStore('auth', () => {
  // Initialize token langsung dari cookie
  const cookieToken = Cookies.get('token')
  
  // State
  const token = ref(cookieToken || null)
  const permissions = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  // Set axios header jika token ada
  if (cookieToken && window.axios) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${cookieToken}`
  }

  // Computed properties
  const isLoggedIn = computed(() => Boolean(token.value))
  const isAuthenticated = computed(() => Boolean(token.value))
  const userPermissions = computed(() => permissions.value)

  /**
   * Initialize auth state dari cookies
   */
  const initializeAuth = () => {
    const cookieToken = Cookies.get('token')
    if (cookieToken) {
      token.value = cookieToken
      // Set axios header jika token ada
      if (window.axios) {
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${cookieToken}`
      }
    }
  }

  /**
   * Set permissions user dan update ability
   * @param {Array} newPermissions - Array permissions
   */
  const setPermissions = (newPermissions) => {
    permissions.value = newPermissions
    updateAbility(ability, newPermissions)
  }

  /**
   * Update token dan simpan ke cookies
   * @param {string} newToken - JWT token baru
   */
  const updateToken = (newToken) => {
    token.value = newToken
    
    // Simpan token ke cookies dengan security flags
    Cookies.set('token', newToken, {
      expires: 7,
      secure: window.location.protocol === 'https:',
      sameSite: 'strict',
      path: '/'
    })
    
    // Set default authorization header untuk axios
    if (window.axios) {
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
    }
  }

  /**
   * Clear token dari cookies dan axios headers
   */
  const clearToken = () => {
    token.value = null
    
    // Clear cookies dengan berbagai path untuk memastikan terhapus
    Cookies.remove('token')
    Cookies.remove('token', { path: '/' })
    Cookies.remove('token', { path: '', domain: window.location.hostname })
    
    // Verifikasi cookie sudah terhapus
    const remainingToken = Cookies.get('token')
    if (remainingToken) {
      // console.warn('Token cookie masih ada setelah clearToken:', remainingToken)
    } else {
      // console.log('Token cookie berhasil dihapus')
    }
    
    // Clear axios headers
    if (window.axios) {
      delete window.axios.defaults.headers.common['Authorization']
    }
  }

  /**
   * Login user
   * @param {Object} credentials - Email dan password
   */
  const login = async (credentials) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.login(credentials)
      // console.log(response);
      // Set permissions dari response
      setPermissions(response.data.result.permissions)
      
      // Update token
      updateToken(response.data.result.access_token)
      
      // Update user data setelah login berhasil
      try {
        const { useUser } = await import('./useUser')
        const { updateUser } = useUser()
        updateUser(response.data.result.user)
        // console.log('User data updated after login')
      } catch (userError) {
        // console.warn('Failed to update user data after login:', userError)
      }
      
      // Initialize vessel data setelah login berhasil
      try {
        // Import useVessel di dalam fungsi untuk menghindari circular dependency
        const { useVessel } = await import('./useVessel')
        const { initializeVessels } = useVessel()
        await initializeVessels()
        // console.log('Vessel data initialized after login')
      } catch (vesselError) {
        // console.warn('Failed to initialize vessel data after login:', vesselError)
        // Tidak throw error karena ini bukan critical untuk login flow
      }
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Login gagal'
      // console.log(err)
      
      const sanitizedError = {
        message: err.response?.data?.message || 'Login failed',
        validation: err.response?.data?.errors || null,
        status: err.response?.status || 500
      }
      
      throw sanitizedError
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Forgot password
   * @param {Object} data - Email untuk reset password
   */
  const forgotPassword = async (data) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.forgotPassword(data)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Reset password gagal'
      
      const sanitizedError = {
        message: err.response?.data?.message || 'Password reset failed',
        validation: err.response?.data?.errors || null,
        status: err.response?.status || 500
      }
      
      throw sanitizedError
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Refresh token
   * @param {string} refreshToken - Refresh token
   */
  const refreshToken = async (refreshToken) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.refreshToken({ refresh_token: refreshToken })
      
      // Update token baru
      updateToken(response.data.access_token)
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Refresh token gagal'
      
      // Jika refresh gagal, logout user
      await logout()
      
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Logout user dan clear semua data authentication
   */
  const logout = async () => {
    try {
      await userApi.logout()
    } catch (err) {
      console.warn('Logout request failed, clearing session anyway.')
    }
    
    // Reset authentication state
    permissions.value = []
    error.value = null
    
    // Clear token
    clearToken()
    
    // Reset ability permissions
    updateAbility(ability, [])
    
    // Clear user data dari useUser store
    try {
      const { useUser } = await import('./useUser')
      const { clearUser } = useUser()
      clearUser()
    } catch (userError) {
      console.warn('Failed to clear user data during logout:', userError)
    }
    
    // Navigate to login - gunakan window.location untuk reliability
    try {
      // Coba gunakan router jika tersedia (untuk normal logout)
      const { useRouter } = await import('vue-router')
      const router = useRouter()
      await router.push('/login')
    } catch (routerError) {
      // Fallback ke window.location jika router tidak tersedia (untuk axios interceptor)
      console.warn('Router not available, using window.location for redirect')
      if (typeof window !== 'undefined') {
        window.location.href = '/login' // COMMENTED: Mencegah loop redirect
      }
    }
  }

  /**
   * Check apakah user memiliki permission tertentu
   * @param {string} permission - Permission yang dicek
   */
  const hasPermission = (permission) => {
    return permissions.value.includes(permission)
  }

  /**
   * Check apakah user memiliki salah satu dari permissions
   * @param {Array} permissionList - Array permissions yang dicek
   */
  const hasAnyPermission = (permissionList) => {
    return permissionList.some(permission => permissions.value.includes(permission))
  }

  /**
   * Check apakah user memiliki semua permissions
   * @param {Array} permissionList - Array permissions yang dicek
   */
  const hasAllPermissions = (permissionList) => {
    return permissionList.every(permission => permissions.value.includes(permission))
  }

  /**
   * Reset authentication state ke kondisi awal
   */
  const resetAuthState = () => {
    token.value = Cookies.get('token') || null
    permissions.value = []
    error.value = null
    isLoading.value = false
  }



  return {
    // State
    token,
    permissions: userPermissions,
    isLoading,
    error,
    
    // Computed
    isLoggedIn,
    isAuthenticated,
    
    // Methods
    setPermissions,
    updateToken,
    clearToken,
    login,
    forgotPassword,
    refreshToken,
    logout,
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    resetAuthState,
    initializeAuth
  }
})

// Export default untuk backward compatibility
export default useAuth