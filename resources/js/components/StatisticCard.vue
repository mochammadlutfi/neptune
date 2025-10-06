<template>
    <div class="rounded-lg border bg-white p-4 shadow-sm transition-shadow hover:shadow-md">
        <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-600 truncate">{{ title || label }}</p>
                <p class="text-xl font-bold mt-1" :class="valueClass">
                    {{ formattedValue }}
                </p>
                <p v-if="subtitle" class="text-xs text-gray-500 mt-1 truncate">{{ subtitle }}</p>
            </div>
            <div class="flex-shrink-0 ml-4">
                <div class="rounded-md p-2 lg:p-3" :class="iconBgClass">
                    <Icon :icon="icon" class="h-5 w-5 lg:h-6 lg:w-6" :class="iconClass" />
                </div>
            </div>
        </div>
        
        <!-- Optional trend indicator -->
        <div v-if="trend" class="flex items-center mt-2 pt-2 border-t border-gray-100">
            <Icon 
                :icon="trend.direction === 'up' ? 'mingcute:arrow-up-line' : 'mingcute:arrow-down-line'" 
                class="h-4 w-4 mr-1"
                :class="trend.direction === 'up' ? 'text-green-500' : 'text-red-500'"
            />
            <span class="text-sm" :class="trend.direction === 'up' ? 'text-green-600' : 'text-red-600'">
                {{ trend.value }}
            </span>
            <span class="text-xs text-gray-500 ml-1">{{ trend.label }}</span>
        </div>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { computed } from 'vue'
import { useFormatter } from '@/composables/common/useFormatter'

const { formatCurrency, formatNumber } = useFormatter()

const props = defineProps({
    title: {
        type: String,
        default: null
    },
    label: {
        type: String,
        default: null
    },
    value: {
        type: [Number, String],
        required: true
    },
    subtitle: {
        type: String,
        default: null
    },
    icon: {
        type: String,
        required: true
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'yellow', 'red', 'purple', 'indigo', 'gray'].includes(value)
    },
    format: {
        type: String,
        default: 'number',
        validator: (value) => ['number', 'currency', 'percent', 'text'].includes(value)
    },
    trend: {
        type: Object,
        default: null,
        validator: (value) => {
            if (!value) return true
            return value.direction && ['up', 'down'].includes(value.direction) && 
                   value.value !== undefined && value.label !== undefined
        }
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const formattedValue = computed(() => {
    if (props.loading) return '...'
    
    switch (props.format) {
        case 'currency':
            return formatCurrency(props.value)
        case 'number':
            return formatNumber(props.value)
        case 'percent':
            return `${props.value}%`
        default:
            return props.value
    }
})

const colorClasses = {
    blue: {
        icon: 'text-blue-600',
        iconBg: 'bg-blue-50',
        value: 'text-gray-900'
    },
    green: {
        icon: 'text-green-600',
        iconBg: 'bg-green-50',
        value: 'text-green-600'
    },
    yellow: {
        icon: 'text-yellow-600',
        iconBg: 'bg-yellow-50',
        value: 'text-yellow-600'
    },
    red: {
        icon: 'text-red-600',
        iconBg: 'bg-red-50',
        value: 'text-red-600'
    },
    purple: {
        icon: 'text-purple-600',
        iconBg: 'bg-purple-50',
        value: 'text-purple-600'
    },
    indigo: {
        icon: 'text-indigo-600',
        iconBg: 'bg-indigo-50',
        value: 'text-indigo-600'
    },
    gray: {
        icon: 'text-gray-600',
        iconBg: 'bg-gray-50',
        value: 'text-gray-900'
    }
}

const iconClass = computed(() => colorClasses[props.color].icon)
const iconBgClass = computed(() => colorClasses[props.color].iconBg)
const valueClass = computed(() => colorClasses[props.color].value)
</script>

<style scoped>
/* Add any custom styles here */
</style>