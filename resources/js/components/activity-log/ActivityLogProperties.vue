<template>
  <div class="properties-container">
    <h5 class="text-sm font-semibold text-gray-800 mb-3">Detail Perubahan</h5>
    
    <!-- Old and New Properties -->
    <div v-if="hasOldAndNew" class="properties-comparison">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Old Properties -->
        <div class="old-properties">
          <h6 class="text-xs font-medium text-red-600 mb-2 flex items-center">
            <i class="fas fa-minus-circle mr-1"></i>
            Nilai Lama
          </h6>
          <div class="properties-list bg-red-50 border border-red-200 rounded p-3">
            <div v-if="oldProperties && Object.keys(oldProperties).length > 0">
              <div 
                v-for="(value, key) in oldProperties" 
                :key="'old-' + key"
                class="property-item mb-2 last:mb-0"
              >
                <div class="flex justify-between items-start">
                  <span class="property-key text-xs font-medium text-gray-700">{{ formatPropertyKey(key) }}:</span>
                  <span class="property-value text-xs text-gray-900 ml-2 text-right flex-1">
                    {{ formatPropertyValue(value) }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="text-xs text-gray-500 italic">
              Tidak ada data lama
            </div>
          </div>
        </div>
        
        <!-- New Properties -->
        <div class="new-properties">
          <h6 class="text-xs font-medium text-green-600 mb-2 flex items-center">
            <i class="fas fa-plus-circle mr-1"></i>
            Nilai Baru
          </h6>
          <div class="properties-list bg-green-50 border border-green-200 rounded p-3">
            <div v-if="newProperties && Object.keys(newProperties).length > 0">
              <div 
                v-for="(value, key) in newProperties" 
                :key="'new-' + key"
                class="property-item mb-2 last:mb-0"
              >
                <div class="flex justify-between items-start">
                  <span class="property-key text-xs font-medium text-gray-700">{{ formatPropertyKey(key) }}:</span>
                  <span class="property-value text-xs text-gray-900 ml-2 text-right flex-1">
                    {{ formatPropertyValue(value) }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="text-xs text-gray-500 italic">
              Tidak ada data baru
            </div>
          </div>
        </div>
      </div>
      
      <!-- Changed Properties Highlight -->
      <div v-if="changedProperties.length > 0" class="changed-summary mt-4">
        <h6 class="text-xs font-medium text-blue-600 mb-2 flex items-center">
          <i class="fas fa-edit mr-1"></i>
          Properti yang Berubah
        </h6>
        <div class="flex flex-wrap gap-1">
          <span 
            v-for="prop in changedProperties" 
            :key="prop"
            class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full"
          >
            {{ formatPropertyKey(prop) }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- Attributes Only (for created events) -->
    <div v-else-if="hasAttributes" class="attributes-only">
      <h6 class="text-xs font-medium text-blue-600 mb-2 flex items-center">
        <i class="fas fa-info-circle mr-1"></i>
        Data yang Disimpan
      </h6>
      <div class="properties-list bg-blue-50 border border-blue-200 rounded p-3">
        <div 
          v-for="(value, key) in attributesProperties" 
          :key="'attr-' + key"
          class="property-item mb-2 last:mb-0"
        >
          <div class="flex justify-between items-start">
            <span class="property-key text-xs font-medium text-gray-700">{{ formatPropertyKey(key) }}:</span>
            <span class="property-value text-xs text-gray-900 ml-2 text-right flex-1">
              {{ formatPropertyValue(value) }}
            </span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Raw Properties (fallback) -->
    <div v-else class="raw-properties">
      <h6 class="text-xs font-medium text-gray-600 mb-2 flex items-center">
        <i class="fas fa-code mr-1"></i>
        Data Mentah
      </h6>
      <div class="bg-gray-50 border border-gray-200 rounded p-3">
        <pre class="text-xs text-gray-800 whitespace-pre-wrap">{{ JSON.stringify(properties, null, 2) }}</pre>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  properties: {
    type: Object,
    required: true
  }
})

// Computed
const oldProperties = computed(() => props.properties.old || null)
const newProperties = computed(() => props.properties.attributes || null)
const attributesProperties = computed(() => props.properties.attributes || null)

const hasOldAndNew = computed(() => {
  return oldProperties.value && newProperties.value
})

const hasAttributes = computed(() => {
  return !hasOldAndNew.value && attributesProperties.value
})

const changedProperties = computed(() => {
  if (!hasOldAndNew.value) return []
  
  const changed = []
  const oldProps = oldProperties.value || {}
  const newProps = newProperties.value || {}
  
  // Check for changed properties
  Object.keys(newProps).forEach(key => {
    if (oldProps[key] !== newProps[key]) {
      changed.push(key)
    }
  })
  
  // Check for removed properties
  Object.keys(oldProps).forEach(key => {
    if (!(key in newProps)) {
      changed.push(key)
    }
  })
  
  return changed
})

// Methods
const formatPropertyKey = (key) => {
  // Convert snake_case to Title Case
  return key
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

const formatPropertyValue = (value) => {
  if (value === null) return 'null'
  if (value === undefined) return 'undefined'
  if (value === '') return '(kosong)'
  if (typeof value === 'boolean') return value ? 'Ya' : 'Tidak'
  if (typeof value === 'object') {
    try {
      return JSON.stringify(value)
    } catch {
      return '[Object]'
    }
  }
  
  // Format dates
  if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}/.test(value)) {
    try {
      const date = new Date(value)
      if (!isNaN(date.getTime())) {
        return date.toLocaleString('id-ID')
      }
    } catch {
      // If date parsing fails, return original value
    }
  }
  
  // Truncate long strings
  const stringValue = String(value)
  if (stringValue.length > 100) {
    return stringValue.substring(0, 100) + '...'
  }
  
  return stringValue
}
</script>

<style scoped>
.properties-container {
  @apply text-sm;
}

.property-item {
  @apply border-b border-gray-200 pb-1 last:border-b-0 last:pb-0;
}

.property-key {
  @apply min-w-0 break-words;
}

.property-value {
  @apply min-w-0 break-words font-mono;
}

.changed-summary {
  @apply pt-3 border-t border-gray-200;
}
</style>