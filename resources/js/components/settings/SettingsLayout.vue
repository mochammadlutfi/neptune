<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <router-link to="/settings" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <Icon icon="mingcute:arrow-left-line" class="text-xl text-gray-600" />
                        </router-link>
                        <div>
                            <h1 class="text-xl font-semibold text-gray-900">{{ pageTitle }}</h1>
                            <p v-if="pageDescription" class="text-sm text-gray-500">{{ pageDescription }}</p>
                        </div>
                    </div>
                    <slot name="header-actions" />
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex gap-8">
                <!-- Sidebar -->
                <div v-if="showSidebar" class="w-64 flex-shrink-0">
                    <div class="sticky top-8">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200/60 p-4">
                            <nav class="space-y-1">
                                <slot name="sidebar" />
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1 min-w-0">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue';

defineProps({
    pageTitle: String,
    pageDescription: String,
    showSidebar: {
        type: Boolean,
        default: true
    }
});
</script>