import axios from 'axios';

/**
 * Equipment API endpoints
 * Mengelola operasi CRUD untuk data equipment
 */
export const equipmentApi = {
  /**
   * Mendapatkan daftar equipment dengan filter dan pagination
   * @param {Object} params - Parameter filter dan pagination
   * @returns {Promise} Response data equipment
   */
  getAll(params = {}) {
    return axios.get('/master/equipment', { params })
  },

  /**
   * Mendapatkan statistik equipment
   * @returns {Promise} Response statistik equipment
   */
  getStats() {
    return axios.get('/master/equipment/stats')
  },

  /**
   * Mendapatkan detail well berdasarkan ID
   * @param {number|string} id - ID well
   * @returns {Promise} Response detail well
   */
  getById(id) {
    return axios.get(`/master/equipment/${id}`)
  },

  /**
   * Membuat well baru
   * @param {Object} data - Data well yang akan dibuat
   * @returns {Promise} Response well yang dibuat
   */
  create(data) {
    return axios.post('/master/equipment', data)
  },

  /**
   * Mengupdate well berdasarkan ID
   * @param {number|string} id - ID well
   * @param {Object} data - Data well yang akan diupdate
   * @returns {Promise} Response well yang diupdate
   */
  update(id, data) {
    return axios.put(`/master/equipment/${id}`, data)
  },

  /**
   * Mengupdate status well
   * @param {number|string} id - ID well
   * @param {string} status - Status baru well
   * @returns {Promise} Response update status
   */
  updateStatus(id, status) {
    return axios.patch(`/master/equipment/${id}/status`, { status })
  },

  /**
   * Menghapus well berdasarkan ID
   * @param {number|string} id - ID well
   * @returns {Promise} Response penghapusan
   */
  delete(id) {
    return axios.delete(`/master/equipment/${id}`)
  },

  /**
   * Export data equipment ke Excel
   * @param {Object} params - Parameter filter untuk export
   * @returns {Promise} Response file Excel
   */
  export(params = {}) {
    return axios.get('/master/equipment/export', {
      params,
      responseType: 'blob'
    })
  }
}

export default equipmentApi