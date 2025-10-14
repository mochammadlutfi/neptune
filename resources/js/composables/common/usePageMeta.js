import { computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n' // Jika menggunakan i18n

export function usePageMeta() {
  const route = useRoute()
  const router = useRouter()
  const { t } = useI18n() // Optional, jika menggunakan i18n

  // Function untuk mengupdate document title
  const updateDocumentTitle = (title) => {
    document.title = `${title} - NEPTUNE`
  }

  // Computed untuk mendapatkan title dari route meta
  const pageTitle = computed(() => {
    const matched = route.matched
    const currentRoute = matched[matched.length - 1]
    
    if (currentRoute?.meta?.title) {
      return t(currentRoute.meta.title, 2)
    }
    
    return ''
  })

  // Computed untuk breadcrumbs
  const breadcrumbs = computed(() => {
    const matched = route.matched.filter(record => record.meta?.title && !record.meta?.hideBreadcrumb)
    const crumbs = []

    matched.forEach((record, index) => {
      const breadcrumbMeta = record.meta.title
      let label = ''

      if (typeof breadcrumbMeta === 'function') {
        label = breadcrumbMeta(route)
      } else {
        label = t(breadcrumbMeta)
      }

      // Tambahkan Home sebagai breadcrumb pertama jika tidak ada
      if (index === 0 && record.path !== '/') {
        crumbs.push({
          label: 'Home',
          to: '/',
          active: false
        })
      }

      crumbs.push({
        label,
        to: index === matched.length - 1 ? null : record.path,
        active: index === matched.length - 1,
        params: route.params
      })
    })

    return crumbs
  })

  // Watch untuk mengupdate title ketika route berubah
  watch(pageTitle, (newTitle) => {
    updateDocumentTitle(newTitle)
  }, { immediate: true })

  const navigateTo = (path) => {
    if (path) {
      router.push(path)
    }
  }

  return {
    pageTitle,
    breadcrumbs,
    navigateTo,
    updateDocumentTitle
  }
}