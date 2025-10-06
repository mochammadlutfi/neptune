<template>
    <el-card body-class="!p-4" class="!rounded-lg !shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between w-full gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900 flex flex-col sm:flex-row sm:items-center gap-2">
                    <div class="flex items-center">
                        <Icon v-if="icon" :icon="icon" class="inline mr-2 lg:mr-3 text-blue-600 flex-shrink-0" />
                        <span class="truncate">{{ title }}</span>
                    </div>
                    <!-- Optional status/badge slot -->
                    <slot name="status" />
                </h1>
            </div>
            
            <!-- Actions section -->
            <div class="flex items-center gap-2 lg:gap-3 flex-shrink-0">
                <!-- Custom action buttons slot -->
                <slot name="actions">
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <!-- Primary action button -->
                        <el-button 
                            v-if="primaryAction"
                            type="primary" 
                            :tag="primaryAction.tag || 'button'"
                            :to="primaryAction.to"
                            :href="primaryAction.href"
                            @click="primaryAction.click"
                            :loading="primaryAction.loading"
                            :disabled="primaryAction.disabled"
                            class="flex-1 sm:flex-none"
                        >
                            <Icon v-if="primaryAction.icon" :icon="primaryAction.icon" class="mr-1 sm:mr-2" />
                            <span class="hidden sm:inline">{{ primaryAction.label }}</span>
                            <span class="sm:hidden">{{ primaryAction.shortLabel || primaryAction.label }}</span>
                        </el-button>
                        
                        <!-- Secondary action buttons -->
                        <template v-for="action in secondaryActions" :key="action.key">
                            <el-button 
                                :type="action.type || 'default'"
                                :tag="action.tag || 'button'"
                                :to="action.to"
                                :href="action.href"
                                @click="action.click"
                                :loading="action.loading"
                                :disabled="action.disabled"
                                class="flex-1 sm:flex-none"
                            >
                                <Icon v-if="action.icon" :icon="action.icon" class="mr-1 sm:mr-2" />
                                <span class="hidden sm:inline">{{ action.label }}</span>
                                <span class="sm:hidden">{{ action.shortLabel || action.label }}</span>
                            </el-button>
                        </template>
                    </div>
                </slot>
                
                <!-- Additional actions dropdown -->
                <el-dropdown v-if="dropdownActions && dropdownActions.length > 0" popper-class="dropdown-action" placement="bottom-end" trigger="click">
                    <el-button circle class="!p-2">
                        <Icon icon="mingcute:more-2-fill" />
                    </el-button>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <template v-for="action in dropdownActions" :key="action.key">
                                <el-dropdown-item 
                                    v-if="action.type !== 'divider'"
                                    @click="action.click"
                                    :class="action.class"
                                    :disabled="action.disabled"
                                >
                                    <component 
                                        v-if="action.to"
                                        :is="'router-link'"
                                        :to="action.to"
                                        class="flex items-center w-full"
                                    >
                                        <Icon v-if="action.icon" :icon="action.icon" class="me-2" />
                                        {{ action.label }}
                                    </component>
                                    <div v-else class="flex items-center">
                                        <Icon v-if="action.icon" :icon="action.icon" class="me-2" />
                                        {{ action.label }}
                                    </div>
                                </el-dropdown-item>
                                <el-dropdown-item v-else divided />
                            </template>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>
        </div>
        
        <!-- Additional content slot -->
        <slot name="additional" />
    </el-card>
</template>

<script setup>
import { Icon } from '@iconify/vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: null
    },
    icon: {
        type: String,
        default: null
    },
    showBreadcrumb: {
        type: Boolean,
        default: true
    },
    primaryAction: {
        type: Object,
        default: null,
        validator: (value) => {
            if (!value) return true
            return value.label !== undefined
        }
    },
    secondaryActions: {
        type: Array,
        default: () => []
    },
    dropdownActions: {
        type: Array,
        default: () => []
    }
})
</script>

<style scoped>
.content-header {
    border: 1px solid #e5e7eb;
}

@media (max-width: 768px) {
    .content-header {
        padding: 1rem;
    }
}
</style>