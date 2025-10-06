import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { userApi } from '@/api/user'
import { updateAbility, ability } from '@/utils/ability'
/**
 * Pinia store untuk mengelola user profile
 * Menangani user info, update profile, change password, dan operasi profile lainnya
 */
export const useUser = defineStore('user', () => {
  // State
  const user = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Computed properties
  const userData = computed(() => user.value)
  const userInitials = computed(() => {
    if (!user.value?.name) return ''
    return user.value.name.split(' ').map(n => n[0]).join('')
  })
  const userName = computed(() => user.value?.name || '')
  const userEmail = computed(() => user.value?.email || '')
  const userPhone = computed(() => user.value?.phone || '')
  const userImage = computed(() => user.value?.image_url || '')
  const userRole = computed(() => user.value?.role || '')
  const userVesselId = computed(() => user.value?.vessel_id || null)
  const userCurrentVessel = computed(() => user.value?.vessel || null)
  const userVessels = computed(() => user.value?.vessels || [])
  const userPermissions = computed(() => user.value?.permissions || [])

  /**
   * Update data user
   * @param {Object} data - Data user dari API
   */
  const updateUser = (data) => {
    user.value = {
      id: data.id,
      name: data.name,
      email: data.email,
      phone: data.phone,
      image: data.image,
      image_url: data.image_url,
      role: data.roles?.[0]?.name || null,
      vessel_id: data.vessel_id || null,
      vessel : data.vessel || null,
      vessels : data.vessels || [],
      permissions : data.permissions || [],
    }
  }

  /**
   * Mengambil data user dari API
   */
  const getUser = async () => {
    if (isLoading.value) return
    
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.getProfile()
      updateUser(response.data)
      // Sync CASL ability with fetched permissions
      updateAbility(ability, response.data.permissions || [])
      
      return response.data
    } catch (err) {
      error.value = err.message || 'Gagal mengambil data user'
      console.error('Failed to fetch user data:', err)
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Update profile user
   * @param {Object} profileData - Data profile yang akan diupdate
   */
  const updateProfile = async (profileData) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.updateProfile(profileData)
      updateUser(response.data)
      // Sync CASL ability with updated permissions
      updateAbility(ability, response.data.permissions || [])
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Update profile gagal'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Change password user
   * @param {Object} passwordData - Data password lama dan baru
   */
  const changePassword = async (passwordData) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.changePassword(passwordData)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Gagal mengubah password'
      
      const sanitizedError = {
        message: err.response?.data?.message || 'Password change failed',
        validation: err.response?.data?.errors || null,
        status: err.response?.status || 500
      }
      
      throw sanitizedError
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Upload avatar user
   * @param {File} file - File avatar yang akan diupload
   */
  const uploadAvatar = async (file) => {
    try {
      isLoading.value = true
      error.value = null
      
      const formData = new FormData()
      formData.append('avatar', file)
      
      const response = await userApi.uploadAvatar(formData)
      
      // Update user data dengan avatar baru
      if (user.value) {
        user.value.image = response.data.image
        user.value.image_url = response.data.image_url
      }
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Gagal upload avatar'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Delete avatar user
   */
  const deleteAvatar = async () => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.deleteAvatar()
      
      // Update user data tanpa avatar
      if (user.value) {
        user.value.image = null
        user.value.image_url = null
      }
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Gagal hapus avatar'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Update vessel user
   * @param {number} vesselId - ID vessel yang akan diset
   */
  const updateVessel = async (vesselId) => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.updateVessel(vesselId)
      
      // Update user data dengan vessel baru
      if (user.value) {
        user.value.vessel_id = response.data.data.vessel_id
        user.value.vessel = response.data.data.updateVessel
      }
      
      return true // Return boolean untuk success indicator
    } catch (err) {
      error.value = err.response?.data?.message || 'Gagal update vessel'
      console.error('Update vessel error:', err)
      return false // Return false jika gagal
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Get notifications user
   */
  const getNotifications = async () => {
    try {
      isLoading.value = true
      error.value = null
      
      const response = await userApi.getNotifications()
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Gagal mengambil notifikasi'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Mark notification as read
   * @param {number} notificationId - ID notifikasi
   */
  const markNotificationAsRead = async (notificationId) => {
    try {
      const response = await userApi.markNotificationAsRead(notificationId)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Gagal mark notifikasi'
      throw err
    }
  }

  /**
   * Reset user state ke kondisi awal
   */
  const resetUserState = () => {
    user.value = null
    error.value = null
    isLoading.value = false
  }

  /**
   * Clear user data (biasanya saat logout)
   */
  const clearUser = () => {
    user.value = null
    error.value = null
  }

  return {
    // State
    user: userData,
    isLoading,
    error,
    
    // Computed
    userInitials,
    userName,
    userEmail,
    userPhone,
    userImage,
    userRole,
    userVesselId,
    userCurrentVessel,
    userVessels,
    userPermissions,
    
    // Methods
    updateUser,
    getUser,
    updateProfile,
    changePassword,
    uploadAvatar,
    deleteAvatar,
    updateVessel,
    getNotifications,
    markNotificationAsRead,
    resetUserState,
    clearUser,
  }
})

// Export default untuk backward compatibility
export default useUser