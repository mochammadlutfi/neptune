import { useCookie } from './useCookie'

// App config default values
const defaultAppConfig = {
  sidebar: {
    collapsible: 'off-canvas',
    side: 'left',
    variant: 'default'
  }
}

export function useAppSettings() {
  // Get default sidebar config
  const { sidebar: _sidebar } = defaultAppConfig
  
  // Menggunakan useCookie composable dengan default values
  const { value: sidebar } = useCookie('app-settings', {
    collapsible: _sidebar.collapsible,
    side: _sidebar.side,
    variant: _sidebar.variant,
  }, {
    expires: 365,
    sameSite: 'lax'
  })
  
  return {
    sidebar,
  }
}