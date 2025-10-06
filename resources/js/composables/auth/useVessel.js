import { ref, computed, watch } from 'vue'
import { defineStore } from 'pinia'
import { profileVesselApi } from '@/api/profile/vessel'
import { ElMessage } from 'element-plus'

/**
 * Pinia store untuk mengelola vessel selection dan state
 * Menyediakan reactive state dan methods untuk vessel operations
 */
export const useVessel = defineStore('vessel', () => {
  // State
  const currentVessel = ref(null)
  const availableVessels = ref([])
  const loading = ref(false)
  const initialized = ref(false)
  
  /**
   * Load daftar vessel yang tersedia dan current vessel
   * Dilengkapi dengan auth guard untuk mencegah API call sebelum login
   */
  const loadVessels = async () => {
    if (loading.value) return
    
    try {
      // Import stores di dalam fungsi untuk menghindari circular dependency
      const { useAuth } = await import('./useAuth')
      const { useUser } = await import('./useUser')
      const { isLoggedIn } = useAuth()
      const { user } = useUser()
      
      // Auth guard - cegah API call jika belum login
      if (!isLoggedIn.value || !user.value) {
        console.log('User belum authenticated, tidak dapat memuat vessel')
        return
      }
    } catch (importError) {
      console.warn('Failed to import auth stores:', importError)
      return
    }
    
    loading.value = true
    try {
      const response = await profileVesselApi.getVessels()
      
      if (response.data.success) {
        availableVessels.value = response.data.data
        
        // Set current vessel berdasarkan response
        const currentVesselId = response.data.current_vessel_id
        if (currentVesselId) {
          currentVessel.value = availableVessels.value.find(
            vessel => vessel.id === currentVesselId
          ) || null
        } else {
          currentVessel.value = null
        }
        
        initialized.value = true
      }
    } catch (error) {
      console.error('Error loading vessels:', error)
      ElMessage.error('Gagal memuat daftar vessel')
    } finally {
      loading.value = false
    }
  }
  
  /**
   * Update vessel aktif untuk user
   * @param {number|string|null} vesselId - ID vessel yang dipilih
   */
  const selectVessel = async (vesselId) => {
    if (loading.value) return
    
    loading.value = true
    try {
      const response = await profileVesselApi.updateVessel(vesselId)
      
      if (response.data.success) {
        // Update current vessel state
        if (vesselId) {
          currentVessel.value = availableVessels.value.find(
            vessel => vessel.id === vesselId
          ) || null
        } else {
          currentVessel.value = null
        }
        
        ElMessage.success(response.data.message || 'Vessel berhasil diupdate')
        
        // Emit event untuk komponen lain yang perlu refresh data
        window.dispatchEvent(new CustomEvent('vessel-changed', {
          detail: { vesselId, vessel: currentVessel.value }
        }))
        
        return true
      }
    } catch (error) {
      console.error('Error updating vessel:', error)
      ElMessage.error('Gagal mengupdate vessel')
      return false
    } finally {
      loading.value = false
    }
  }
  
  /**
   * Reset vessel selection (set ke null)
   */
  const resetVessel = () => {
    return selectVessel(null)
  }
  
  /**
   * Initialize vessel data jika belum diload
   * Hanya akan berjalan jika user sudah authenticated
   */
  const initializeVessels = async () => {
    try {
      // Dynamic import untuk menghindari circular dependency
      const { useAuth } = await import('./useAuth')
      const { useUser } = await import('./useUser')
      
      const { isLoggedIn } = useAuth()
      const { user } = useUser()
      
      // Cek authentication status terlebih dahulu
      if (!isLoggedIn.value || !user.value) {
        console.log('User belum authenticated, skip vessel initialization')
        return
      }
      
      if (!initialized.value && !loading.value) {
        await loadVessels()
      }
    } catch (error) {
      console.error('Failed to initialize vessel data after login:', error)
    }
  }
  
  // Computed properties
  const hasVessel = computed(() => currentVessel.value !== null)
  const currentVesselId = computed(() => currentVessel.value?.id || null)
  const currentVesselName = computed(() => currentVessel.value?.name || 'Semua Vessel')
  const vesselOptions = computed(() => {
    return [
      { id: null, name: 'Semua Vessel', code: 'ALL' },
      ...availableVessels.value
    ]
  })
  
  // Hapus auto-initialize untuk mencegah API call saat halaman login
  // initializeVessels() akan dipanggil manual setelah user login
  
  return {
    // State
    currentVessel: computed(() => currentVessel.value),
    availableVessels: computed(() => availableVessels.value),
    loading: computed(() => loading.value),
    initialized: computed(() => initialized.value),
    
    // Computed
    hasVessel,
    currentVesselId,
    currentVesselName,
    vesselOptions,
    
    // Methods
    loadVessels,
    selectVessel,
    resetVessel,
    initializeVessels
  }
})

export default useVessel