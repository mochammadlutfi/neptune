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
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <!-- Header -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                <i class="fas fa-history text-blue-600"></i>
              </div>
              <div class="ml-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Detail Activity Log
                </h3>
                <p class="text-sm text-gray-500">
                  Informasi lengkap tentang aktivitas yang dipilih
                </p>
              </div>
            </div>
            <button
              @click="$emit('close')"
              class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <span class="sr-only">Close</span>
              <i class="fas fa-times h-6 w-6"></i>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6" v-if="!loading && activity">
          <!-- Activity Overview -->
          <div class="mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Basic Info -->
                <div>
                  <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Dasar</h4>
                  <dl class="space-y-2">
                    <div class="flex justify-between">
                      <dt class="text-sm text-gray-500">ID:</dt>
                      <dd class="text-sm font-medium text-gray-900">{{ activity.id }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-sm text-gray-500">Tipe Event:</dt>
                      <dd>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getEventTypeClass(activity.event)">
                          <i :class="getEventIcon(activity.event)" class="mr-1"></i>
                          {{ getEventLabel(activity.event) }}
                        </span>
                      </dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-sm text-gray-500">Waktu:</dt>
                      <dd class="text-sm font-medium text-gray-900">{{ formatDateTime(activity.created_at) }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-sm text-gray-500">Deskripsi:</dt>
                      <dd class="text-sm font-medium text-gray-900">{{ activity.description || '-' }}</dd>
                    </div>
                  </dl>
                </div>

                <!-- Subject Info -->
                <div>
                  <h4 class="text-sm font-medium text-gray-900 mb-3">Objek yang Diubah</h4>
                  <dl class="space-y-2">
                    <div class="flex justify-between">
                      <dt class="text-sm text-gray-500">Tipe Model:</dt>
                      <dd class="text-sm font-medium text-gray-900">{{ activity.subject_type || '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-sm text-gray-500">ID Model:</dt>
                      <dd class="text-sm font-medium text-gray-900">{{ activity.subject_id || '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                      <dt class="text-sm text-gray-500">Log Name:</dt>
                      <dd class="text-sm font-medium text-gray-900">{{ activity.log_name || '-' }}</dd>
                    </div>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- User Info -->
          <div class="mb-6" v-if="activity.causer">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi User</h4>
            <div class="bg-blue-50 rounded-lg p-4">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center">
                    <i class="fas fa-user text-blue-600"></i>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ activity.causer.name || 'Unknown User' }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ activity.causer.email || '-' }}
                  </div>
                  <div class="text-xs text-gray-400">
                    ID: {{ activity.causer.id }} | Type: {{ activity.causer_type }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Properties Changes -->
          <div v-if="hasPropertyChanges">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Perubahan Data</h4>
            <ActivityLogProperties :activity="activity" />
          </div>

          <!-- Raw Data (for debugging) -->
          <div class="mt-6" v-if="showRawData">
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-sm font-medium text-gray-900">Raw Data</h4>
              <button
                @click="showRawData = false"
                class="text-xs text-gray-500 hover:text-gray-700"
              >
                Hide
              </button>
            </div>
            <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
              <pre class="text-xs text-green-400">{{ JSON.stringify(activity, null, 2) }}</pre>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="bg-white px-4 pt-5 pb-4 sm:p-6">
          <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-sm text-gray-600">Memuat detail aktivitas...</span>
          </div>
        </div>

        <!-- Error State -->
        <div v-if="error" class="bg-white px-4 pt-5 pb-4 sm:p-6">
          <div class="text-center py-12">
            <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Gagal Memuat Data</h3>
            <p class="text-sm text-gray-600 mb-4">{{ error }}</p>
            <button
              @click="fetchActivityDetail"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <i class="fas fa-redo mr-2"></i>
              Coba Lagi
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="showRawData = !showRawData"
            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            <i class="fas fa-code mr-2"></i>
            {{ showRawData ? 'Hide' : 'Show' }} Raw Data
          </button>
          <button
            @click="$emit('close')"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import ActivityLogProperties from './ActivityLogProperties.vue'
import { formatDateTime } from '@/utils/dateHelpers'
import axios from 'axios'

// Props
const props = defineProps({
  activityId: {
    type: [String, Number],
    required: true
  }
})

// Emits
defineEmits(['close'])

// Reactive data
const loading = ref(false)
const error = ref(null)
const activity = ref(null)
const showRawData = ref(false)

// Computed
const hasPropertyChanges = computed(() => {
  return activity.value && (activity.value.properties?.old || activity.value.properties?.attributes)
})

// Methods
const fetchActivityDetail = async () => {
  if (!props.activityId) return
  
  loading.value = true
  error.value = null
  
  try {
    const response = await axios.get(`/api/activity-log/${props.activityId}`)
    activity.value = response.data.data
  } catch (err) {
    console.error('Error fetching activity detail:', err)
    error.value = err.response?.data?.message || 'Terjadi kesalahan saat memuat data'
  } finally {
    loading.value = false
  }
}

const getEventTypeClass = (event) => {
  const classes = {
    created: 'bg-green-100 text-green-800',
    updated: 'bg-blue-100 text-blue-800',
    deleted: 'bg-red-100 text-red-800',
    restored: 'bg-yellow-100 text-yellow-800'
  }
  return classes[event] || 'bg-gray-100 text-gray-800'
}

const getEventIcon = (event) => {
  const icons = {
    created: 'fas fa-plus-circle',
    updated: 'fas fa-edit',
    deleted: 'fas fa-trash',
    restored: 'fas fa-undo'
  }
  return icons[event] || 'fas fa-circle'
}

const getEventLabel = (event) => {
  const labels = {
    created: 'Dibuat',
    updated: 'Diperbarui',
    deleted: 'Dihapus',
    restored: 'Dipulihkan'
  }
  return labels[event] || event
}

// Watchers
watch(() => props.activityId, () => {
  if (props.activityId) {
    fetchActivityDetail()
  }
}, { immediate: true })

// Lifecycle
onMounted(() => {
  // Handle escape key
  const handleEscape = (e) => {
    if (e.key === 'Escape') {
      $emit('close')
    }
  }
  
  document.addEventListener('keydown', handleEscape)
  
  // Cleanup
  onUnmounted(() => {
    document.removeEventListener('keydown', handleEscape)
  })
})
</script>

<style scoped>
/* Custom scrollbar for raw data */
pre::-webkit-scrollbar {
  height: 8px;
}

pre::-webkit-scrollbar-track {
  background: #374151;
}

pre::-webkit-scrollbar-thumb {
  background: #6b7280;
  border-radius: 4px;
}

pre::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>