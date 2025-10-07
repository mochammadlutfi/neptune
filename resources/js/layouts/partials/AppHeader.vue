<script setup>
import { useRoute } from 'vue-router'
import { computed } from 'vue'
import { SidebarTrigger } from '@/components/ui/sidebar'
import { Separator } from '@/components/ui/separator'
import { usePageMeta } from '@/composables/common/usePageMeta'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuLabel,
  DropdownMenuSeparator, DropdownMenuItem, DropdownMenuGroup
} from '@/components/ui/dropdown-menu'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import DropdownVessel from '@/components/shared/DropdownVessel.vue'

import { useAuth } from '@/composables/auth';
import { useUser } from '@/composables/auth';

// Auth composables
const { logout } = useAuth();
const { user } = useUser();

const { pageTitle } = usePageMeta();
const route = useRoute();

/**
 * Handle logout dengan composable auth
 */
const handleLogout = async () => {
    await logout();
}

// Vessel change handlers
const handleVesselChange = (vesselId, vessel) => {
  console.log('Vessel changed:', { vesselId, vessel })
  // Bisa ditambahkan logic untuk refresh data berdasarkan vessel
}

const handleVesselUpdated = (vesselId) => {
  console.log('Vessel updated successfully:', vesselId)
  // Bisa ditambahkan logic untuk notifikasi atau refresh data
}

</script>

<template>
  <header class="sticky top-0 z-10 h-[50px] flex items-center gap-4 border-b bg-white px-4 md:px-6">
    <div class="w-full flex items-center gap-4">
      <SidebarTrigger />
      <Separator orientation="vertical" class="h-4" />
      <div>
        <AutoBreadcrumb/>
      </div>
    </div>
    <div class="ml-auto flex items-center gap-3">
      <!-- Vessel Selector -->
      <DropdownVessel 
        @change="handleVesselChange"
        @updated="handleVesselUpdated"
        size="default"
      />
      
      <!-- User Dropdown -->
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="ghost">
            <Avatar class="h-8 w-8 rounded-lg bg-muted">
              <AvatarImage :src="user?.image || ''" :alt="user?.name || 'User'" />
              <AvatarFallback class="rounded-full">
                {{ user?.name ? user.name.split(' ').map((n) => n[0]).join('') : 'U' }}
              </AvatarFallback>
            </Avatar>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="min-w-56 w-[--radix-dropdown-menu-trigger-width] rounded-lg"
          side="bottom" align="end">
          <DropdownMenuLabel class="p-0 font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
              <Avatar class="h-8 w-8 rounded-lg bg-muted">
                <AvatarImage :src="user?.image || ''" :alt="user?.name || 'User'" />
                <AvatarFallback class="rounded-lg">
                  {{ user?.name ? user.name.split(' ').map((n) => n[0]).join('') : 'U' }}
                </AvatarFallback>
              </Avatar>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-semibold">{{ user?.name || 'Loading...' }}</span>
                <span class="truncate text-xs">{{ user?.email || '' }}</span>
              </div>
            </div>
          </DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuGroup>
            <DropdownMenuItem as-child class="cursor-pointer">
              <router-link to="/profile" @click="setOpenMobile(false)">
                <Icon icon="lucide:badge-check" />
                Profile
              </router-link>
            </DropdownMenuItem>
            <DropdownMenuItem as-child class="cursor-pointer">
              <router-link to="/profile/password" @click="setOpenMobile(false)">
                <Icon icon="lucide:settings" />
                Password
              </router-link>
            </DropdownMenuItem>
          </DropdownMenuGroup>
          <DropdownMenuSeparator />
          <DropdownMenuItem @click="handleLogout" class="cursor-pointer">
            <Icon icon="lucide:log-out" />
            Log out
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </header>
</template>
