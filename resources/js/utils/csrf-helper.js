/**
 * CSRF Helper Utilities
 * Menyediakan fungsi-fungsi helper untuk menangani CSRF token di komponen Vue
 */

import { useCSRF } from '@/composables/csrf/useCSRF'
import { ElMessage, ElNotification } from 'element-plus'

/**
 * Handle CSRF error dengan user feedback
 * @param {Error} error - Error object dari axios
 * @param {Object} options - Options untuk customization
 * @returns {Promise} Promise yang resolve setelah handling selesai
 */
export const handleCSRFErrorWithFeedback = async (error, options = {}) => {
  const {
    showMessage = true,
    showNotification = false,
    messageType = 'warning',
    retryCallback = null
  } = options
  
  if (error.status === 419) {
    const { handleCSRFError } = useCSRF()
    
    try {
      if (showMessage) {
        ElMessage({
          type: messageType,
          message: 'CSRF token expired. Refreshing and retrying...',
          duration: 3000
        })
      }
      
      if (showNotification) {
        ElNotification({
          title: 'Security Token Refresh',
          message: 'Security token has been refreshed automatically.',
          type: 'info',
          duration: 4000
        })
      }
      
      // Handle CSRF error dan retry
      const result = await handleCSRFError(error.original.config)
      
      if (showMessage) {
        ElMessage({
          type: 'success',
          message: 'Request completed successfully after token refresh.',
          duration: 2000
        })
      }
      
      // Jalankan callback jika ada
      if (retryCallback && typeof retryCallback === 'function') {
        await retryCallback(result)
      }
      
      return result
      
    } catch (refreshError) {
      if (showMessage) {
        ElMessage({
          type: 'error',
          message: 'Failed to refresh security token. Please reload the page.',
          duration: 5000
        })
      }
      
      throw refreshError
    }
  }
  
  // Jika bukan error 419, lempar kembali error
  throw error
}

/**
 * Wrapper untuk axios request dengan automatic CSRF handling
 * @param {Function} requestFunction - Fungsi yang mengembalikan axios promise
 * @param {Object} options - Options untuk error handling
 * @returns {Promise} Promise dari request
 */
export const withCSRFHandling = async (requestFunction, options = {}) => {
  try {
    return await requestFunction()
  } catch (error) {
    if (error.status === 419) {
      return await handleCSRFErrorWithFeedback(error, options)
    }
    throw error
  }
}

/**
 * Mixin untuk Vue components yang membutuhkan CSRF handling
 */
export const CSRFMixin = {
  methods: {
    /**
     * Handle CSRF error dalam component
     * @param {Error} error - Error dari axios
     * @param {Object} options - Options untuk handling
     */
    async handleCSRFError(error, options = {}) {
      return await handleCSRFErrorWithFeedback(error, {
        showMessage: true,
        showNotification: false,
        ...options
      })
    },
    
    /**
     * Execute request dengan automatic CSRF handling
     * @param {Function} requestFn - Function yang return axios promise
     * @param {Object} options - Options untuk error handling
     */
    async executeWithCSRFHandling(requestFn, options = {}) {
      return await withCSRFHandling(requestFn, {
        showMessage: true,
        ...options
      })
    }
  }
}

/**
 * Composable untuk CSRF handling dalam setup function
 * @returns {Object} Object dengan methods untuk CSRF handling
 */
export const useCSRFHandling = () => {
  const { handleCSRFError, refreshCSRFToken, isRefreshing } = useCSRF()
  
  /**
   * Execute request dengan error handling dan user feedback
   * @param {Function} requestFn - Function yang return axios promise
   * @param {Object} options - Options untuk customization
   */
  const executeRequest = async (requestFn, options = {}) => {
    const {
      showLoading = false,
      loadingText = 'Processing...',
      successMessage = null,
      errorMessage = 'Request failed'
    } = options
    
    try {
      if (showLoading) {
        ElMessage({
          type: 'info',
          message: loadingText,
          duration: 0 // Persistent until manually closed
        })
      }
      
      const result = await requestFn()
      
      if (showLoading) {
        ElMessage.closeAll()
      }
      
      if (successMessage) {
        ElMessage({
          type: 'success',
          message: successMessage,
          duration: 3000
        })
      }
      
      return result
      
    } catch (error) {
      if (showLoading) {
        ElMessage.closeAll()
      }
      
      if (error.status === 419) {
        return await handleCSRFErrorWithFeedback(error, {
          showMessage: true,
          retryCallback: async () => {
            if (successMessage) {
              ElMessage({
                type: 'success',
                message: successMessage,
                duration: 3000
              })
            }
          }
        })
      }
      
      ElMessage({
        type: 'error',
        message: errorMessage,
        duration: 4000
      })
      
      throw error
    }
  }
  
  return {
    executeRequest,
    handleCSRFError,
    refreshCSRFToken,
    isRefreshing
  }
}

/**
 * Decorator untuk methods yang membutuhkan CSRF protection
 * @param {Object} options - Options untuk handling
 */
export const withCSRFProtection = (options = {}) => {
  return function(target, propertyKey, descriptor) {
    const originalMethod = descriptor.value
    
    descriptor.value = async function(...args) {
      try {
        return await originalMethod.apply(this, args)
      } catch (error) {
        if (error.status === 419) {
          return await handleCSRFErrorWithFeedback(error, options)
        }
        throw error
      }
    }
    
    return descriptor
  }
}

export default {
  handleCSRFErrorWithFeedback,
  withCSRFHandling,
  CSRFMixin,
  useCSRFHandling,
  withCSRFProtection
}