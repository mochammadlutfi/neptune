/**
 * Utility untuk inisialisasi aplikasi
 * Menangani setup auth, user profile, dan error handling
 */

import { useAuth } from '@/composables/auth/useAuth'
import { useUser } from '@/composables/auth/useUser'

/**
 * Inisialisasi authentication dan user profile
 * Menangani error dengan graceful fallback
 */
export async function initializeAuth() {
  try {
    // Import composables
    const authStore = useAuth()
    const userStore = useUser()
    
    // Check apakah user sudah login dan memiliki token valid
    if (!authStore.isLoggedIn || !authStore.token) {
      // console.log('No valid token found, skipping user profile fetch')
      return { success: false, reason: 'no_token' }
    }
    
    // Set axios authorization header
    if (window.axios && authStore.token) {
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${authStore.token}`
    }
    
    // Fetch user profile jika belum ada
    if (!userStore.user) {
      try {
        await userStore.getUser()
        return { success: true, user: userStore.user }
      } catch (error) {

        if (error.status === 401) {
          // Session expired - axios interceptor akan handle logout dan redirect
          return { success: false, reason: 'session_expired', error }
        } else {
          
          // Clear auth state untuk safety pada error non-401
          try {
            authStore.clearToken()
            userStore.clearUser()
            // console.log('Auth state cleared due to unexpected error')
          } catch (clearError) {
            // console.warn('Failed to clear auth state:', clearError)
          }
          
          return { success: false, reason: 'fetch_error', error }
        }
      }
    } else {
      return { success: true, user: userStore.user }
    }
  } catch (error) {
    console.error('Critical error during auth initialization:', error)
    return { success: false, reason: 'critical_error', error }
  }
}

/**
 * Inisialisasi lengkap aplikasi
 * Termasuk auth, user profile, dan setup lainnya
 */
export async function initializeApp() {
  console.log('Initializing NEPTUNE application...')
  
  try {
    // Initialize authentication
    const authResult = await initializeAuth()
    
    if (!authResult.success) {
      console.log(`Auth initialization failed: ${authResult.reason}`)
      
      // Untuk session expired, tidak perlu action tambahan
      // Axios interceptor sudah menangani redirect
      if (authResult.reason === 'session_expired') {
        return { success: false, reason: 'session_expired' }
      }
      
      // Untuk error lain, mungkin perlu handling khusus
      return authResult
    }
    
    return { success: true, user: authResult.user }
    
  } catch (error) {
    return { success: false, reason: 'initialization_failed', error }
  }
}

/**
 * Utility untuk check health aplikasi
 * Memverifikasi bahwa semua komponen penting berfungsi
 */
export function checkAppHealth() {
  const health = {
    axios: !!window.axios,
    auth: false,
    user: false,
    timestamp: new Date().toISOString()
  }
  
  try {
    const authStore = useAuth()
    health.auth = authStore.isLoggedIn
    
    const userStore = useUser()
    health.user = !!userStore.user
  } catch (error) {
    console.warn('Health check failed for auth/user stores:', error)
  }
  
  return health
}

/**
 * Utility untuk recovery dari error state
 * Mencoba memulihkan aplikasi dari kondisi error
 */
export async function recoverFromError(errorType) {
  
  switch (errorType) {
    case 'session_expired':
      // Untuk session expired, redirect ke login sudah ditangani axios interceptor
      if (typeof window !== 'undefined') {
        // window.location.href = '/login' // COMMENTED: Mencegah loop redirect
      }
      break
      
    case 'fetch_error':
      // Untuk fetch error, coba initialize ulang
      return await initializeAuth()
      
    case 'critical_error':
      // Untuk critical error, reload aplikasi
      if (typeof window !== 'undefined') {
        // window.location.reload()
      }
      break
      
    default:
      return { success: false, reason: 'unknown_error_type' }
  }
}

export default {
  initializeAuth,
  initializeApp,
  checkAppHealth,
  recoverFromError
}