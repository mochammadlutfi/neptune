<template>
  <div class="detail-field">
    <dt class="text-sm font-medium text-gray-500 mb-1">
      {{ label }}
    </dt>
    <dd class="text-sm text-gray-900">
      <slot name="value">
        <span v-if="value" class="break-words">{{ value }}</span>
        <span v-else class="text-gray-400 italic">{{ $t('common.no_data') }}</span>
      </slot>
    </dd>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

// Props definition
defineProps({
  label: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number, Boolean, Object, Array],
    default: null
  }
})
</script>

<style scoped>
.detail-field {
  @apply space-y-1;
}

.detail-field dt {
  @apply text-sm font-medium text-gray-500;
}

.detail-field dd {
  @apply text-sm text-gray-900;
}
</style>