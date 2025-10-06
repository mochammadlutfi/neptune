import axios from 'axios';

/**
 * Vessel API endpoints untuk profile/user operations
 * Mengelola vessel selection dan user vessel preferences
 */
export const profileVesselApi = {
  /**
   * Mendapatkan daftar vessel yang dapat diakses user
   * @returns {Promise} Response daftar vessels dan current vessel
   */
  getVessels() {
    return axios.get('/master/vessels')
  },

  /**
   * Update vessel aktif untuk user yang sedang login
   * @param {number|string|null} vesselId - ID vessel yang dipilih (null untuk reset)
   * @returns {Promise} Response update vessel
   */
  updateVessel(vesselId) {
    return axios.post('/profile/vessel', {
      vessel_id: vesselId
    })
  },

  /**
   * Get current user vessel information
   * Mengambil informasi vessel dari profile user
   * @returns {Promise} Response current vessel info
   */
  getCurrentVessel() {
    return axios.get('/profile')
      .then(response => {
        return {
          ...response,
          data: {
            vessel_id: response.data.vessel_id,
            vessel: response.data.vessel
          }
        }
      })
  }
}

export default profileVesselApi