// composables/useCookie.js
import { ref, watch } from 'vue'
import Cookies from 'js-cookie'

/**
 * Vue 3 Composable untuk pengelolaan cookie dengan js-cookie
 * @param {string} key - Nama cookie
 * @param {any} defaultValue - Nilai default jika cookie tidak ada
 * @param {object} options - Opsi cookie (expires, domain, path, secure, sameSite)
 * @returns {object} - Object dengan value reactive dan fungsi helper
 */
export function useCookie(key, defaultValue = null, options = {}) {
  // Membaca nilai cookie saat ini
  const getCookieValue = () => {
    const value = Cookies.get(key)
    if (value === undefined) return defaultValue
    
    try {
      return JSON.parse(value)
    } catch {
      return value
    }
  }

  // Reactive reference untuk nilai cookie
  const cookieValue = ref(getCookieValue())

  // Fungsi untuk set cookie
  const setCookie = (value, cookieOptions = {}) => {
    const finalOptions = { ...options, ...cookieOptions }
    const serializedValue = typeof value === 'object' ? JSON.stringify(value) : value
    
    Cookies.set(key, serializedValue, finalOptions)
    cookieValue.value = value
  }

  // Fungsi untuk hapus cookie
  const removeCookie = (cookieOptions = {}) => {
    const finalOptions = { ...options, ...cookieOptions }
    Cookies.remove(key, finalOptions)
    cookieValue.value = defaultValue
  }

  // Fungsi untuk refresh nilai cookie
  const refreshCookie = () => {
    cookieValue.value = getCookieValue()
  }

  // Watch perubahan nilai dan update cookie otomatis
  watch(
    cookieValue,
    (newValue) => {
      if (newValue === null || newValue === undefined) {
        removeCookie()
      } else {
        setCookie(newValue)
      }
    },
    { deep: true }
  )

  return {
    value: cookieValue,
    setValue: setCookie,
    remove: removeCookie,
    refresh: refreshCookie
  }
}

/**
 * Composable untuk multiple cookies management
 * @returns {object} - Object dengan fungsi helper untuk mengelola semua cookies
 */
export function useCookies() {
  // Get semua cookies
  const getAll = () => {
    return Cookies.get()
  }

  // Set cookie dengan key-value
  const set = (key, value, options = {}) => {
    const serializedValue = typeof value === 'object' ? JSON.stringify(value) : value
    Cookies.set(key, serializedValue, options)
  }

  // Get cookie berdasarkan key
  const get = (key) => {
    const value = Cookies.get(key)
    if (value === undefined) return null
    
    try {
      return JSON.parse(value)
    } catch {
      return value
    }
  }

  // Remove cookie berdasarkan key
  const remove = (key, options = {}) => {
    Cookies.remove(key, options)
  }

  // Check apakah cookie ada
  const has = (key) => {
    return Cookies.get(key) !== undefined
  }

  // Clear semua cookies (hati-hati menggunakan ini)
  const clear = () => {
    const allCookies = Cookies.get()
    Object.keys(allCookies).forEach(key => {
      Cookies.remove(key)
    })
  }

  return {
    getAll,
    set,
    get,
    remove,
    has,
    clear
  }
}

/**
 * Composable khusus untuk user authentication cookie
 * @returns {object} - Object dengan fungsi auth-specific
 */
export function useAuthCookie() {
  const { value: token, setValue: setToken, remove: removeToken } = useCookie('auth_token', null, {
    expires: 7, // 7 hari
    secure: true,
    sameSite: 'strict'
  })

  const { value: user, setValue: setUser, remove: removeUser } = useCookie('user_data', null, {
    expires: 7,
    secure: true,
    sameSite: 'strict'
  })

  const login = (authToken, userData) => {
    setToken(authToken)
    setUser(userData)
  }

  const logout = () => {
    removeToken()
    removeUser()
  }

  const isAuthenticated = () => {
    return token.value !== null && token.value !== undefined
  }

  return {
    token,
    user,
    login,
    logout,
    isAuthenticated
  }
}

/**
 * Composable untuk user preferences
 * @returns {object} - Object dengan fungsi preferences management
 */
export function usePreferencesCookie() {
  const { value: theme, setValue: setTheme } = useCookie('user_theme', 'light', {
    expires: 365 // 1 tahun
  })

  const { value: language, setValue: setLanguage } = useCookie('user_language', 'id', {
    expires: 365
  })

  const { value: preferences, setValue: setPreferences } = useCookie('user_preferences', {}, {
    expires: 365
  })

  const updatePreference = (key, value) => {
    const current = preferences.value || {}
    setPreferences({
      ...current,
      [key]: value
    })
  }

  const getPreference = (key, defaultValue = null) => {
    const current = preferences.value || {}
    return current[key] || defaultValue
  }

  return {
    theme,
    language,
    preferences,
    setTheme,
    setLanguage,
    setPreferences,
    updatePreference,
    getPreference
  }
}