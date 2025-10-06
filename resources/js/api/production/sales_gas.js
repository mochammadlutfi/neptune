import axios from 'axios';

/**
 * Wells API endpoints
 * Mengelola operasi CRUD untuk data wells
 */
export const salesGasApi = {
  /**
   * Mendapatkan daftar wells dengan filter dan pagination
   * @param {Object} params - Parameter filter dan pagination
   * @returns {Promise} Response data wells
   */
  getAll(params = {}) {
    return axios.get('/production/sales-gas', { params })
  },

  /**
   * Mendapatkan statistik wells
   * @returns {Promise} Response statistik wells
   */
  getStats() {
    return axios.get('/production/sales-gas/stats')
  },

  /**
   * Mendapatkan detail well berdasarkan ID
   * @param {number|string} id - ID well
   * @returns {Promise} Response detail well
   */
  getById(id) {
    return axios.get(`/production/sales-gas/${id}`)
  },

  /**
   * Membuat well baru
   * @param {Object} data - Data well yang akan dibuat
   * @returns {Promise} Response well yang dibuat
   */
  create(data) {
    return axios.post('/production/sales-gas/store', data)
  },

  /**
   * Mengupdate well berdasarkan ID
   * @param {number|string} id - ID well
   * @param {Object} data - Data well yang akan diupdate
   * @returns {Promise} Response well yang diupdate
   */
  update(id, data) {
    return axios.put(`/production/sales-gas/${id}/update`, data)
  },

  /**
   * Mengupdate status well
   * @param {number|string} id - ID well
   * @param {string} status - Status baru well
   * @returns {Promise} Response update status
   */
  updateStatus(id, status) {
    return axios.patch(`/production/sales-gas/${id}/status`, { status })
  },

  /**
   * Menghapus well berdasarkan ID
   * @param {number|string} id - ID well
   * @returns {Promise} Response penghapusan
   */
  delete(id) {
    return axios.delete(`/production/sales-gas/${id}/delete`)
  },

  /**
   * Export data wells ke Excel
   * @param {Object} params - Parameter filter untuk export
   * @returns {Promise} Response file Excel
   */
  export(params = {}) {
    return axios.get('/production/sales-gas/export', {
      params,
      responseType: 'blob'
    })
  },

  /**
   * Import data wells from Excel
   * @param {FormData} data - Form data containing Excel file
   * @returns {Promise} Response import result
   */
  import(data) {
    return axios.post('/production/sales-gas/import', data, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  }
}

export default salesGasApi