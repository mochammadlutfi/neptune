<template>
    <SidebarProvider>
        <AppSidebar />
            <SidebarInset>
            <AppHeader/>
            <div class="min-w-0 w-full flex-1 overflow-x-auto p-4 lg:p-6">
                <slot />
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>

<script setup>
import SidebarProvider from '@/components/ui/sidebar/SidebarProvider.vue'
import AppSidebar from './Partials/AppSidebar.vue'
import SidebarInset from '@/components/ui/sidebar/SidebarInset.vue'
import AppHeader from './Partials/AppHeader.vue'
</script>