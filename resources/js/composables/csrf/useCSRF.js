import axios from 'axios'
import { ref, computed } from 'vue'

/**
 * Composable untuk menangani CSRF token management
 * Menyediakan fungsi-fungsi untuk refresh token dan handling error 419
 */
export function useCSRF() {
  // State untuk tracking CSRF token
  const isRefreshing = ref(false)
  const lastRefreshTime = ref(null)
  
  /**
   * Mendapatkan CSRF token dari meta tag
   * @returns {string|null} CSRF token atau null jika tidak ditemukan
   */
  const getCSRFToken = () => {
    const tokenMeta = document.head.querySelector('meta[name="csrf-token"]')
    return tokenMeta ? tokenMeta.content : null
  }
  
  /**
   * Update CSRF token di meta tag dan axios headers
   * @param {string} newToken - Token CSRF yang baru
   */
  const updateCSRFToken = (newToken) => {
    // Update meta tag
    const tokenMeta = document.head.querySelector('meta[name="csrf-token"]')
    if (tokenMeta) {
      tokenMeta.content = newToken
    }
    
    // Update axios default headers
    axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken
    
    // console.log('CSRF token updated successfully')
  }
  
  /**
   * Refresh CSRF token dari server
   * @returns {Promise<string>} Promise yang resolve dengan token baru
   */
  const refreshCSRFToken = async () => {
    if (isRefreshing.value) {
      // Jika sedang refresh, tunggu sampai selesai
      return new Promise((resolve) => {
        const checkInterval = setInterval(() => {
          if (!isRefreshing.value) {
            clearInterval(checkInterval)
            resolve(getCSRFToken())
          }
        }, 100)
      })
    }
    
    try {
      isRefreshing.value = true
      
      // Request CSRF cookie dari Laravel Sanctum
      await axios.get('/base/csrf-cookie', {
        baseURL: window.location.origin,
        withCredentials: true,
        timeout: 5000 // Tambahkan timeout 5 detik
      })
      
      // Tunggu sebentar untuk memastikan cookie ter-set
      await new Promise(resolve => setTimeout(resolve, 100))
      
      // Dapatkan token baru dari meta tag (jika ada update dari server)
      const newToken = getCSRFToken()
      
      if (newToken) {
        lastRefreshTime.value = new Date()
        return newToken
      } else {
        throw new Error('CSRF token not found after refresh')
      }
      
    } catch (error) {
      console.error('Failed to refresh CSRF token:', error)
      throw error
    } finally {
      isRefreshing.value = false
    }
  }
  
  /**
   * Handle error 419 CSRF token mismatch
   * @param {Object} originalRequest - Request config yang gagal
   * @returns {Promise} Promise dari retry request
   */
  const handleCSRFError = async (originalRequest) => {
    try {
      console.warn('CSRF token mismatch detected, refreshing token...')
      
      // Refresh CSRF token
      const newToken = await refreshCSRFToken()
      
      // Update request headers dengan token baru
      if (!originalRequest.headers) {
        originalRequest.headers = {}
      }
      originalRequest.headers['X-CSRF-TOKEN'] = newToken
      
      // Retry original request
      // console.log('Retrying request with new CSRF token')
      return axios(originalRequest)
      
    } catch (refreshError) {
      // console.error('Failed to handle CSRF error:', refreshError)
      
      // Jika semua gagal, reload halaman sebagai last resort
      if (typeof window !== 'undefined') {
        console.warn('Reloading page to get fresh CSRF token')
        setTimeout(() => {
          window.location.reload()
        }, 1000)
      }
      
      return Promise.reject(refreshError)
    }
  }
  
  /**
   * Cek apakah CSRF token masih valid (berdasarkan waktu refresh terakhir)
   * @returns {boolean} True jika token kemungkinan masih valid
   */
  const isTokenValid = computed(() => {
    if (!lastRefreshTime.value) return true // Belum pernah refresh, anggap valid
    
    const now = new Date()
    const timeDiff = now - lastRefreshTime.value
    const maxAge = 2 * 60 * 60 * 1000 // 2 jam dalam milliseconds
    
    return timeDiff < maxAge
  })
  
  /**
   * Initialize CSRF token dari meta tag saat composable pertama kali digunakan
   */
  const initializeCSRF = () => {
    const token = getCSRFToken()
    if (token) {
      axios.defaults.headers.common['X-CSRF-TOKEN'] = token
      // console.log('CSRF token initialized from meta tag')
    } else {
      console.warn('CSRF token not found in meta tag')
    }
  }
  
  // Auto initialize saat composable dibuat
  initializeCSRF()
  
  return {
    // State
    isRefreshing: computed(() => isRefreshing.value),
    isTokenValid,
    lastRefreshTime: computed(() => lastRefreshTime.value),
    
    // Methods
    getCSRFToken,
    updateCSRFToken,
    refreshCSRFToken,
    handleCSRFError,
    initializeCSRF
  }
}

/**
 * Export default instance untuk penggunaan global
 */
export default useCSRF