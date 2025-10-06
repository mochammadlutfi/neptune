import axios from 'axios'

/**
 * API client untuk operasi user
 * Menyediakan fungsi-fungsi untuk komunikasi dengan backend
 */
export const userApi = {
  /**
   * Login user
   * @param {Object} credentials - Email dan password
   * @returns {Promise} Response dari API
   */
  async login(credentials) {
    return await axios.post('/login', credentials)
  },

  /**
   * Logout user
   * @returns {Promise} Response dari API
   */
  async logout() {
    return await axios.post('/logout')
  },

  /**
   * Forgot password
   * @param {Object} data - Email untuk reset password
   * @returns {Promise} Response dari API
   */
  async forgotPassword(data) {
    return await axios.post('/forgot-password', data)
  },

  /**
   * Get user profile
   * @returns {Promise} Response dari API
   */
  async getProfile() {
    return await axios.get('/profile')
  },

  /**
   * Update user profile
   * @param {Object} profileData - Data profile yang akan diupdate
   * @returns {Promise} Response dari API
   */
  async updateProfile(profileData) {
    return await axios.put('/profile', profileData)
  },

  /**
   * Update user vessel
   * @param {number} vesselId - ID vessel yang dipilih
   * @returns {Promise} Response dari API
   */
  async updateVessel(vesselId) {
    return await axios.post('/profile/vessel', { vessel_id: vesselId })
  },

  /**
   * Change password
   * @param {Object} passwordData - Current password dan new password
   * @returns {Promise} Response dari API
   */
  async changePassword(passwordData) {
    return await axios.put('/profile/password', passwordData)
  },

  /**
   * Upload user avatar
   * @param {FormData} formData - Form data dengan file avatar
   * @returns {Promise} Response dari API
   */
  async uploadAvatar(formData) {
    return await axios.post('/profile/avatar', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  },

  /**
   * Delete user avatar
   * @returns {Promise} Response dari API
   */
  async deleteAvatar() {
    return await axios.delete('/profile/avatar')
  },

  /**
   * Get user notifications
   * @returns {Promise} Response dari API
   */
  async getNotifications() {
    return await axios.get('/notifications')
  },

  /**
   * Mark notification as read
   * @param {number} notificationId - ID notifikasi
   * @returns {Promise} Response dari API
   */
  async markNotificationAsRead(notificationId) {
    return await axios.put(`/notifications/${notificationId}/read`)
  },

  /**
   * Mark all notifications as read
   * @returns {Promise} Response dari API
   */
  async markAllNotificationsAsRead() {
    return await axios.put('/notifications/read-all')
  }
}

export default userApi