<template>
    <el-tag 
      :type="tagType" 
      :size="size"
      :class="tagClass"
      class="font-medium"
    >
      <Icon 
        v-if="showIcon" 
        :icon="statusIcon" 
        class="mr-1" 
        :class="iconClass"
      />
      {{ statusLabel }}
    </el-tag>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  import { Icon } from '@iconify/vue'
  import { useI18n } from 'vue-i18n'
  
  const { t } = useI18n()
  
  const props = defineProps({
    status: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'default', // default, shipping, invoice
      validator: (value) => ['default', 'shipping', 'invoice'].includes(value)
    },
    size: {
      type: String,
      default: 'default',
      validator: (value) => ['small', 'default', 'large'].includes(value)
    },
    showIcon: {
      type: Boolean,
      default: true
    }
  })
  
  // Status configurations
  const statusConfig = {
    // Default order status
    draft: {
      type: 'info',
      icon: 'mingcute:file-line',
      label: 'common.draft',
      class: 'bg-gray-100 text-gray-800 border-gray-200'
    },
    confirmed: {
      type: 'warning',
      icon: 'mingcute:check-circle-line',
      label: 'common.confirmed',
      class: 'bg-yellow-100 text-yellow-800 border-yellow-200'
    },
    done: {
      type: 'success',
      icon: 'mingcute:check-circle-fill',
      label: 'common.done',
      class: 'bg-green-100 text-green-800 border-green-200'
    },
    cancel: {
      type: 'danger',
      icon: 'mingcute:close-circle-line',
      label: 'common.cancel',
      class: 'bg-red-100 text-red-800 border-red-200'
    },
    
    // Shipping status
    pending: {
      type: 'info',
      icon: 'mingcute:time-line',
      label: 'shipping.pending',
      class: 'bg-blue-100 text-blue-800 border-blue-200'
    },
    ready: {
      type: 'warning',
      icon: 'mingcute:box-line',
      label: 'shipping.ready',
      class: 'bg-orange-100 text-orange-800 border-orange-200'
    },
    shipped: {
      type: 'success',
      icon: 'mingcute:truck-line',
      label: 'shipping.shipped',
      class: 'bg-green-100 text-green-800 border-green-200'
    },
    
    // Invoice status
    to_invoice: {
      type: 'warning',
      icon: 'mingcute:bill-line',
      label: 'invoice.to_invoice',
      class: 'bg-yellow-100 text-yellow-800 border-yellow-200'
    },
    partially_invoiced: {
      type: 'info',
      icon: 'mingcute:bill-line',
      label: 'invoice.partially_invoiced',
      class: 'bg-blue-100 text-blue-800 border-blue-200'
    },
    invoiced: {
      type: 'success',
      icon: 'mingcute:check-circle-fill',
      label: 'invoice.invoiced',
      class: 'bg-green-100 text-green-800 border-green-200'
    },
    not_invoiced: {
      type: 'danger',
      icon: 'mingcute:close-circle-line',
      label: 'invoice.not_invoiced',
      class: 'bg-red-100 text-red-800 border-red-200'
    }
  }
  
  // Computed properties
  const currentStatus = computed(() => {
    return statusConfig[props.status] || statusConfig.draft
  })
  
  const tagType = computed(() => {
    return currentStatus.value.type
  })
  
  const statusIcon = computed(() => {
    return currentStatus.value.icon
  })
  
  const statusLabel = computed(() => {
    // Handle different label formats
    const labelKey = currentStatus.value.label
    
    // Try different translation patterns based on type
    if (props.type === 'shipping') {
      return t(`shipping.${props.status}`) || t(labelKey) || props.status
    } else if (props.type === 'invoice') {
      return t(`invoice.${props.status}`) || t(labelKey) || props.status
    } else {
      return t(labelKey) || t(`transaction.${props.status}`) || props.status
    }
  })
  
  const tagClass = computed(() => {
    let classes = ['border', 'transition-all', 'duration-200']
    
    // Add custom styling
    if (currentStatus.value.class) {
      classes.push(currentStatus.value.class)
    }
    
    // Size-specific classes
    if (props.size === 'small') {
      classes.push('text-xs', 'px-2', 'py-1')
    } else if (props.size === 'large') {
      classes.push('text-sm', 'px-3', 'py-2')
    }
    
    return classes.join(' ')
  })
  
  const iconClass = computed(() => {
    const classes = []
    
    if (props.size === 'small') {
      classes.push('w-3', 'h-3')
    } else if (props.size === 'large') {
      classes.push('w-5', 'h-5')
    } else {
      classes.push('w-4', 'h-4')
    }
    
    return classes.join(' ')
  })
  </script>
  
  <style scoped>
  /* Custom tag styling to override Element Plus defaults */
  :deep(.el-tag) {
    border-radius: 6px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
  }
  
  /* Hover effects */
  :deep(.el-tag):hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  /* Small size */
  :deep(.el-tag--small) {
    padding: 2px 6px;
    font-size: 11px;
    line-height: 1.2;
  }
  
  /* Default size */
  :deep(.el-tag) {
    padding: 4px 8px;
    font-size: 12px;
    line-height: 1.3;
  }
  
  /* Large size */
  :deep(.el-tag--large) {
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.4;
  }
  
  /* Status-specific animations */
  .bg-green-100 {
    animation: pulse-success 2s infinite;
  }
  
  .bg-red-100 {
    animation: pulse-danger 2s infinite;
  }
  
  .bg-yellow-100 {
    animation: pulse-warning 2s infinite;
  }
  
  @keyframes pulse-success {
    0%, 100% {
      background-color: rgb(220 252 231);
    }
    50% {
      background-color: rgb(187 247 208);
    }
  }
  
  @keyframes pulse-danger {
    0%, 100% {
      background-color: rgb(254 226 226);
    }
    50% {
      background-color: rgb(252 165 165);
    }
  }
  
  @keyframes pulse-warning {
    0%, 100% {
      background-color: rgb(254 249 195);
    }
    50% {
      background-color: rgb(253 230 138);
    }
  }
  
  /* Disable animations for users who prefer reduced motion */
  @media (prefers-reduced-motion: reduce) {
    .bg-green-100,
    .bg-red-100,
    .bg-yellow-100 {
      animation: none;
    }
    
    :deep(.el-tag):hover {
      transform: none;
    }
  }
  </style>