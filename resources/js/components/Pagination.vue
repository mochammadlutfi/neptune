<template>
    <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-t border-gray-100 gap-4">
        <!-- Page Size Selector -->
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">{{ $t('common.pagination.per_page') }}:</span>
            <el-select 
                :model-value="pageSize" 
                @update:model-value="$emit('update:page-size', $event)"
                class="w-20" 
                size="small"
            >
                <el-option 
                    v-for="size in pageSizeOptions" 
                    :key="size" 
                    :label="size.toString()" 
                    :value="size"
                />
            </el-select>
        </div>

        <!-- Results Info and Pagination -->
        <div class="flex align-middle gap-4 items-center">
            <!-- Results Count -->
            <div class="text-sm text-gray-600 my-auto">
                <template v-if="total > 0">
                    {{ $t("common.pagination.showing_results", {
                        from: from,
                        to: to,
                        total: total,
                    }) }}
                </template>
                <template v-else>
                    {{ $t('common.messages.no_data') }}
                </template>
            </div>

            <!-- Pagination Controls -->
            <el-pagination
                v-if="total > 0"
                background
                :layout="layout"
                :page-size="pageSize"
                :total="total"
                :current-page="currentPage"
                @current-change="$emit('page-change', $event)"
                class="flex-shrink-0"
                :small="small"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

// Props
const props = defineProps({
    // Pagination data
    currentPage: {
        type: Number,
        default: 1
    },
    pageSize: {
        type: Number,
        default: 25
    },
    total: {
        type: Number,
        default: 0
    },
    from: {
        type: Number,
        default: 0
    },
    to: {
        type: Number,
        default: 0
    },
    
    // Customization options
    pageSizeOptions: {
        type: Array,
        default: () => [25, 50, 100]
    },
    layout: {
        type: String,
        default: 'prev, pager, next'
    },
    small: {
        type: Boolean,
        default: false
    },
    showBorder: {
        type: Boolean,
        default: true
    }
})

// Events
defineEmits([
    'update:page-size',
    'page-change'
])
</script>

<style scoped>
/* Custom styling untuk pagination component */
.pagination-container {
    @apply flex flex-col sm:flex-row justify-between items-center gap-4;
}

.pagination-container.no-border {
    @apply border-t-0;
}
</style>