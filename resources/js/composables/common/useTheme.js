import { ref, computed, watch, onMounted } from 'vue'
import ThemeService from '@/Services/ThemeService'

// Default theme configuration
const defaultTheme = {
  mode: 'light', // light | dark | auto
  primaryColor: '#3b82f6', // blue-500
  radius: '0.5rem',
  fontFamily: 'Poppins',
  fontSize: '14px',
  spacing: 'normal', // compact | normal | comfortable
  shadow: 'medium', // none | small | medium | large
  fontWeight: 'normal' // light | normal | medium | semibold
}

// Theme JSON support
const isThemeJsonLoaded = ref(false)
const currentThemeJson = ref(null)

// Reactive theme state
const theme = ref({ ...defaultTheme })

// Theme presets
const themePresets = {
  blue: {
    name: 'Blue',
    primaryColor: '#3b82f6',
    colors: {
      primary: '221.2 83.2% 53.3%',
      secondary: '210 40% 96%',
      success: '142 76% 36%',
      warning: '48 96% 53%',
      danger: '0 84.2% 60.2%',
      info: '199 89% 48%'
    }
  },
  green: {
    name: 'Green',
    primaryColor: '#22c55e',
    colors: {
      primary: '142 76% 36%',
      secondary: '210 40% 96%',
      success: '142 76% 36%',
      warning: '48 96% 53%',
      danger: '0 84.2% 60.2%',
      info: '199 89% 48%'
    }
  },
  purple: {
    name: 'Purple',
    primaryColor: '#8b5cf6',
    colors: {
      primary: '262 83% 58%',
      secondary: '210 40% 96%',
      success: '142 76% 36%',
      warning: '48 96% 53%',
      danger: '0 84.2% 60.2%',
      info: '199 89% 48%'
    }
  },
  orange: {
    name: 'Orange',
    primaryColor: '#f97316',
    colors: {
      primary: '24 95% 53%',
      secondary: '210 40% 96%',
      success: '142 76% 36%',
      warning: '48 96% 53%',
      danger: '0 84.2% 60.2%',
      info: '199 89% 48%'
    }
  }
}

// Font families
const fontFamilies = {
  poppins: { name: 'Poppins', value: 'Poppins, sans-serif' },
  inter: { name: 'Inter', value: 'Inter, sans-serif' },
  roboto: { name: 'Roboto', value: 'Roboto, sans-serif' },
  opensans: { name: 'Open Sans', value: 'Open Sans, sans-serif' }
}

// Radius options
const radiusOptions = {
  none: { name: 'None', value: '0' },
  small: { name: 'Small', value: '0.25rem' },
  medium: { name: 'Medium', value: '0.5rem' },
  large: { name: 'Large', value: '0.75rem' },
  xlarge: { name: 'Extra Large', value: '1rem' }
}

// Spacing options
const spacingOptions = {
  compact: { name: 'Compact', value: 0.8 },
  normal: { name: 'Normal', value: 1 },
  comfortable: { name: 'Comfortable', value: 1.2 }
}

// Shadow options
const shadowOptions = {
  none: { name: 'None', value: 'none' },
  small: { name: 'Small', value: '0 1px 2px 0 rgb(0 0 0 / 0.05)' },
  medium: { name: 'Medium', value: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)' },
  large: { name: 'Large', value: '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)' }
}

// Font weight options
const fontWeightOptions = {
  light: { name: 'Light', value: '300' },
  normal: { name: 'Normal', value: '400' },
  medium: { name: 'Medium', value: '500' },
  semibold: { name: 'Semi Bold', value: '600' }
}

export function useTheme() {
  // Load theme from localStorage or theme.json
  const loadTheme = async () => {
    // Try to load theme.json first
    try {
      const themeJsonData = await ThemeService.loadTheme('/uploads/theme/theme.json')
      if (themeJsonData) {
        currentThemeJson.value = themeJsonData
        isThemeJsonLoaded.value = true
        applyThemeJson(themeJsonData)
        return
      }
    } catch (error) {
      console.warn('Failed to load theme.json, falling back to localStorage')
    }

    // Fallback to localStorage
    const savedTheme = localStorage.getItem('lovaerp-theme')
    if (savedTheme) {
      try {
        const parsed = JSON.parse(savedTheme)
        theme.value = { ...defaultTheme, ...parsed }
      } catch (e) {
        console.warn('Failed to parse saved theme, using default')
      }
    }
    applyTheme()
  }

  // Apply theme from JSON format
  const applyThemeJson = (themeData, mode = 'light') => {
    if (!themeData || !themeData.cssVars) return

    const root = document.documentElement
    const vars = themeData.cssVars[mode] || themeData.cssVars.light
    const themeVars = themeData.cssVars.theme || {}

    // Apply theme variables
    Object.entries(themeVars).forEach(([key, value]) => {
      root.style.setProperty(`--${key}`, value)
    })

    // Apply mode-specific variables
    Object.entries(vars).forEach(([key, value]) => {
      root.style.setProperty(`--${key}`, value)
    })

    // Update dark mode class
    root.classList.toggle('dark', mode === 'dark')

    // Sync with legacy theme object
    syncThemeFromJson(themeData, mode)
  }

  // Sync legacy theme object from JSON
  const syncThemeFromJson = (themeData, mode) => {
    const vars = themeData.cssVars[mode] || themeData.cssVars.light
    const themeVars = themeData.cssVars.theme || {}

    theme.value = {
      ...theme.value,
      mode: mode,
      primaryColor: vars.primary || theme.value.primaryColor,
      radius: vars.radius || themeVars.radius || theme.value.radius,
      fontFamily: vars['font-sans'] || themeVars['font-sans'] || theme.value.fontFamily,
      spacing: vars.spacing || theme.value.spacing
    }
  }

  // Save theme to localStorage
  const saveTheme = () => {
    localStorage.setItem('lovaerp-theme', JSON.stringify(theme.value))
  }

  // Apply theme to CSS variables
  const applyTheme = () => {
    const root = document.documentElement
    
    // Apply theme mode
    if (theme.value.mode === 'dark') {
      root.classList.add('dark')
    } else {
      root.classList.remove('dark')
    }

    // Apply primary color and related colors
    const preset = Object.values(themePresets).find(p => p.primaryColor === theme.value.primaryColor)
    if (preset) {
      Object.entries(preset.colors).forEach(([key, value]) => {
        root.style.setProperty(`--${key}`, value)
      })
    }

    // Apply radius
    root.style.setProperty('--radius', theme.value.radius)

    // Apply font family
    const fontFamily = fontFamilies[theme.value.fontFamily]?.value || fontFamilies.poppins.value
    root.style.setProperty('--font-family', fontFamily)
    document.body.style.fontFamily = fontFamily

    // Apply font size
    root.style.setProperty('--font-size-base', theme.value.fontSize)
    document.body.style.fontSize = theme.value.fontSize

    // Apply spacing scale
    const spacingScale = spacingOptions[theme.value.spacing]?.value || 1
    root.style.setProperty('--spacing-scale', spacingScale)

    // Apply shadow
    const shadowValue = shadowOptions[theme.value.shadow]?.value || shadowOptions.medium.value
    root.style.setProperty('--shadow-default', shadowValue)

    // Apply font weight
    const fontWeight = fontWeightOptions[theme.value.fontWeight]?.value || '400'
    root.style.setProperty('--font-weight-base', fontWeight)

    // Update Element Plus CSS variables
    updateElementPlusTheme()
  }

  // Update Element Plus specific variables
  const updateElementPlusTheme = () => {
    const root = document.documentElement
    
    // Update Element Plus primary color
    root.style.setProperty('--el-color-primary', theme.value.primaryColor)
    
    // Update Element Plus radius
    root.style.setProperty('--el-border-radius-base', theme.value.radius)
    
    // Update Element Plus font
    const fontFamily = fontFamilies[theme.value.fontFamily]?.value || fontFamilies.poppins.value
    root.style.setProperty('--el-font-family', fontFamily)
    root.style.setProperty('--el-font-size-base', theme.value.fontSize)
  }

  // Set theme mode
  const setThemeMode = (mode) => {
    theme.value.mode = mode
    
    // If using theme.json, apply the new mode
    if (isThemeJsonLoaded.value && currentThemeJson.value) {
      applyThemeJson(currentThemeJson.value, mode)
    } else {
      applyTheme()
    }
    
    saveTheme()
  }

  // Export theme as JSON format
  const exportThemeJson = () => {
    const themeJson = {
      name: "Custom Theme",
      cssVars: {
        theme: {
          "primary": theme.value.primaryColor,
          "radius": theme.value.radius,
          "font-sans": theme.value.fontFamily
        },
        light: {
          "background": "0 0% 100%",
          "foreground": "222.2 84% 4.9%",
          "card": "0 0% 100%",
          "card-foreground": "222.2 84% 4.9%",
          "popover": "0 0% 100%",
          "popover-foreground": "222.2 84% 4.9%",
          "primary": theme.value.primaryColor,
          "primary-foreground": "210 40% 98%",
          "secondary": "210 40% 96%",
          "secondary-foreground": "222.2 84% 4.9%",
          "muted": "210 40% 96%",
          "muted-foreground": "215.4 16.3% 46.9%",
          "accent": "210 40% 96%",
          "accent-foreground": "222.2 84% 4.9%",
          "destructive": "0 84.2% 60.2%",
          "destructive-foreground": "210 40% 98%",
          "border": "214.3 31.8% 91.4%",
          "input": "214.3 31.8% 91.4%",
          "ring": theme.value.primaryColor,
          "radius": theme.value.radius
        },
        dark: {
          "background": "222.2 84% 4.9%",
          "foreground": "210 40% 98%",
          "card": "222.2 84% 4.9%",
          "card-foreground": "210 40% 98%",
          "popover": "222.2 84% 4.9%",
          "popover-foreground": "210 40% 98%",
          "primary": theme.value.primaryColor,
          "primary-foreground": "222.2 84% 4.9%",
          "secondary": "217.2 32.6% 17.5%",
          "secondary-foreground": "210 40% 98%",
          "muted": "217.2 32.6% 17.5%",
          "muted-foreground": "215 20.2% 65.1%",
          "accent": "217.2 32.6% 17.5%",
          "accent-foreground": "210 40% 98%",
          "destructive": "0 62.8% 30.6%",
          "destructive-foreground": "210 40% 98%",
          "border": "217.2 32.6% 17.5%",
          "input": "217.2 32.6% 17.5%",
          "ring": theme.value.primaryColor,
          "radius": theme.value.radius
        }
      }
    }
    
    return themeJson
  }

  // Import theme from JSON
  const importThemeJson = async (themeJsonData) => {
    try {
      if (ThemeService.validateTheme(themeJsonData)) {
        currentThemeJson.value = themeJsonData
        isThemeJsonLoaded.value = true
        applyThemeJson(themeJsonData, theme.value.mode)
        return true
      }
      return false
    } catch (error) {
      console.error('Failed to import theme JSON:', error)
      return false
    }
  }

  // Load theme from file
  const loadThemeFromFile = async (filePath) => {
    try {
      const themeData = await ThemeService.loadTheme(filePath)
      if (themeData) {
        return await importThemeJson(themeData)
      }
      return false
    } catch (error) {
      console.error('Failed to load theme from file:', error)
      return false
    }
  }

  // Set primary color
  const setPrimaryColor = (color) => {
    theme.value.primaryColor = color
    applyTheme()
    saveTheme()
  }

  // Set radius
  const setRadius = (radius) => {
    theme.value.radius = radius
    applyTheme()
    saveTheme()
  }

  // Set font family
  const setFontFamily = (fontFamily) => {
    theme.value.fontFamily = fontFamily
    applyTheme()
    saveTheme()
  }

  // Set font size
  const setFontSize = (fontSize) => {
    theme.value.fontSize = fontSize
    applyTheme()
    saveTheme()
  }

  // Set spacing
  const setSpacing = (spacing) => {
    theme.value.spacing = spacing
    applyTheme()
    saveTheme()
  }

  // Set shadow
  const setShadow = (shadow) => {
    theme.value.shadow = shadow
    applyTheme()
    saveTheme()
  }

  // Set font weight
  const setFontWeight = (fontWeight) => {
    theme.value.fontWeight = fontWeight
    applyTheme()
    saveTheme()
  }

  // Reset to default theme
  const resetTheme = () => {
    theme.value = { ...defaultTheme }
    applyTheme()
    saveTheme()
  }

  // Computed properties
  const isDark = computed(() => theme.value.mode === 'dark')
  const currentPreset = computed(() => 
    Object.values(themePresets).find(p => p.primaryColor === theme.value.primaryColor)
  )

  // Watch for system theme changes
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  const handleSystemThemeChange = (e) => {
    if (theme.value.mode === 'system') {
      setThemeMode(e.matches ? 'dark' : 'light')
    }
  }
  mediaQuery.addEventListener('change', handleSystemThemeChange)

  // Initialize theme on mount
  onMounted(() => {
    loadTheme()
  })

  return {
    theme: computed(() => theme.value),
    themePresets,
    fontFamilies,
    radiusOptions,
    spacingOptions,
    shadowOptions,
    fontWeightOptions,
    isDark,
    currentPreset,
    
    // Theme JSON support
    isThemeJsonLoaded,
    currentThemeJson,
    
    // Core methods
    loadTheme,
    saveTheme,
    applyTheme,
    
    // Theme property setters
    setThemeMode,
    setPrimaryColor,
    setRadius,
    setFontFamily,
    setFontSize,
    setSpacing,
    setShadow,
    setFontWeight,
    resetTheme,
    
    // Theme JSON methods
    applyThemeJson,
    exportThemeJson,
    importThemeJson,
    loadThemeFromFile
  }
}