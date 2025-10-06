<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
          {{ title }}
        </p>
        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">
          {{ value }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" v-if="unit">
          {{ unit }}
        </p>
        
        <!-- Additional Info Section -->
        <div v-if="additionalInfo" class="mt-3">
          <div v-if="additionalInfo.type === 'trend'" class="flex items-center text-sm">
            <div class="flex items-center" :class="getTrendColor(additionalInfo)">
              <Icon 
                :icon="getTrendIcon(additionalInfo)" 
                class="w-4 h-4 mr-1" 
              />
              <span>{{ additionalInfo.value }}</span>
            </div>
            <span class="ml-2 text-gray-500 dark:text-gray-400">{{ additionalInfo.label }}</span>
          </div>
          
          <div v-else-if="additionalInfo.type === 'status'" class="flex items-center space-x-2">
            <div v-if="additionalInfo.status === 'success'" class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <div v-else-if="additionalInfo.status === 'warning'" class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
            <div v-else-if="additionalInfo.status === 'error'" class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
            <div v-else class="w-2 h-2 bg-gray-500 rounded-full animate-pulse"></div>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ additionalInfo.value }}</p>
          </div>
          
          <div v-else-if="additionalInfo.type === 'breakdown'">
            <div v-if="additionalInfo.subtitle" class="flex items-center space-x-2 mb-2">
              <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ additionalInfo.subtitle }}</p>
            </div>
            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
              <span v-for="(item, index) in additionalInfo.items" :key="index">
                {{ item.label }}: {{ item.value }}
              </span>
            </div>
          </div>
          
          <div v-else-if="additionalInfo.type === 'quality'" class="flex items-center text-sm">
            <div class="flex items-center text-green-600 dark:text-green-400">
              <Icon icon="heroicons:check-circle" class="w-4 h-4 mr-1" />
              <span>{{ additionalInfo.value }}</span>
            </div>
            <span class="ml-2 text-gray-500 dark:text-gray-400" v-if="additionalInfo.label">{{ additionalInfo.label }}</span>
          </div>
        </div>
      </div>
      
      <!-- Icon Section -->
      <div v-if="icon" class="p-3 rounded-lg" :class="getIconBgColor()">
        <Icon :icon="icon" class="w-8 h-8 text-white" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  unit: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  iconColor: {
    type: String,
    default: 'blue',
    validator: (value) => ['blue', 'green', 'orange', 'purple', 'red', 'cyan'].includes(value)
  },
  additionalInfo: {
    type: Object,
    default: null
    // Structure examples:
    // { type: 'trend', trend: 5.2, label: 'vs yesterday' }
    // { type: 'status', label: 'Real-time monitoring' }
    // { type: 'breakdown', items: [{ label: 'Online', value: 3 }, { label: 'Offline', value: 1 }] }
    // { type: 'quality', status: 'Within spec', label: 'Quality OK' }
  }
})

const getIconBgColor = () => {
  const colors = {
    blue: 'bg-blue-500',
    green: 'bg-green-500',
    orange: 'bg-orange-500',
    purple: 'bg-purple-500',
    red: 'bg-red-500',
    cyan: 'bg-cyan-500'
  }
  return colors[props.iconColor] || colors.blue
}

const getTrendColor = (additionalInfo) => {
  // Check if isPositive is explicitly set
  if (additionalInfo.hasOwnProperty('isPositive')) {
    return additionalInfo.isPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
  }
  
  // Fallback to checking if value starts with + or -
  const value = additionalInfo.value?.toString() || ''
  if (value.startsWith('+')) {
    return 'text-green-600 dark:text-green-400'
  } else if (value.startsWith('-')) {
    return 'text-red-600 dark:text-red-400'
  }
  
  return 'text-gray-600 dark:text-gray-400'
}

const getTrendIcon = (additionalInfo) => {
  // Check if isPositive is explicitly set
  if (additionalInfo.hasOwnProperty('isPositive')) {
    return additionalInfo.isPositive ? 'heroicons:arrow-trending-up' : 'heroicons:arrow-trending-down'
  }
  
  // Fallback to checking if value starts with + or -
  const value = additionalInfo.value?.toString() || ''
  if (value.startsWith('+')) {
    return 'heroicons:arrow-trending-up'
  } else if (value.startsWith('-')) {
    return 'heroicons:arrow-trending-down'
  }
  
  return 'heroicons:arrow-trending-up'
}
</script>