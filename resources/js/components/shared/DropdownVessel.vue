<template>
  <div class="w-full">
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <button
          class="flex h-9 w-full items-center justify-between whitespace-nowrap rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1"
          :disabled="loading"
        >
          <span v-if="userCurrentVessel" class="flex items-center gap-2">
            <span class="vessel-name">{{ userCurrentVessel.name }}</span>
            <span v-if="userCurrentVessel.code && userCurrentVessel.code !== 'ALL'" class="vessel-code text-muted-foreground text-xs">({{ userCurrentVessel.code }})</span>
          </span>
          <span v-else class="text-muted-foreground">Pilih Vessel</span>
          <ChevronDownIcon class="h-4 w-4 opacity-50" />
        </button>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="w-[250px]">
        <DropdownMenuItem
          v-for="vessel in userVessels"
          :key="vessel.id || 'all'"
          @click="handleVesselChange(vessel.id)"
          class="cursor-pointer"
        >
          <div class="flex items-center justify-between w-full">
            <span class="vessel-name">{{ vessel.name }}</span>
            <span v-if="vessel.code && vessel.code !== 'ALL'" class="vessel-code text-muted-foreground text-xs">{{ vessel.code }}</span>
          </div>
        </DropdownMenuItem>
        <DropdownMenuItem v-if="loading" disabled>
          <div class="flex items-center gap-2">
            <div class="h-4 w-4 animate-spin rounded-full border-2 border-primary border-t-transparent"></div>
            <span>Loading...</span>
          </div>
        </DropdownMenuItem>
        <DropdownMenuItem v-if="!loading && userVessels.length === 0" disabled>
          <span class="text-muted-foreground">Tidak ada vessel tersedia</span>
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>
</template>

<script setup>
import { computed, watch, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useUser } from '@/composables/auth'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { ChevronDownIcon } from 'lucide-vue-next'

// Props
const props = defineProps({
  /**
   * Ukuran dropdown
   */
  size: {
    type: String,
    default: 'default',
    validator: (value) => ['large', 'default', 'small'].includes(value)
  },
  
  /**
   * Apakah menampilkan loading state
   */
  showLoading: {
    type: Boolean,
    default: true
  }
})


// Emits
const emit = defineEmits([
  /**
   * Event ketika vessel berubah
   * @param {number|null} vesselId - ID vessel yang dipilih
   * @param {Object|null} vessel - Data vessel yang dipilih
   */
  'change',
  
  /**
   * Event ketika vessel berhasil diupdate
   * @param {number|null} vesselId - ID vessel yang dipilih
   */
  'updated'
])

const userStore = useUser()

// Ambil refs reaktif dari store untuk menghindari auto-unwrapping
const { userCurrentVessel, userVessels, isLoading } = storeToRefs(userStore)
// Methods tetap diambil langsung dari store
const { updateVessel } = userStore

// Computed
const loading = computed(() => {
  return props.showLoading && isLoading.value
})



// Methods
const handleVesselChange = async (vesselId) => {
  try {
    // Emit change event immediately untuk UI responsiveness
    const selectedVessel = userVessels.value.find(v => v.id === vesselId) || null
    emit('change', vesselId, selectedVessel)
    
    // Update vessel via API
    const success = await updateVessel(vesselId)
    
    if (success) {
      emit('updated', vesselId)
      // Reload browser setelah vessel berhasil diupdate
      window.location.reload()
      // console.log('Vessel berhasil diupdate:', userCurrentVessel.value)
    } else {
      console.error('Gagal update vessel')
    }
  } catch (error) {
    console.error('Error dalam handleVesselChange:', error)
  }
}

// Watch untuk perubahan vessel dari luar komponen
watch(
  userCurrentVessel,
  (newVessel, oldVessel) => {
    // console.log('Watch userCurrentVessel triggered:');
    // console.log('- Old vessel:', oldVessel);
    // console.log('- New vessel:', newVessel);
    emit('change', newVessel?.id || null, newVessel)
  },
  { deep: true, immediate: false }
)

// Lifecycle
onMounted(() => {
  // console.log('DropdownVessel mounted');
  // console.log('Initial userCurrentVessel:', userCurrentVessel.value);
  // console.log('Initial userVessels:', userVessels.value);
})

</script>