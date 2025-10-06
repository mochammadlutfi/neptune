<script setup lang="js">
import { useSidebar } from '@/components/ui/sidebar'
import { computed } from 'vue'
import { SidebarMenu, SidebarMenuItem, SidebarMenuButton } from '@/components/ui/sidebar'
import { Icon } from '@iconify/vue'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  size: {
    type: String,
    default: 'default'
  }
})

const { setOpenMobile, state } = useSidebar()

// Computed untuk menentukan apakah sidebar dalam mode collapsed
const isCollapsed = computed(() => state.value === 'collapsed')
</script>

<template>
  <SidebarMenu class="mb-2">
    <SidebarMenuItem>
      <SidebarMenuButton 
        as-child 
        :tooltip="isCollapsed ? $t(item.name, 2) : undefined" 
        :size="size"
        class="w-full justify-start h-8"
      >
        <router-link :to="item.to" @click="setOpenMobile(false)">
          <div class="w-full items-center flex justify-start">
            <Icon :icon="item.icon || ''" mode="svg" class="h-5 w-5 shrink-0 mr-3" />
            <span :class="{ 'absolute w-px h-px p-0 -m-px overflow-hidden whitespace-nowrap border-0': isCollapsed }">
              {{ $t(item.name, 2) }}
            </span>
          </div>
        </router-link>
      </SidebarMenuButton>
    </SidebarMenuItem>
  </SidebarMenu>
</template>

<style scoped>
/* .nav-link-button {
  transition: all 0.2s ease-in-out;
} */

/* .sidebar-icon {
  flex-shrink: 0;
  width: 1.25rem;
  height: 1.25rem;
} */

/* .sidebar-text, .sidebar-badge {
  transition: opacity 0.2s ease-in-out;
} */

/* .sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
} */

/* Styling untuk router-link */
/* .nav-link-button a {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  text-decoration: none;
  color: inherit;
} */

/* Hover effects */
/* .nav-link-button:hover {
  background-color: hsl(var(--sidebar-accent));
} */

/* Active state */
/* .nav-link-button a.router-link-active {
  background-color: hsl(var(--sidebar-primary));
  color: hsl(var(--sidebar-primary-foreground));
} */
</style>