<!-- AppSidebar.vue - Bagian yang perlu diperbaiki -->
<script setup>
import { computed, onMounted, reactive } from 'vue'
import { useAppSettings } from '@/composables/common/useAppSetting'
import { 
  SidebarGroup, 
  Sidebar, 
  SidebarHeader, 
  SidebarContent,
  SidebarRail
} from '@/components/ui/sidebar'
import SidebarNavGroup from '@/layouts/partials/SidebarNavGroup.vue'
import SidebarNavLink from '@/layouts/partials/SidebarNavLink.vue'
import { useMenu } from '@/composables/common/useMenu'
import { useAuth } from '@/composables/auth'
import { useSidebar } from "@/components/ui/sidebar"
import { SidebarMenu, SidebarMenuItem } from '@/components/ui/sidebar'
import { useAbility } from '@casl/vue'

// Composables
const menuStore = useMenu()
const { isLoggedIn } = useAuth()
// const navigation = computed(() => menuList.main || [])

const {
  state,
  open,
  setOpen,
  openMobile,
  setOpenMobile,
  isMobile,
  toggleSidebar,
} = useSidebar()

// CASL ability helper
const { can } = useAbility()
const canPermission = (perm) => {
  if (!perm) return true
  const [subjectSlug, action] = perm.split('.')
  const subject = subjectSlug.charAt(0) + subjectSlug.slice(1)
  return can(action || 'view', subject)
}


const navigation = reactive([
  
    {
        name: "base.dashboard",
        to: "/dashboard",
        icon: "fluent:home-24-regular",
        priority: 1,
        metadata: {
            description: "base.dashboard_desc",
            frequency: "continuous",
            category: "operational"
        }
    },
    {
        name: "master.title",
        icon: "fluent:database-24-regular",
        subActivePaths: "/master/",
        priority: 2,
        metadata: {
            description: "master.description",
            frequency: "low",
            category: "administrative"
        },
        children: [
            {
                name: "master.contracts.title",
                to: "/master/contracts",
                permission: "master-contracts.view",
                metadata: {
                    description: "master.contracts_desc"
                }
            },
            {
                name: "master.vessels.title",
                to: "/master/vessels",
                permission: "master-vessels.view",
                metadata: {
                    description: "master.vessels_desc"
                }
            },
            {
                name: "master.wells.title",
                to: "/master/wells",
                permission: "master-wells.view",
                metadata: {
                    description: "master.wells_desc"
                }
            },
            {
                name: "master.equipment.title",
                to: "/master/equipment",
                permission: "master-equipment.view",
                metadata: {
                    description: "master.equipment_desc"
                }
            },
            {
                name: "master.chemicals.title",
                to: "/master/chemicals",
                permission: "master-chemicals.view",
                metadata: {
                    description: "master.chemicals_desc"
                }
            },
            {
                name: "master.gas_buyer.title",
                to: "/master/gas-buyer",
                permission: "master-gas_buyer.view",  
                metadata: {
                    description: "master.gas_buyer_desc"
                }
            },
            {
                name: "master.tank.title",
                to: "/master/tank",
                permission: "master-tank.view",  
                metadata: {
                    description: "master.tank_desc"
                }
            }
        ]
    },
    {
        name: "production.title",
        icon: "fluent:production-24-regular",
        subActivePaths: "/production/",
        children: [
            {
                name: "production.sales_gas_nomination.title",
                to: "/production/sales-gas-nomination",
                permission: "production-sales_gas_nomination.view"  
            },
            {
                name: "production.sales_gas_metering.title",
                to: "/production/sales-gas-metering",
                permission: "production-sales_gas_metering.view"
            },
            {
                name: "production.sales_gas_allocation.title",
                to: "/production/sales-gas-allocation",
                permission: "production-sales_gas_allocation.view"  
            },
            {
                name: "production.operation.title",
                to: "/production/operation",
                permission: "production-operation.view"
            },
            {
                name: "production.daily_production.title",
                to: "/production/daily-production",
                permission: "production-daily_production.view"
            },
        ]
    },
    {
        name: "settings.title",
        icon: "fluent:settings-24-regular",
        subActivePaths: "/settings/",
        children: [
            {
                name: "settings.user.title",
                to: "/settings/user",
                permission: "settings-user.view",
            },
            {
                name: "settings.role_permission.title",
                to: "/settings/permission",
                permission: "settings-role_permission.view"
            },
            {
                name: "settings.system.title",
                to: "/settings/system",
                permission: "settings-system.view",
                metadata: {
                    description: "settings.system_desc"
                }
            },
        ]
    }
])

// Filter navigation berdasarkan ability: parent muncul jika punya child yang lolos
const filteredNavigation = computed(() => {
  return navigation
    .map(item => {
      if (item.children && item.children.length > 0) {
        const filteredChildren = item.children.filter(child => canPermission(child.permission))
        if (filteredChildren.length === 0) return null
        return { ...item, children: filteredChildren }
      }
      // Single-level item
      if (!item.permission || canPermission(item.permission)) {
        return item
      }
      return null
    })
    .filter(Boolean)
})

// Lifecycle
onMounted(async () => {
  // await tryFetchMenus()
});

// Perbaikan: Logic collapsible yang benar
const collapsible = computed(() => {
  // Jika mobile, gunakan off-canvas
  if (isMobile.value) {
    return 'off-canvas';
  }
  // Jika desktop, gunakan icon untuk collapse/expand
  return 'icon';
})
const logo = computed(() => {
  if(!open.value && !isMobile.value){
    return '/images/logo/logo-sm.png'
  }
  return '/images/logo/logo.png'
})

// Settings
const { sidebar } = useAppSettings()
</script>

<template>
  <!-- Gunakan computed collapsible dan tambahkan class untuk styling -->
  <Sidebar 
    :collapsible="collapsible" 
    :side="sidebar.side" 
    :variant="sidebar.variant"
  >
    <SidebarHeader>
      <!-- <LayoutSidebarNavHeader :collapsible="collapsible"/> -->
       
      <SidebarMenu>
        <SidebarMenuItem>
          <el-image :src="logo" class="h-10"/>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>
    <SidebarContent>
      <SidebarGroup>
        <template v-if="navigation && navigation.length > 0">
          <template v-for="(item, index) in filteredNavigation" :key="`nav-item-${index}`">
            <SidebarNavGroup 
              v-if="item.children && item.children.length > 0" 
              :item="item"
            />
            <SidebarNavLink 
              v-else 
              :item="item" 
            />
          </template>
        </template>
        
        <template v-else-if="menuStore.isLoading">
          <div class="p-2 text-sm text-muted-foreground">
            <div class="flex items-center gap-2">
              <div class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></div>
              Loading navigation...
            </div>
          </div>
        </template>
        
        <template v-else-if="!authStore.isLoggedIn">
          <div class="p-2 text-sm text-muted-foreground">
            Please login to view navigation
          </div>
        </template>
        
        <template v-else-if="!authStore.user">
          <div class="p-2 text-sm text-muted-foreground">
            Loading user data...
          </div>
        </template>
        
        <template v-else>
          <div class="p-2 text-sm text-muted-foreground">
            <div>No navigation items found</div>
            <div class="text-xs mt-1 opacity-70">
              Debug: Loaded={{ menuStore.isLoaded }}, Loading={{ menuStore.isLoading }}
            </div>
          </div>
        </template>
      </SidebarGroup>
    </SidebarContent>
    <SidebarRail />
  </Sidebar>
</template>

<style scoped>
/* Custom styling untuk sidebar */
.sidebar-custom {
  /* Transition untuk smooth animation */
  transition: width 0.2s ease-in-out;
}

/* Ketika sidebar dalam mode collapsed (icon only) */
.sidebar-custom[data-state="collapsed"] {
  width: var(--sidebar-width-icon);
}

/* Ketika sidebar dalam mode expanded */
.sidebar-custom[data-state="expanded"] {
  width: var(--sidebar-width);
}

/* Responsif untuk mobile */
@media (max-width: 768px) {
  .sidebar-custom[data-state="open"] {
    width: var(--sidebar-width-mobile);
  }
}
</style>