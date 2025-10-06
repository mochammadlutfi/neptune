// Main composables index - Hybrid (Domain + Common Utilities) structure

// Auth domain exports
export * from './auth'

// Common utilities exports
export * from './common'

// Production domain exports
export * from './production'

// Equipment domain exports
export * from './equipment'

// Legacy exports untuk backward compatibility
export { default as commonComposable } from './common.js'
export { default as authComposable } from './auth.js'

// Re-export individual composables untuk direct import
// Auth
export { useAuth } from './auth/useAuth'
export { useUser } from './auth/useUser'
export { useVessel } from './auth/useVessel'

// Common
export { useAppSettings } from './common/useAppSetting'
export { useBase } from './common/useBase'
export { useCookie } from './common/useCookie'
export { useFormatter } from './common/useFormatter'
export { useInvoiceForm } from './common/useInvoiceForm'
export { usePageMeta } from './common/usePageMeta'
export { useTheme } from './common/useTheme'
export { useSetting } from './common/useSetting'
export { useMenu } from './common/useMenu'

// Production
export { useProducts } from './production/useProducts'
export { useProductsQuery } from './production/useProductsQuery'
export { useSalesQuery } from './production/useSalesQuery'