<script setup lang="js">
import { useSidebar } from '@/components/ui/sidebar'
import { ref, computed } from 'vue'
import { SidebarMenu,SidebarMenuItem, SidebarMenuButton, SidebarMenuSub, 
  SidebarMenuSubItem, SidebarMenuSubButton } from '@/components/ui/sidebar'
import { Collapsible, CollapsibleTrigger, CollapsibleContent } from '@/components/ui/collapsible'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

import { Icon } from '@iconify/vue';
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

const isCollapsed = computed(() => state.value === 'collapsed')
const openCollapsible = ref(false)
</script>

<template>
  <SidebarMenu class="mb-2">
    <Collapsible
      :key="item.title"
      v-model:open="openCollapsible"
      as-child
      class="group/collapsible" v-if="!isCollapsed"
    >
      <SidebarMenuItem>
        <CollapsibleTrigger as-child>
          <SidebarMenuButton :tooltip="$t(item.name,2)" :size="size" class="w-full justify-start h-8">
            <div class="w-full items-center flex justify-between">
              <Icon :icon="item.icon || ''" mode="svg" class="h-5 w-5 mr-3" />
              <span class="sidebar-text" :class="{ 'absolute w-px h-px p-0 -m-px overflow-hidden whitespace-nowrap border-0': isCollapsed }">
                {{ $t(item.name, 2) }}
              </span>
              <Icon v-if="!isCollapsed" icon="lucide:chevron-right" class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
            </div>    
          </SidebarMenuButton>
        </CollapsibleTrigger>
        <CollapsibleContent>
          <SidebarMenuSub class="pl-5 ml-5">
            <SidebarMenuSubItem
              v-for="subItem in item.children"
              :key="$t(subItem.name, 2)"
            >
              <SidebarMenuSubButton as-child>
                <router-link :to="subItem.to" @click="setOpenMobile(false)" class="flex gap-2 items-center	w-full">
                  <span>
                    {{ $t(subItem.name, 2) }}
                  </span>
                  <span v-if="subItem.new" class="rounded-md bg-#adfa1d px-1.5 py-0.5 text-xs text-black leading-none no-underline group-hover:no-underline">
                    New
                  </span>
                </router-link>
              </SidebarMenuSubButton>
            </SidebarMenuSubItem>
          </SidebarMenuSub>
        </CollapsibleContent>
      </SidebarMenuItem>
    </Collapsible>
    
    <DropdownMenu dir="ltr" v-else>
      <SidebarMenuItem>
      <DropdownMenuTrigger asChild>
        <SidebarMenuButton :tooltip="$t(item.name,2)" :size="size" class="w-full justify-start h-8">
          <div class="w-full items-center flex justify-between">
            <Icon :icon="item.icon || ''" mode="svg" class="h-5 w-5" />
            <span class="sidebar-text" :class="{ 'absolute w-px h-px p-0 -m-px overflow-hidden whitespace-nowrap border-0': isCollapsed }">
              {{ $t(item.name, 2) }}
            </span>
            <Icon v-if="!isCollapsed" icon="lucide:chevron-right" class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
          </div>    
        </SidebarMenuButton>
      </DropdownMenuTrigger>
        <DropdownMenuContent  side="right" :sideOffset="10" align="start">
          <DropdownMenuLabel class="max-w-[190px] truncate">
            {{ $t(item.name, 2) }}
          </DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuItem
              v-for="subItem in item.children"
              :key="$t(subItem.name, 2)"
            >
              <router-link :to="subItem.to" @click="setOpenMobile(false)" class="flex gap-2 items-center	w-full">
                <span>
                  {{ $t(subItem.name, 2) }}
                </span>
                <span v-if="subItem.new" class="rounded-md bg-#adfa1d px-1.5 py-0.5 text-xs text-black leading-none no-underline group-hover:no-underline">
                  New
                </span>
              </router-link>
          </DropdownMenuItem>
        </DropdownMenuContent>
      </SidebarMenuItem>
    </DropdownMenu>
  </SidebarMenu>
</template>

<style scoped>

</style>
