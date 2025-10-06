<script setup>
import { useSidebar } from '@/components/ui/sidebar'
import { ref } from 'vue'
import { SidebarMenu, SidebarMenuItem, SidebarMenuButton } from '@/components/ui/sidebar'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuLabel, 
  DropdownMenuSeparator, DropdownMenuItem, DropdownMenuGroup
} from '@/components/ui/dropdown-menu'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog'
// import ThemeCustomize from '@/components/ThemeCustomize.vue'
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
defineProps({
  user: {
    type: Object,
    required: true,
    default: () => ({
      name: '',
      email: '',
      avatar: ''
    }),
    validator: (user) => {
      return (
        typeof user.name === 'string' &&
        typeof user.email === 'string' &&
        typeof user.avatar === 'string'
      )
    }
  }
})

const { isMobile, setOpenMobile } = useSidebar()

function handleLogout() {
  // navigateTo('/login')
}

const showModalTheme = ref(false)
</script>

<template>
  <SidebarMenu>
    <SidebarMenuItem>
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <SidebarMenuButton
            size="lg"
            class="data-[state=open]:bg-sidebar-primary data-[state=open]:text-sidebar-accent-foreground"
          >
            <Avatar class="h-8 w-8 rounded-lg">
              <AvatarImage :src="user.avatar" :alt="user.name" />
              <AvatarFallback class="rounded-lg">
                {{ user.name.split(' ').map((n) => n[0]).join('') }}
              </AvatarFallback>
            </Avatar>
            <div class="grid flex-1 text-left text-sm leading-tight">
              <span class="truncate font-semibold">{{ user.name }}</span>
              <span class="truncate text-xs">{{ user.email }}</span>
            </div>
            <Icon icon="lucide:chevrons-up-down" class="ml-auto size-4" />
          </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          class="min-w-56 w-[--radix-dropdown-menu-trigger-width] rounded-lg"
          :side="isMobile ? 'bottom' : 'right'"
          align="end"
        >
          <DropdownMenuLabel class="p-0 font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
              <Avatar class="h-8 w-8 rounded-lg">
                <AvatarImage :src="user.avatar" :alt="user.name" />
                <AvatarFallback class="rounded-lg">
                  {{ user.name.split(' ').map((n) => n[0]).join('') }}
                </AvatarFallback>
              </Avatar>
              <div class="grid flex-1 text-left text-sm leading-tight">
                <span class="truncate font-semibold">{{ user.name }}</span>
                <span class="truncate text-xs">{{ user.email }}</span>
              </div>
            </div>
          </DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuGroup>
            <DropdownMenuItem>
              <Icon icon="lucide:sparkles" />
              Upgrade to Pro
            </DropdownMenuItem>
          </DropdownMenuGroup>
          <DropdownMenuSeparator />
          <DropdownMenuGroup>
            <DropdownMenuItem>
              <Icon icon="lucide:badge-check" />
              Account
            </DropdownMenuItem>
            <DropdownMenuItem as-child>
              <router-link to="/settings" @click="setOpenMobile(false)">
                <Icon icon="lucide:settings" />
                Settings
              </router-link>
            </DropdownMenuItem>
            <DropdownMenuItem>
              <Icon icon="lucide:bell" />
              Notifications
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem as-child>
              <router-link to="https://github.com/dianprata/nuxt-shadcn-dashboard" external target="_blank">
                <Icon icon="lucide:github" />
                Github Repository
              </router-link>
            </DropdownMenuItem>
            <DropdownMenuItem @click="showModalTheme = true">
              <Icon icon="lucide:paintbrush" />
              Theme
            </DropdownMenuItem>
          </DropdownMenuGroup>
          <DropdownMenuSeparator />
          <DropdownMenuItem @click="handleLogout">
            <Icon icon="lucide:log-out" />
            Log out
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </SidebarMenuItem>
  </SidebarMenu>

  <Dialog v-model:open="showModalTheme">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Customize</DialogTitle>
        <DialogDescription class="text-xs text-muted-foreground">
          Customize & Preview in Real Time
        </DialogDescription>
      </DialogHeader>
      <!-- <ThemeCustomize /> -->
    </DialogContent>
  </Dialog>
</template>

<style scoped>

</style>
