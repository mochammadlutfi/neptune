import { useTheme } from '@/composables/useTheme'

export default {
  install(app) {
    // Initialize theme system
    const { loadTheme, applyTheme } = useTheme()
    
    // Load theme on app initialization
    loadTheme()
    
    // Apply theme immediately
    applyTheme()
    
    // Make theme available globally
    app.config.globalProperties.$theme = useTheme()
    
    // Provide theme for composition API
    app.provide('theme', useTheme())
    
    // Watch for system theme changes
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const handleSystemThemeChange = (e) => {
      const { theme, setMode } = useTheme()
      if (theme.value.mode === 'system') {
        setMode(e.matches ? 'dark' : 'light')
      }
    }
    
    mediaQuery.addEventListener('change', handleSystemThemeChange)
    
    // Cleanup on app unmount
    app.config.globalProperties.$cleanupTheme = () => {
      mediaQuery.removeEventListener('change', handleSystemThemeChange)
    }
  }
}