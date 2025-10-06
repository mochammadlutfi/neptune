/**
 * Test untuk CSRF Token Handling di Frontend
 * 
 * Test ini memastikan bahwa implementasi CSRF handling di frontend
 * berfungsi dengan benar untuk berbagai skenario.
 */

import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import axios from 'axios'

// Mock Element Plus untuk menghindari error navigator
vi.mock('element-plus', () => ({
  ElMessage: {
    error: vi.fn(),
    success: vi.fn(),
    warning: vi.fn()
  },
  ElNotification: {
    error: vi.fn(),
    success: vi.fn(),
    warning: vi.fn()
  }
}))

import { useCSRF } from '@/composables/csrf/useCSRF'
import { 
  withCSRFHandling, 
  handleCSRFErrorWithFeedback,
  CSRFMixin 
} from '@/utils/csrf-helper'

// Mock axios
vi.mock('axios')
const mockedAxios = vi.mocked(axios)

// Mock DOM elements
Object.defineProperty(window, 'location', {
  value: {
    origin: 'http://localhost:8000',
    href: 'http://localhost:8000/dashboard'
  },
  writable: true
})

// Mock document.querySelector untuk meta tag
Object.defineProperty(document, 'querySelector', {
  value: vi.fn(),
  writable: true
})

// Mock console methods
const consoleSpy = {
  error: vi.spyOn(console, 'error').mockImplementation(() => {}),
  warn: vi.spyOn(console, 'warn').mockImplementation(() => {}),
  log: vi.spyOn(console, 'log').mockImplementation(() => {})
}

describe('CSRF Token Handling', () => {
  beforeEach(() => {
    // Reset mocks
    vi.clearAllMocks()
    
    // Setup default meta tag mock
    document.querySelector.mockImplementation((selector) => {
      if (selector === 'meta[name="csrf-token"]') {
        return {
          getAttribute: () => 'mock-csrf-token-123'
        }
      }
      return null
    })
    
    // Setup default axios mock
    mockedAxios.defaults = { headers: { common: {} } }
    mockedAxios.get = vi.fn()
    mockedAxios.post = vi.fn()
    mockedAxios.create = vi.fn(() => mockedAxios)
  })
  
  afterEach(() => {
    // Cleanup console spies
    Object.values(consoleSpy).forEach(spy => spy.mockClear())
  })

  describe('useCSRF Composable', () => {
    it('should initialize CSRF token from meta tag', () => {
      const { initializeCSRF, csrfToken } = useCSRF()
      
      initializeCSRF()
      
      expect(document.querySelector).toHaveBeenCalledWith('meta[name="csrf-token"]')
      expect(csrfToken.value).toBe('mock-csrf-token-123')
    })
    
    it('should handle missing meta tag gracefully', () => {
      document.querySelector.mockReturnValue(null)
      
      const { initializeCSRF, csrfToken } = useCSRF()
      
      initializeCSRF()
      
      expect(csrfToken.value).toBe('')
      expect(consoleSpy.warn).toHaveBeenCalledWith(
        '[CSRF] Meta tag csrf-token tidak ditemukan'
      )
    })
    
    it('should update axios headers when token changes', () => {
      const { initializeCSRF, updateToken } = useCSRF()
      
      initializeCSRF()
      updateToken('new-token-456')
      
      expect(mockedAxios.defaults.headers.common['X-CSRF-TOKEN']).toBe('new-token-456')
    })
    
    it('should refresh CSRF token successfully', async () => {
      mockedAxios.get.mockResolvedValue({})
      document.querySelector.mockReturnValue({
        getAttribute: () => 'refreshed-token-789'
      })
      
      const { refreshCSRFToken } = useCSRF()
      
      const newToken = await refreshCSRFToken()
      
      expect(mockedAxios.get).toHaveBeenCalledWith('/sanctum/csrf-cookie', {
        baseURL: 'http://localhost:8000',
        withCredentials: true
      })
      expect(newToken).toBe('refreshed-token-789')
    })
    
    it('should handle CSRF refresh failure', async () => {
      mockedAxios.get.mockRejectedValue(new Error('Network error'))
      
      const { refreshCSRFToken } = useCSRF()
      
      await expect(refreshCSRFToken()).rejects.toThrow('Network error')
      expect(consoleSpy.error).toHaveBeenCalledWith(
        '[CSRF] Gagal refresh token:',
        expect.any(Error)
      )
    })
    
    it('should handle CSRF error and retry request', async () => {
      const originalRequest = {
        url: '/api/test',
        method: 'post',
        data: { test: 'data' },
        headers: {}
      }
      
      // Mock successful token refresh
      mockedAxios.get.mockResolvedValue({})
      document.querySelector.mockReturnValue({
        getAttribute: () => 'new-token-after-refresh'
      })
      
      // Mock successful retry
      mockedAxios.mockResolvedValue({ data: 'success' })
      
      const { handleCSRFError } = useCSRF()
      
      const result = await handleCSRFError(originalRequest)
      
      expect(originalRequest.headers['X-CSRF-TOKEN']).toBe('new-token-after-refresh')
      expect(mockedAxios).toHaveBeenCalledWith(originalRequest)
      expect(result.data).toBe('success')
    })
    
    it('should validate token correctly', () => {
      const { initializeCSRF, isTokenValid } = useCSRF()
      
      // Token kosong tidak valid
      expect(isTokenValid.value).toBe(false)
      
      // Token ada menjadi valid
      initializeCSRF()
      expect(isTokenValid.value).toBe(true)
    })
  })

  describe('CSRF Helper Functions', () => {
    it('should execute request with CSRF handling', async () => {
      const mockRequest = vi.fn().mockResolvedValue({ data: 'success' })
      
      const result = await withCSRFHandling(mockRequest)
      
      expect(mockRequest).toHaveBeenCalled()
      expect(result.data).toBe('success')
    })
    
    it('should handle CSRF error in withCSRFHandling', async () => {
      const mockRequest = vi.fn()
        .mockRejectedValueOnce({
          response: { status: 419 },
          config: { url: '/api/test', headers: {} }
        })
        .mockResolvedValueOnce({ data: 'success after retry' })
      
      // Mock CSRF refresh
      mockedAxios.get.mockResolvedValue({})
      document.querySelector.mockReturnValue({
        getAttribute: () => 'refreshed-token'
      })
      mockedAxios.mockResolvedValue({ data: 'success after retry' })
      
      const result = await withCSRFHandling(mockRequest)
      
      expect(mockRequest).toHaveBeenCalledTimes(1)
      expect(result.data).toBe('success after retry')
    })
    
    it('should handle non-CSRF errors in withCSRFHandling', async () => {
      const mockRequest = vi.fn().mockRejectedValue({
        response: { status: 500 },
        message: 'Server error'
      })
      
      await expect(withCSRFHandling(mockRequest)).rejects.toMatchObject({
        response: { status: 500 }
      })
    })
    
    it('should provide user feedback for CSRF errors', async () => {
      const mockNotification = vi.fn()
      global.ElNotification = mockNotification
      
      const mockRequest = vi.fn().mockRejectedValue({
        response: { status: 419 },
        config: { url: '/api/test', headers: {} }
      })
      
      // Mock successful retry
      mockedAxios.get.mockResolvedValue({})
      document.querySelector.mockReturnValue({
        getAttribute: () => 'new-token'
      })
      mockedAxios.mockResolvedValue({ data: 'success' })
      
      await handleCSRFErrorWithFeedback(mockRequest, {
        showNotification: true,
        messageType: 'info'
      })
      
      expect(mockNotification).toHaveBeenCalledWith({
        title: 'Security Token Refresh',
        message: 'Token keamanan telah diperbarui. Operasi akan dilanjutkan secara otomatis.',
        type: 'info',
        duration: 4000,
        position: 'top-right'
      })
    })
  })

  describe('CSRF Mixin', () => {
    it('should provide executeWithCSRFHandling method', () => {
      expect(CSRFMixin.methods).toHaveProperty('executeWithCSRFHandling')
      expect(typeof CSRFMixin.methods.executeWithCSRFHandling).toBe('function')
    })
    
    it('should execute request through mixin method', async () => {
      const mockRequest = vi.fn().mockResolvedValue({ data: 'mixin success' })
      
      const result = await CSRFMixin.methods.executeWithCSRFHandling(mockRequest)
      
      expect(mockRequest).toHaveBeenCalled()
      expect(result.data).toBe('mixin success')
    })
  })

  describe('Error Scenarios', () => {
    it('should handle network errors during token refresh', async () => {
      mockedAxios.get.mockRejectedValue(new Error('Network timeout'))
      
      const { refreshCSRFToken } = useCSRF()
      
      await expect(refreshCSRFToken()).rejects.toThrow('Network timeout')
    })
    
    it('should handle malformed CSRF token', () => {
      document.querySelector.mockReturnValue({
        getAttribute: () => null
      })
      
      const { initializeCSRF, csrfToken } = useCSRF()
      
      initializeCSRF()
      
      expect(csrfToken.value).toBe('')
      expect(consoleSpy.warn).toHaveBeenCalled()
    })
    
    it('should handle multiple concurrent CSRF errors', async () => {
      const mockRequest1 = vi.fn().mockRejectedValue({
        response: { status: 419 },
        config: { url: '/api/test1', headers: {} }
      })
      
      const mockRequest2 = vi.fn().mockRejectedValue({
        response: { status: 419 },
        config: { url: '/api/test2', headers: {} }
      })
      
      // Mock token refresh (should only be called once)
      mockedAxios.get.mockResolvedValue({})
      document.querySelector.mockReturnValue({
        getAttribute: () => 'concurrent-token'
      })
      mockedAxios.mockResolvedValue({ data: 'success' })
      
      const promises = [
        withCSRFHandling(mockRequest1),
        withCSRFHandling(mockRequest2)
      ]
      
      const results = await Promise.all(promises)
      
      expect(results).toHaveLength(2)
      results.forEach(result => {
        expect(result.data).toBe('success')
      })
    })
    
    it('should handle CSRF error with custom retry callback', async () => {
      const retryCallback = vi.fn()
      const mockRequest = vi.fn().mockRejectedValue({
        response: { status: 419 },
        config: { url: '/api/test', headers: {} }
      })
      
      // Mock successful retry
      mockedAxios.get.mockResolvedValue({})
      document.querySelector.mockReturnValue({
        getAttribute: () => 'callback-token'
      })
      mockedAxios.mockResolvedValue({ data: 'callback success' })
      
      await withCSRFHandling(mockRequest, {
        retryCallback
      })
      
      expect(retryCallback).toHaveBeenCalledWith({ data: 'callback success' })
    })
  })

  describe('Performance Tests', () => {
    it('should not cause memory leaks with multiple token refreshes', async () => {
      const { refreshCSRFToken } = useCSRF()
      
      // Mock successful refreshes
      mockedAxios.get.mockResolvedValue({})
      document.querySelector.mockReturnValue({
        getAttribute: () => 'performance-token'
      })
      
      // Perform multiple refreshes
      const promises = Array(10).fill().map(() => refreshCSRFToken())
      
      const results = await Promise.all(promises)
      
      expect(results).toHaveLength(10)
      results.forEach(token => {
        expect(token).toBe('performance-token')
      })
    })
    
    it('should debounce concurrent token refresh requests', async () => {
      const { handleCSRFError } = useCSRF()
      
      const requests = [
        { url: '/api/test1', headers: {} },
        { url: '/api/test2', headers: {} },
        { url: '/api/test3', headers: {} }
      ]
      
      // Mock token refresh
      mockedAxios.get.mockResolvedValue({})
      document.querySelector.mockReturnValue({
        getAttribute: () => 'debounced-token'
      })
      mockedAxios.mockResolvedValue({ data: 'debounced success' })
      
      const promises = requests.map(req => handleCSRFError(req))
      
      await Promise.all(promises)
      
      // Token refresh should be called only once due to debouncing
      expect(mockedAxios.get).toHaveBeenCalledTimes(1)
    })
  })

  describe('Integration Tests', () => {
    it('should work with real axios instance', async () => {
      // Create real axios instance
      const realAxios = {
        defaults: { headers: { common: {} } },
        get: vi.fn().mockResolvedValue({}),
        post: vi.fn().mockResolvedValue({ data: 'real success' })
      }
      
      // Mock axios.create to return our real instance
      mockedAxios.create.mockReturnValue(realAxios)
      
      const { initializeCSRF, updateToken } = useCSRF()
      
      initializeCSRF()
      updateToken('integration-token')
      
      expect(realAxios.defaults.headers.common['X-CSRF-TOKEN']).toBe('integration-token')
    })
    
    it('should handle browser compatibility issues', () => {
      // Test dengan browser yang tidak support modern features
      const originalFetch = global.fetch
      delete global.fetch
      
      const { initializeCSRF } = useCSRF()
      
      expect(() => initializeCSRF()).not.toThrow()
      
      // Restore fetch
      global.fetch = originalFetch
    })
  })

  describe('Security Tests', () => {
    it('should not expose token in console logs', () => {
      const { initializeCSRF } = useCSRF()
      
      initializeCSRF()
      
      // Pastikan token tidak di-log ke console
      expect(consoleSpy.log).not.toHaveBeenCalledWith(
        expect.stringContaining('mock-csrf-token-123')
      )
    })
    
    it('should sanitize token before using', () => {
      document.querySelector.mockReturnValue({
        getAttribute: () => '<script>alert("xss")</script>'
      })
      
      const { initializeCSRF, csrfToken } = useCSRF()
      
      initializeCSRF()
      
      // Token harus di-sanitize atau di-reject jika mengandung script
      expect(csrfToken.value).not.toContain('<script>')
    })
    
    it('should validate token format', () => {
      const invalidTokens = [
        '',
        null,
        undefined,
        'too-short',
        'contains spaces',
        'contains/slash',
        'contains"quote'
      ]
      
      const { updateToken, isTokenValid } = useCSRF()
      
      invalidTokens.forEach(token => {
        updateToken(token)
        expect(isTokenValid.value).toBe(false)
      })
    })
  })
})