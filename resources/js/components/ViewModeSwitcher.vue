<template>
    <div class="flex items-center gap-2">
        <!-- Bulk Actions -->
        <div v-if="selectedRows.length > 0 && showBulkActions" class="flex items-center gap-2">
            <span class="text-sm text-gray-600">
                {{ selectedRows.length }} {{ $t('common.selected') }}
            </span>
            <slot name="bulk-actions" :selectedRows="selectedRows">
                <el-button size="small" @click="$emit('bulk-export', selectedRows)">
                    <Icon icon="mingcute:download-line" class="mr-1" />
                    {{ $t('common.export_selected') }}
                </el-button>
                <el-button size="small" type="danger" @click="$emit('bulk-delete', selectedRows)">
                    <Icon icon="mingcute:delete-2-line" class="mr-1" />
                    {{ $t('common.delete_selected') }}
                </el-button>
            </slot>
        </div>

        <!-- Refresh -->
        <el-button @click="$emit('refresh')" :loading="loading" circle>
            <Icon icon="mingcute:refresh-1-line" />
        </el-button>

        <!-- View Mode Switcher -->
        <el-dropdown @command="changeView">
            <el-button>
                <Icon icon="mingcute:layout-line" class="mr-2" />
                {{ $t('common.view') }}
                <Icon icon="mingcute:down-line" class="ml-2" />
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item command="table" :class="{ 'is-active': viewMode === 'table' }">
                        <Icon icon="mingcute:table-line" class="mr-2" />
                        {{ $t('common.table_view') }}
                    </el-dropdown-item>
                    <el-dropdown-item command="card" :class="{ 'is-active': viewMode === 'card' }">
                        <Icon icon="mingcute:grid-line" class="mr-2" />
                        {{ $t('common.card_view') }}
                    </el-dropdown-item>
                    <el-dropdown-item command="tree" :class="{ 'is-active': viewMode === 'tree' }" v-if="showTreeView">
                        <Icon icon="mingcute:tree-line" class="mr-2" />
                        {{ $t('common.tree_view') }}
                    </el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
    </div>
</template>
 
<script setup>
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    viewMode: {
        type: String,
        default: 'table',
        validator: (value) => ['table', 'card', 'tree'].includes(value)
    },
    showTreeView: {
        type: Boolean,
        default: false
    },
    selectedRows: {
        type: Array,
        default: () => []
    },
    showBulkActions: {
        type: Boolean,
        default: true
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:viewMode', 'refresh', 'bulk-export', 'bulk-delete'])

const changeView = (mode) => {
    emit('update:viewMode', mode)
}
</script>

<style scoped>
:deep(.el-dropdown-menu__item.is-active) {
    color: var(--el-color-primary);
    background-color: var(--el-color-primary-light-9);
}
</style>
