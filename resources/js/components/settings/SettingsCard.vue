<template>
    <el-card class="!rounded-xl !shadow-sm border-gray-200/60 hover:shadow-md transition-all duration-200" :body-style="{ padding: '0' }">
        <template #header v-if="title || $slots.header">
            <div class="flex items-center justify-between py-4 px-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div v-if="icon" class="p-2 rounded-lg" :class="iconColor || 'bg-blue-50'">
                        <Icon :icon="icon" class="text-lg" :class="iconTextColor || 'text-blue-600'" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                        <p v-if="description" class="text-sm text-gray-500 mt-1">{{ description }}</p>
                    </div>
                </div>
                <slot name="header-actions" />
            </div>
        </template>
        
        <div class="p-6">
            <slot />
        </div>
        
        <template #footer v-if="$slots.footer">
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                <slot name="footer" />
            </div>
        </template>
    </el-card>
</template>

<script setup>
import { Icon } from '@iconify/vue';

defineProps({
    title: String,
    description: String,
    icon: String,
    iconColor: String,
    iconTextColor: String,
});
</script>