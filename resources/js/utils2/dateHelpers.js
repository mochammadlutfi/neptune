/**
 * Utility functions untuk formatting tanggal dan waktu
 */

/**
 * Format tanggal dan waktu lengkap
 * @param {String|Date} date - Tanggal yang akan diformat
 * @param {Object} options - Opsi formatting
 * @returns {String} - Tanggal yang sudah diformat
 */
export function formatDateTime(date, options = {}) {
  if (!date) return '-'
  
  try {
    const dateObj = new Date(date)
    if (isNaN(dateObj.getTime())) return '-'
    
    const defaultOptions = {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      timeZone: 'Asia/Jakarta'
    }
    
    const formatOptions = { ...defaultOptions, ...options }
    
    return dateObj.toLocaleString('id-ID', formatOptions)
  } catch (error) {
    console.error('Error formatting date:', error)
    return '-'
  }
}

/**
 * Format tanggal saja (tanpa waktu)
 * @param {String|Date} date - Tanggal yang akan diformat
 * @param {Object} options - Opsi formatting
 * @returns {String} - Tanggal yang sudah diformat
 */
export function formatDate(date, options = {}) {
  if (!date) return '-'
  
  try {
    const dateObj = new Date(date)
    if (isNaN(dateObj.getTime())) return '-'
    
    const defaultOptions = {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      timeZone: 'Asia/Jakarta'
    }
    
    const formatOptions = { ...defaultOptions, ...options }
    
    return dateObj.toLocaleDateString('id-ID', formatOptions)
  } catch (error) {
    console.error('Error formatting date:', error)
    return '-'
  }
}

/**
 * Format tanggal pendek (untuk chart/grafik)
 * @param {String|Date} date - Tanggal yang akan diformat
 * @returns {String} - Tanggal yang sudah diformat
 */
export function formatShortDate(date) {
  if (!date) return '-'
  
  try {
    const dateObj = new Date(date)
    if (isNaN(dateObj.getTime())) return '-'
    
    return dateObj.toLocaleDateString('id-ID', {
      month: 'short',
      day: 'numeric',
      timeZone: 'Asia/Jakarta'
    })
  } catch (error) {
    console.error('Error formatting short date:', error)
    return '-'
  }
}

/**
 * Format waktu saja
 * @param {String|Date} date - Tanggal yang akan diformat
 * @param {Object} options - Opsi formatting
 * @returns {String} - Waktu yang sudah diformat
 */
export function formatTime(date, options = {}) {
  if (!date) return '-'
  
  try {
    const dateObj = new Date(date)
    if (isNaN(dateObj.getTime())) return '-'
    
    const defaultOptions = {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      timeZone: 'Asia/Jakarta'
    }
    
    const formatOptions = { ...defaultOptions, ...options }
    
    return dateObj.toLocaleTimeString('id-ID', formatOptions)
  } catch (error) {
    console.error('Error formatting time:', error)
    return '-'
  }
}

/**
 * Format relative time (berapa lama yang lalu)
 * @param {String|Date} date - Tanggal yang akan diformat
 * @returns {String} - Waktu relatif
 */
export function formatTimeAgo(date) {
  if (!date) return '-'
  
  try {
    const dateObj = new Date(date)
    if (isNaN(dateObj.getTime())) return '-'
    
    const now = new Date()
    const diffInSeconds = Math.floor((now - dateObj) / 1000)
    
    // Kurang dari 1 menit
    if (diffInSeconds < 60) {
      return 'Baru saja'
    }
    
    // Kurang dari 1 jam
    if (diffInSeconds < 3600) {
      const minutes = Math.floor(diffInSeconds / 60)
      return `${minutes} menit yang lalu`
    }
    
    // Kurang dari 1 hari
    if (diffInSeconds < 86400) {
      const hours = Math.floor(diffInSeconds / 3600)
      return `${hours} jam yang lalu`
    }
    
    // Kurang dari 1 minggu
    if (diffInSeconds < 604800) {
      const days = Math.floor(diffInSeconds / 86400)
      return `${days} hari yang lalu`
    }
    
    // Kurang dari 1 bulan
    if (diffInSeconds < 2592000) {
      const weeks = Math.floor(diffInSeconds / 604800)
      return `${weeks} minggu yang lalu`
    }
    
    // Kurang dari 1 tahun
    if (diffInSeconds < 31536000) {
      const months = Math.floor(diffInSeconds / 2592000)
      return `${months} bulan yang lalu`
    }
    
    // Lebih dari 1 tahun
    const years = Math.floor(diffInSeconds / 31536000)
    return `${years} tahun yang lalu`
    
  } catch (error) {
    console.error('Error formatting time ago:', error)
    return '-'
  }
}

/**
 * Format tanggal untuk input date HTML
 * @param {String|Date} date - Tanggal yang akan diformat
 * @returns {String} - Tanggal dalam format YYYY-MM-DD
 */
export function formatDateForInput(date) {
  if (!date) return ''
  
  try {
    const dateObj = new Date(date)
    if (isNaN(dateObj.getTime())) return ''
    
    const year = dateObj.getFullYear()
    const month = String(dateObj.getMonth() + 1).padStart(2, '0')
    const day = String(dateObj.getDate()).padStart(2, '0')
    
    return `${year}-${month}-${day}`
  } catch (error) {
    console.error('Error formatting date for input:', error)
    return ''
  }
}

/**
 * Format tanggal dan waktu untuk input datetime-local HTML
 * @param {String|Date} date - Tanggal yang akan diformat
 * @returns {String} - Tanggal dalam format YYYY-MM-DDTHH:mm
 */
export function formatDateTimeForInput(date) {
  if (!date) return ''
  
  try {
    const dateObj = new Date(date)
    if (isNaN(dateObj.getTime())) return ''
    
    const year = dateObj.getFullYear()
    const month = String(dateObj.getMonth() + 1).padStart(2, '0')
    const day = String(dateObj.getDate()).padStart(2, '0')
    const hours = String(dateObj.getHours()).padStart(2, '0')
    const minutes = String(dateObj.getMinutes()).padStart(2, '0')
    
    return `${year}-${month}-${day}T${hours}:${minutes}`
  } catch (error) {
    console.error('Error formatting datetime for input:', error)
    return ''
  }
}

/**
 * Cek apakah tanggal valid
 * @param {String|Date} date - Tanggal yang akan dicek
 * @returns {Boolean} - True jika valid
 */
export function isValidDate(date) {
  if (!date) return false
  
  try {
    const dateObj = new Date(date)
    return !isNaN(dateObj.getTime())
  } catch (error) {
    return false
  }
}

/**
 * Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
 * @returns {String} - Tanggal hari ini
 */
export function getTodayDate() {
  return formatDateForInput(new Date())
}

/**
 * Mendapatkan tanggal kemarin dalam format YYYY-MM-DD
 * @returns {String} - Tanggal kemarin
 */
export function getYesterdayDate() {
  const yesterday = new Date()
  yesterday.setDate(yesterday.getDate() - 1)
  return formatDateForInput(yesterday)
}

/**
 * Mendapatkan tanggal minggu lalu dalam format YYYY-MM-DD
 * @returns {String} - Tanggal minggu lalu
 */
export function getLastWeekDate() {
  const lastWeek = new Date()
  lastWeek.setDate(lastWeek.getDate() - 7)
  return formatDateForInput(lastWeek)
}

/**
 * Mendapatkan tanggal bulan lalu dalam format YYYY-MM-DD
 * @returns {String} - Tanggal bulan lalu
 */
export function getLastMonthDate() {
  const lastMonth = new Date()
  lastMonth.setMonth(lastMonth.getMonth() - 1)
  return formatDateForInput(lastMonth)
}

/**
 * Mendapatkan range tanggal untuk periode tertentu
 * @param {String} period - Periode (today, yesterday, week, month, year)
 * @returns {Object} - Object dengan from dan to date
 */
export function getDateRange(period) {
  const today = new Date()
  let from, to
  
  switch (period) {
    case 'today':
      from = to = formatDateForInput(today)
      break
      
    case 'yesterday':
      const yesterday = new Date(today)
      yesterday.setDate(yesterday.getDate() - 1)
      from = to = formatDateForInput(yesterday)
      break
      
    case 'week':
      const weekAgo = new Date(today)
      weekAgo.setDate(weekAgo.getDate() - 7)
      from = formatDateForInput(weekAgo)
      to = formatDateForInput(today)
      break
      
    case 'month':
      const monthAgo = new Date(today)
      monthAgo.setMonth(monthAgo.getMonth() - 1)
      from = formatDateForInput(monthAgo)
      to = formatDateForInput(today)
      break
      
    case 'year':
      const yearAgo = new Date(today)
      yearAgo.setFullYear(yearAgo.getFullYear() - 1)
      from = formatDateForInput(yearAgo)
      to = formatDateForInput(today)
      break
      
    default:
      from = to = formatDateForInput(today)
  }
  
  return { from, to }
}