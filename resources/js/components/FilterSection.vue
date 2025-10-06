<template>
    <div class="border-b border-gray-200">
        <!-- Filter Toggle Button (Mobile) -->
        <div class="p-4 sm:hidden">
            <el-button @click="showMobileFilters = !showMobileFilters" class="w-full">
                <Icon icon="mingcute:filter-line" class="mr-2" />
                {{ $t('common.filters') }}
                <Icon 
                    :icon="showMobileFilters ? 'mingcute:up-line' : 'mingcute:down-line'" 
                    class="ml-auto" 
                />
            </el-button>
        </div>

        <!-- Filter Content -->
        <div 
            class="transition-all duration-300 ease-in-out"
            :class="[
                'sm:block', // Always show on desktop
                showMobileFilters ? 'block' : 'hidden' // Show/hide on mobile
            ]"
        >
            <!-- Main Search Row -->
            <div class="p-4 flex flex-col sm:flex-row gap-4 sm:items-center">
                <!-- Search Input -->
                <div class="flex-1 max-w-xs">
                    <el-input 
                        :disabled="loading" 
                        :model-value="searchValue"
                        @input="$emit('update:search', $event)"
                        :placeholder="searchPlaceholder"
                        clearable
                        class="w-full"
                    >
                        <template #prefix>
                            <Icon icon="mingcute:search-line" />
                        </template>
                    </el-input>
                </div>

                <!-- Quick Filter Buttons -->
                <div v-if="quickFilters && quickFilters.length > 0" class="flex flex-wrap gap-2">
                    <DropdownFilter
                        :options="quickFilters"
                        :label="$t('common.all')"
                        :model-value="activeQuickFilter ? [activeQuickFilter] : []"
                        @update:model-value="(val) => $emit('quick-filter', val.length > 0 ? val[0] : null)"
                    />
                </div>

                <!-- Advanced Filter Toggle -->
                <el-button 
                    v-if="hasAdvancedFilters"
                    @click="showAdvancedFilters = !showAdvancedFilters"
                    :type="showAdvancedFilters ? 'primary' : 'default'"
                >
                    <Icon icon="mingcute:settings-3-line" class="mr-1" />
                    {{ $t('common.filters.filters') }}
                    <el-badge v-if="activeFiltersCount > 0" :value="activeFiltersCount" class="ml-1" />
                </el-button>
            </div>

            <!-- Advanced Filters -->
            <el-collapse-transition>
                <div v-if="showAdvancedFilters && hasAdvancedFilters" class="p-4 bg-muted">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 xl:grid-cols-6 gap-4">
                        <slot name="advanced-filters" />
                    </div>
                    
                    <!-- Filter Actions -->
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100">
                        <el-button @click="$emit('apply-filters')">
                            <Icon icon="fluent:checkmark-24-filled" class="mr-1" />
                            {{ $t('common.actions.apply') }}
                        </el-button>
                        <el-button @click="$emit('clear-filters')">
                            <Icon icon="fluent:arrow-counterclockwise-24-filled" class="mr-1" />
                            {{ $t('common.actions.reset') }}
                        </el-button>
                    </div>
                </div>
            </el-collapse-transition>

            <!-- Active Filters Display -->
            <div v-if="activeFilters && activeFilters.length > 0" class="px-4 py-2 flex flex-wrap gap-2 border-t border-muted">
                <span class="text-sm text-gray-600">{{ $t('common.filters.filter_by') }}:</span>
                <el-tag
                    v-for="filter in activeFilters"
                    :key="filter.key"
                    closable
                    size="small"
                    @close="$emit('remove-filter', filter.key)"
                >
                    {{ filter.label }}: {{ filter.value }}
                </el-tag>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    searchValue: {
        type: String,
        default: ''
    },
    searchPlaceholder: {
        type: String,
        default: 'Search...'
    },
    loading: {
        type: Boolean,
        default: false
    },
    quickFilters: {
        type: Array,
        default: () => []
    },
    activeQuickFilter: {
        type: String,
        default: null
    },
    activeFilters: {
        type: Array,
        default: () => []
    },
    hasAdvancedFilters: {
        type: Boolean,
        default: false
    },
    defaultShowAdvanced: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits([
    'update:search',
    'quick-filter',
    'apply-filters',
    'clear-filters',
    'remove-filter'
])

const showMobileFilters = ref(false)
const showAdvancedFilters = ref(props.defaultShowAdvanced)

const activeFiltersCount = computed(() => {
    return props.activeFilters ? props.activeFilters.length : 0
})
</script>

<style scoped>
:deep(.el-collapse-transition) {
    overflow: hidden;
}

:deep(.el-badge__content) {
    min-width: 16px;
    height: 16px;
    line-height: 16px;
    font-size: 10px;
}
</style>