import axios from 'axios';

/**
 * Wells API endpoints
 * Mengelola operasi CRUD untuk data wells
 */
export const wellsApi = {
  /**
   * Mendapatkan daftar wells dengan filter dan pagination
   * @param {Object} params - Parameter filter dan pagination
   * @returns {Promise} Response data wells
   */
  getAll(params = {}) {
    return axios.get('/master/wells', { params })
  },

  /**
   * Mendapatkan statistik wells
   * @returns {Promise} Response statistik wells
   */
  getStats() {
    return axios.get('/master/wells/stats')
  },

  /**
   * Mendapatkan detail well berdasarkan ID
   * @param {number|string} id - ID well
   * @returns {Promise} Response detail well
   */
  getById(id) {
    return axios.get(`/master/wells/${id}`)
  },

  /**
   * Membuat well baru
   * @param {Object} data - Data well yang akan dibuat
   * @returns {Promise} Response well yang dibuat
   */
  create(data) {
    return axios.post('/master/wells', data)
  },

  /**
   * Mengupdate well berdasarkan ID
   * @param {number|string} id - ID well
   * @param {Object} data - Data well yang akan diupdate
   * @returns {Promise} Response well yang diupdate
   */
  update(id, data) {
    return axios.put(`/master/wells/${id}`, data)
  },

  /**
   * Mengupdate status well
   * @param {number|string} id - ID well
   * @param {string} status - Status baru well
   * @returns {Promise} Response update status
   */
  updateStatus(id, status) {
    return axios.patch(`/master/wells/${id}/status`, { status })
  },

  /**
   * Menghapus well berdasarkan ID
   * @param {number|string} id - ID well
   * @returns {Promise} Response penghapusan
   */
  delete(id) {
    return axios.delete(`/master/wells/${id}`)
  },

  /**
   * Export data wells ke Excel
   * @param {Object} params - Parameter filter untuk export
   * @returns {Promise} Response file Excel
   */
  export(params = {}) {
    return axios.get('/master/wells/export', {
      params,
      responseType: 'blob'
    })
  }
}

export default wellsApi