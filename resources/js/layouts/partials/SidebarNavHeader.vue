<script setup>
import { useSidebar } from '@/components/ui/sidebar'
import { ref } from 'vue'
import { SidebarMenu, SidebarMenuItem, SidebarMenuButton } from '@/components/ui/sidebar'
import { DropdownMenu,DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem, 
  DropdownMenuSeparator, DropdownMenuShortcut ,DropdownMenuLabel } from '@/components/ui/dropdown-menu'

const props = defineProps({
  teams: {
    type: Array,
    required: true,
    default: () => [],
    validator: (value) => {
      return value.every(team => 
        typeof team.name === 'string' &&
        typeof team.logo === 'string' &&
        typeof team.plan === 'string'
      )
    }
  }
})

const { isMobile } = useSidebar()

const activeTeam = ref(props.teams[0])
</script>

<template>
  <SidebarMenu>
    <SidebarMenuItem>
      <el-image :src="isMobile ? '/images/logo/logo-sm.png' : '/images/logo/logo.png'" class="h-10"/>
    </SidebarMenuItem>
  </SidebarMenu>
</template>

<style scoped>

</style>
