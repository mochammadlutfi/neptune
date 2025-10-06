<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" :disabled="isLoading">
                <Avatar class="h-8 w-8 rounded-lg bg-muted">
                    <AvatarImage :src="user?.image_url || user?.image || ''" :alt="user?.name || 'User'" />
                    <AvatarFallback class="rounded-full">
                        {{ userInitials }}
                    </AvatarFallback>
                </Avatar>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="min-w-56 w-[--radix-dropdown-menu-trigger-width] rounded-lg" side="bottom"
            align="end">
            <DropdownMenuLabel class="p-0 font-normal">
                <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                    <Avatar class="h-8 w-8 rounded-lg bg-muted">
                        <AvatarImage :src="user?.image_url || user?.image || ''" :alt="user?.name || 'User'" />
                        <AvatarFallback class="rounded-lg">
                            {{ userInitials }}
                        </AvatarFallback>
                    </Avatar>
                    <div class="grid flex-1 text-left text-sm leading-tight">
                        <span class="truncate font-semibold">{{ user?.name || 'Loading...' }}</span>
                        <span class="truncate text-xs text-muted-foreground">{{ user?.email || '' }}</span>
                    </div>
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                <DropdownMenuItem @click="handleUpgrade">
                    <Icon icon="lucide:sparkles" class="mr-2 h-4 w-4" />
                    Upgrade to Pro
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                <DropdownMenuItem @click="handleAccount">
                    <Icon icon="lucide:badge-check" class="mr-2 h-4 w-4" />
                    Account
                </DropdownMenuItem>
                <DropdownMenuItem as-child>
                    <router-link to="/settings" @click="handleMobileClose">
                        <Icon icon="lucide:settings" class="mr-2 h-4 w-4" />
                        Settings
                    </router-link>
                </DropdownMenuItem>
                <DropdownMenuItem @click="handleNotifications">
                    <Icon icon="lucide:bell" class="mr-2 h-4 w-4" />
                    Notifications
                    <Badge v-if="notificationCount > 0" variant="destructive" class="ml-auto">
                        {{ notificationCount }}
                    </Badge>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem as-child>
                    <a href="https://github.com/dianprata/nuxt-shadcn-dashboard" target="_blank" rel="noopener noreferrer">
                        <Icon icon="lucide:github" class="mr-2 h-4 w-4" />
                        Github Repository
                    </a>
                </DropdownMenuItem>
                <DropdownMenuItem @click="handleTheme">
                    <Icon icon="lucide:paintbrush" class="mr-2 h-4 w-4" />
                    Theme
                </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem @click="handleLogout" class="cursor-pointer text-red-600 focus:text-red-600" :disabled="isLoading">
                <Icon icon="lucide:log-out" class="mr-2 h-4 w-4" />
                <span v-if="isLoading">Logging out...</span>
                <span v-else>Log out</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuth } from '@/composables/auth/useAuth'
import { useUser } from '@/composables/auth/useUser'
import { useRouter } from 'vue-router'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
  DropdownMenuGroup
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Icon } from '@iconify/vue'

// Props
const props = defineProps({
  /**
   * Callback untuk menutup mobile menu
   */
  onMobileClose: {
    type: Function,
    default: () => {}
  }
})

// Emits
const emit = defineEmits([
  'theme-toggle',
  'account-click',
  'notifications-click',
  'upgrade-click'
])

// Composables
const { logout, isLoading: authLoading } = useAuth()
const { user, isLoading: userLoading, userInitials, getUser } = useUser()
const router = useRouter()

// Combined loading state
const isLoading = computed(() => authLoading.value || userLoading.value)

// Local state
const notificationCount = ref(0)

/**
 * Handle logout user
 */
const handleLogout = async () => {
  try {
    await logout()
    // Router push sudah dihandle di dalam composable logout
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

/**
 * Handle mobile menu close
 */
const handleMobileClose = () => {
  props.onMobileClose()
}

/**
 * Handle theme toggle
 */
const handleTheme = () => {
  emit('theme-toggle')
}

/**
 * Handle account click
 */
const handleAccount = () => {
  emit('account-click')
  router.push('/profile')
}

/**
 * Handle notifications click
 */
const handleNotifications = () => {
  emit('notifications-click')
  router.push('/notifications')
}

/**
 * Handle upgrade click
 */
const handleUpgrade = () => {
  emit('upgrade-click')
  // Implementasi upgrade logic di sini
}

// Load user data saat component mounted
onMounted(async () => {
  try {
    if (!user.value) {
      await getUser()
    }
    // TODO: Load notification count
    // notificationCount.value = await getNotificationCount()
  } catch (error) {
    console.error('Failed to load user data:', error)
  }
})
</script>