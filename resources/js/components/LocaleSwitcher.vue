<template>
  <div class="locale-switcher">
    <!-- Dropdown Style Switcher -->
    <div v-if="displayStyle === 'dropdown'" class="relative">
      <button
        @click="toggleDropdown"
        :disabled="isLoading"
        class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span class="text-lg">{{ getLocaleFlag(currentLocale) }}</span>
        <span class="hidden sm:block">{{ getLocaleDisplayName(currentLocale) }}</span>
        <ChevronDownIcon class="w-4 h-4" />
      </button>

      <!-- Dropdown Menu -->
      <Transition
        enter-active-class="transition ease-out duration-100"
        enter-from-class="transform opacity-0 scale-95"
        enter-to-class="transform opacity-100 scale-100"
        leave-active-class="transition ease-in duration-75"
        leave-from-class="transform opacity-100 scale-100"
        leave-to-class="transform opacity-0 scale-95"
      >
        <div
          v-if="showDropdown"
          class="absolute right-0 z-50 mt-2 w-48 origin-top-right bg-white border border-gray-200 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        >
          <div class="py-1">
            <button
              v-for="locale in availableLocales"
              :key="locale.code"
              @click="handleLocaleChange(locale.code)"
              :disabled="isLoading || locale.code === currentLocale"
              class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
              :class="{
                'bg-blue-50 text-blue-700': locale.code === currentLocale
              }"
            >
              <span class="mr-3 text-lg">{{ locale.flag }}</span>
              <div class="flex-1 text-left">
                <div class="font-medium">{{ locale.name }}</div>
                <div class="text-xs text-gray-500">{{ locale.native_name }}</div>
              </div>
              <CheckIcon
                v-if="locale.code === currentLocale"
                class="w-4 h-4 text-blue-600"
              />
            </button>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Toggle Style Switcher -->
    <div v-else-if="displayStyle === 'toggle'" class="flex items-center space-x-2">
      <button
        v-for="locale in availableLocales"
        :key="locale.code"
        @click="handleLocaleChange(locale.code)"
        :disabled="isLoading"
        class="flex items-center space-x-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
        :class="{
          'bg-blue-600 text-white': locale.code === currentLocale,
          'bg-gray-100 text-gray-700 hover:bg-gray-200': locale.code !== currentLocale
        }"
      >
        <span class="text-lg">{{ locale.flag }}</span>
        <span v-if="showLabels" class="hidden sm:block">{{ locale.name }}</span>
      </button>
    </div>

    <!-- Compact Style Switcher -->
    <div v-else-if="displayStyle === 'compact'" class="flex items-center">
      <button
        @click="toggleLocale"
        :disabled="isLoading"
        class="flex items-center space-x-1 px-2 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span class="text-lg">{{ getLocaleFlag(currentLocale) }}</span>
        <span v-if="showLabels" class="hidden sm:block text-xs">{{ currentLocale.toUpperCase() }}</span>
      </button>
    </div>

    <!-- Loading Indicator -->
    <div v-if="isLoading" class="flex items-center space-x-2 text-sm text-gray-500">
      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
      <span>{{ t('common.loading') }}</span>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="text-sm text-red-600">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useLocalization } from '@/composables/useLocalization'
import { ChevronDownIcon, CheckIcon } from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  displayStyle: {
    type: String,
    default: 'dropdown',
    validator: (value) => ['dropdown', 'toggle', 'compact'].includes(value)
  },
  showLabels: {
    type: Boolean,
    default: true
  },
  autoClose: {
    type: Boolean,
    default: true
  }
})

// Emits
const emit = defineEmits(['locale-changed', 'error'])

// Composables
const {
  currentLocale,
  availableLocales,
  isLoading,
  error,
  setLocale,
  toggleLocale,
  getLocaleDisplayName,
  getLocaleFlag,
  t
} = useLocalization()

// Local state
const showDropdown = ref(false)

// Methods
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const closeDropdown = () => {
  showDropdown.value = false
}

const handleLocaleChange = async (newLocale) => {
  if (newLocale === currentLocale.value || isLoading.value) {
    return
  }

  try {
    await setLocale(newLocale)
    emit('locale-changed', newLocale)
    
    if (props.autoClose) {
      closeDropdown()
    }
    
    // Show success notification
    if (window.$toast) {
      window.$toast.success(t('localization.locale_changed_successfully'))
    }
  } catch (err) {
    emit('error', err)
    
    // Show error notification
    if (window.$toast) {
      window.$toast.error(err.message || t('localization.failed_to_change_locale'))
    }
  }
}

// Click outside handler
const handleClickOutside = (event) => {
  if (!event.target.closest('.locale-switcher')) {
    closeDropdown()
  }
}

// Lifecycle
onMounted(() => {
  if (props.displayStyle === 'dropdown') {
    document.addEventListener('click', handleClickOutside)
  }
})

onUnmounted(() => {
  if (props.displayStyle === 'dropdown') {
    document.removeEventListener('click', handleClickOutside)
  }
})

// Keyboard navigation for dropdown
const handleKeydown = (event) => {
  if (props.displayStyle !== 'dropdown' || !showDropdown.value) {
    return
  }

  switch (event.key) {
    case 'Escape':
      closeDropdown()
      break
    case 'ArrowDown':
    case 'ArrowUp':
      event.preventDefault()
      // Handle arrow navigation if needed
      break
  }
}

// Add keyboard event listener
onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
.locale-switcher {
  @apply relative inline-block;
}

/* Custom animations for smooth transitions */
.locale-switcher button {
  transition: all 0.2s ease-in-out;
}

.locale-switcher button:hover {
  transform: translateY(-1px);
}

.locale-switcher button:active {
  transform: translateY(0);
}

/* Loading spinner animation */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Responsive design */
@media (max-width: 640px) {
  .locale-switcher .hidden.sm\:block {
    display: none !important;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .locale-switcher button {
    @apply bg-gray-800 text-gray-200 border-gray-600;
  }
  
  .locale-switcher button:hover {
    @apply bg-gray-700;
  }
  
  .locale-switcher .bg-white {
    @apply bg-gray-800;
  }
  
  .locale-switcher .border-gray-200 {
    @apply border-gray-600;
  }
  
  .locale-switcher .text-gray-700 {
    @apply text-gray-200;
  }
  
  .locale-switcher .hover\:bg-gray-100:hover {
    @apply bg-gray-700;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .locale-switcher button {
    @apply border-2 border-black;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .locale-switcher button,
  .locale-switcher .transition {
    transition: none !important;
  }
  
  .animate-spin {
    animation: none !important;
  }
}
</style>