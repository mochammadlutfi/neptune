<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
        aria-hidden="true"
        @click="$emit('close')"
      ></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <!-- Header -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
          <div class="flex items-center">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
              <i class="fas fa-download text-green-600"></i>
            </div>
            <div class="ml-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Export Activity Log
              </h3>
              <p class="text-sm text-gray-500">
                Pilih format dan data yang ingin diekspor
              </p>
            </div>
          </div>
        </div>

        <!-- Content -->
        <form @submit.prevent="handleExport">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
            <!-- Export Format -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Format Export
              </label>
              <div class="grid grid-cols-3 gap-3">
                <label 
                  v-for="format in exportFormats" 
                  :key="format.value"
                  class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                  :class="{
                    'border-blue-500 ring-2 ring-blue-500': exportOptions.format === format.value,
                    'border-gray-300': exportOptions.format !== format.value
                  }"
                >
                  <input
                    type="radio"
                    :value="format.value"
                    v-model="exportOptions.format"
                    class="sr-only"
                  >
                  <div class="flex flex-col items-center text-center">
                    <i :class="format.icon" class="text-2xl mb-2"></i>
                    <span class="block text-sm font-medium text-gray-900">{{ format.label }}</span>
                    <span class="block text-xs text-gray-500">{{ format.description }}</span>
                  </div>
                </label>
              </div>
            </div>

            <!-- Date Range -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Rentang Tanggal
              </label>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                  <input
                    type="date"
                    v-model="exportOptions.dateRange.start"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                </div>
                <div>
                  <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                  <input
                    type="date"
                    v-model="exportOptions.dateRange.end"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                </div>
              </div>
              <div class="mt-2">
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="preset in datePresets"
                    :key="preset.key"
                    type="button"
                    @click="applyDatePreset(preset.key)"
                    class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    {{ preset.label }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Columns Selection -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Kolom yang Diekspor
              </label>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input
                    type="checkbox"
                    :checked="allColumnsSelected"
                    @change="toggleAllColumns"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                </label>
                <div class="grid grid-cols-2 gap-2 mt-3">
                  <label 
                    v-for="column in availableColumns" 
                    :key="column.value"
                    class="flex items-center"
                  >
                    <input
                      type="checkbox"
                      :value="column.value"
                      v-model="exportOptions.columns"
                      class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <span class="ml-2 text-sm text-gray-700">{{ column.label }}</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Additional Options -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-3">
                Opsi Tambahan
              </label>
              <div class="space-y-3">
                <label class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="exportOptions.includeProperties"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700">Sertakan Detail Perubahan Properties</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="exportOptions.includeUserInfo"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700">Sertakan Informasi User</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="exportOptions.groupByDate"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700">Kelompokkan berdasarkan Tanggal</span>
                </label>
              </div>
            </div>

            <!-- Export Summary -->
            <div class="bg-blue-50 rounded-lg p-4">
              <h4 class="text-sm font-medium text-blue-900 mb-2">Ringkasan Export</h4>
              <ul class="text-sm text-blue-800 space-y-1">
                <li>Format: <strong>{{ getFormatLabel(exportOptions.format) }}</strong></li>
                <li>Periode: <strong>{{ getDateRangeLabel() }}</strong></li>
                <li>Kolom: <strong>{{ exportOptions.columns.length }} kolom dipilih</strong></li>
                <li v-if="exportOptions.includeProperties">✓ Termasuk detail perubahan</li>
                <li v-if="exportOptions.includeUserInfo">✓ Termasuk info user</li>
                <li v-if="exportOptions.groupByDate">✓ Dikelompokkan per tanggal</li>
              </ul>
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="!canExport"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i class="fas fa-download mr-2"></i>
              Export Data
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { getTodayDate, getYesterdayDate, getLastWeekDate, getLastMonthDate, formatDate } from '@/utils/dateHelpers'

// Emits
defineEmits(['close', 'export'])

// Reactive data
const exportOptions = reactive({
  format: 'xlsx',
  dateRange: {
    start: getLastMonthDate(),
    end: getTodayDate()
  },
  columns: ['id', 'description', 'event', 'subject_type', 'created_at', 'causer_name'],
  includeProperties: true,
  includeUserInfo: true,
  groupByDate: false
})

// Export formats
const exportFormats = [
  {
    value: 'xlsx',
    label: 'Excel',
    description: '.xlsx',
    icon: 'fas fa-file-excel',
    color: 'text-green-600'
  },
  {
    value: 'csv',
    label: 'CSV',
    description: '.csv',
    icon: 'fas fa-file-csv',
    color: 'text-blue-600'
  },
  {
    value: 'pdf',
    label: 'PDF',
    description: '.pdf',
    icon: 'fas fa-file-pdf',
    color: 'text-red-600'
  }
]

// Available columns
const availableColumns = [
  { value: 'id', label: 'ID' },
  { value: 'description', label: 'Deskripsi' },
  { value: 'event', label: 'Event' },
  { value: 'subject_type', label: 'Tipe Model' },
  { value: 'subject_id', label: 'ID Model' },
  { value: 'causer_name', label: 'Nama User' },
  { value: 'causer_email', label: 'Email User' },
  { value: 'log_name', label: 'Log Name' },
  { value: 'created_at', label: 'Tanggal Dibuat' },
  { value: 'properties', label: 'Properties' }
]

// Date presets
const datePresets = [
  { key: 'today', label: 'Hari Ini' },
  { key: 'yesterday', label: 'Kemarin' },
  { key: 'week', label: '7 Hari Terakhir' },
  { key: 'month', label: '30 Hari Terakhir' },
  { key: 'all', label: 'Semua Data' }
]

// Computed
const allColumnsSelected = computed(() => {
  return exportOptions.columns.length === availableColumns.length
})

const canExport = computed(() => {
  return exportOptions.format && exportOptions.columns.length > 0
})

// Methods
const toggleAllColumns = (event) => {
  if (event.target.checked) {
    exportOptions.columns = availableColumns.map(col => col.value)
  } else {
    exportOptions.columns = []
  }
}

const applyDatePreset = (preset) => {
  switch (preset) {
    case 'today':
      exportOptions.dateRange.start = getTodayDate()
      exportOptions.dateRange.end = getTodayDate()
      break
    case 'yesterday':
      exportOptions.dateRange.start = getYesterdayDate()
      exportOptions.dateRange.end = getYesterdayDate()
      break
    case 'week':
      exportOptions.dateRange.start = getLastWeekDate()
      exportOptions.dateRange.end = getTodayDate()
      break
    case 'month':
      exportOptions.dateRange.start = getLastMonthDate()
      exportOptions.dateRange.end = getTodayDate()
      break
    case 'all':
      exportOptions.dateRange.start = ''
      exportOptions.dateRange.end = ''
      break
  }
}

const getFormatLabel = (format) => {
  const formatObj = exportFormats.find(f => f.value === format)
  return formatObj ? formatObj.label : format.toUpperCase()
}

const getDateRangeLabel = () => {
  if (!exportOptions.dateRange.start && !exportOptions.dateRange.end) {
    return 'Semua data'
  }
  
  if (exportOptions.dateRange.start === exportOptions.dateRange.end) {
    return formatDate(exportOptions.dateRange.start)
  }
  
  return `${formatDate(exportOptions.dateRange.start)} - ${formatDate(exportOptions.dateRange.end)}`
}

const handleExport = () => {
  if (!canExport.value) return
  
  // Emit export event with options
  $emit('export', { ...exportOptions })
}
</script>

<style scoped>
/* Custom radio button styling */
input[type="radio"]:checked + div {
  @apply ring-2 ring-blue-500;
}
</style>