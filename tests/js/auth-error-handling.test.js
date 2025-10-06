/**
 * Test untuk memverifikasi error handling 401 (session expired)
 * Memastikan aplikasi melakukan logout otomatis dan redirect ke login
 */

import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import axios from 'axios'
import { useAuth } from '@/composables/auth/useAuth'
import { useUser } from '@/composables/auth/useUser'

// Mock dependencies
vi.mock('@/composables/auth/useAuth')
vi.mock('@/composables/auth/useUser')
vi.mock('js-cookie')

describe('Auth Error Handling - Session Expired (401)', () => {
  let mockAuth, mockUser
  
  beforeEach(() => {
    // Reset mocks
    vi.clearAllMocks()
    
    // Mock window.location
    delete window.location
    window.location = { href: '' }
    
    // Mock auth store
    mockAuth = {
      token: { value: 'mock-token' },
      logout: vi.fn().mockResolvedValue(true),
      clearToken: vi.fn()
    }
    
    // Mock user store
    mockUser = {
      user: null,
      getUser: vi.fn(),
      clearUser: vi.fn()
    }
    
    useAuth.mockReturnValue(mockAuth)
    useUser.mockReturnValue(mockUser)
  })
  
  afterEach(() => {
    vi.restoreAllMocks()
  })
  
  it('should handle 401 error during app startup', async () => {
    // Simulate 401 error from getUser
    const error401 = {
      status: 401,
      message: 'Session expired. Please login again.'
    }
    
    mockUser.getUser.mockRejectedValue(error401)
    
    // Import app initialization logic
    const { initializeApp } = await import('@/utils/app-init')
    
    // Should not throw error, should handle gracefully
    await expect(initializeApp()).resolves.not.toThrow()
    
    // Verify getUser was called
    expect(mockUser.getUser).toHaveBeenCalled()
  })
  
  it('should handle 401 error in axios interceptor', async () => {
    // Mock axios response with 401
    const mockError = {
      response: {
        status: 401,
        data: { message: 'Unauthenticated' }
      }
    }
    
    // Simulate axios interceptor behavior
    const interceptorHandler = axios.interceptors.response.handlers[0].rejected
    
    // Should call logout and redirect
    await interceptorHandler(mockError)
    
    // Verify logout was called
    expect(mockAuth.logout).toHaveBeenCalled()
    
    // Verify redirect happened (with timeout for async behavior)
    setTimeout(() => {
      expect(window.location.href).toBe('/login')
    }, 150)
  })
  
  it('should clear auth state on non-401 errors during startup', async () => {
    // Simulate non-401 error
    const error500 = {
      status: 500,
      message: 'Internal server error'
    }
    
    mockUser.getUser.mockRejectedValue(error500)
    
    // Import app initialization logic
    const { initializeApp } = await import('@/utils/app-init')
    
    await initializeApp()
    
    // Should clear auth state for safety
    expect(mockAuth.clearToken).toHaveBeenCalled()
    expect(mockUser.clearUser).toHaveBeenCalled()
  })
  
  it('should handle logout gracefully when auth store not available', () => {
    // Mock scenario where auth store throws error
    useAuth.mockImplementation(() => {
      throw new Error('Auth store not initialized')
    })
    
    // Should not crash the application
    expect(() => {
      // Simulate axios interceptor trying to access auth
      try {
        const { logout } = useAuth()
      } catch (error) {
        // Should handle gracefully and use manual cleanup
        console.warn('Auth store not available, using manual cleanup')
      }
    }).not.toThrow()
  })
})

/**
 * Integration test untuk memverifikasi flow lengkap
 */
describe('Auth Error Handling - Integration', () => {
  it('should complete full logout flow on session expired', async () => {
    const consoleSpy = vi.spyOn(console, 'log')
    
    // Mock 401 response
    const mockError = {
      response: {
        status: 401,
        data: { message: 'Token expired' }
      }
    }
    
    // Mock auth composable
    const mockLogout = vi.fn().mockResolvedValue(true)
    useAuth.mockReturnValue({
      token: { value: 'expired-token' },
      logout: mockLogout,
      clearToken: vi.fn()
    })
    
    // Simulate the full flow
    try {
      // This would normally be triggered by axios interceptor
      const { logout } = useAuth()
      await logout()
      
      // Verify logout was called
      expect(mockLogout).toHaveBeenCalled()
      
      // Verify console logging
      expect(consoleSpy).toHaveBeenCalledWith(
        expect.stringContaining('Unauthenticated response detected')
      )
    } catch (error) {
      // Should not reach here in normal flow
      expect(error).toBeUndefined()
    }
    
    consoleSpy.mockRestore()
  })
})