import axios from 'axios';

/**
 * Vessels API endpoints
 * Mengelola operasi CRUD untuk data vessels
 */
export const vesselsApi = {
  /**
   * Mendapatkan daftar vessels dengan filter dan pagination
   * @param {Object} params - Parameter filter dan pagination
   * @returns {Promise} Response data vessels
   */
  getAll(params = {}) {
    return axios.get('/master/vessels', { params })
  },

  /**
   * Mendapatkan statistik vessels
   * @returns {Promise} Response statistik vessels
   */
  getStats() {
    return axios.get('/master/vessels/stats')
  },

  /**
   * Mendapatkan detail well berdasarkan ID
   * @param {number|string} id - ID well
   * @returns {Promise} Response detail well
   */
  getById(id) {
    return axios.get(`/master/vessels/${id}`)
  },

  /**
   * Membuat well baru
   * @param {Object} data - Data well yang akan dibuat
   * @returns {Promise} Response well yang dibuat
   */
  create(data) {
    return axios.post('/master/vessels', data)
  },

  /**
   * Mengupdate well berdasarkan ID
   * @param {number|string} id - ID well
   * @param {Object} data - Data well yang akan diupdate
   * @returns {Promise} Response well yang diupdate
   */
  update(id, data) {
    return axios.put(`/master/vessels/${id}`, data)
  },

  /**
   * Mengupdate status well
   * @param {number|string} id - ID well
   * @param {string} status - Status baru well
   * @returns {Promise} Response update status
   */
  updateStatus(id, status) {
    return axios.patch(`/master/vessels/${id}/status`, { status })
  },

  /**
   * Menghapus well berdasarkan ID
   * @param {number|string} id - ID well
   * @returns {Promise} Response penghapusan
   */
  delete(id) {
    return axios.delete(`/master/vessels/${id}`)
  },

  /**
   * Export data vessels ke Excel
   * @param {Object} params - Parameter filter untuk export
   * @returns {Promise} Response file Excel
   */
  export(params = {}) {
    return axios.get('/master/vessels/export', {
      params,
      responseType: 'blob'
    })
  }
}

export default vesselsApi