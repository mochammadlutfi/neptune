<template>
  <div class="activity-log-container">
    <!-- Header dengan filter -->
    <div class="activity-log-header">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Activity Log</h2>
      
      <!-- Filter Section -->
      <div class="filter-section bg-white p-4 rounded-lg shadow-sm border mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Search Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Cari aktivitas..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @input="debounceSearch"
            />
          </div>
          
          <!-- Date From Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
            <input
              v-model="filters.date_from"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="fetchActivities"
            />
          </div>
          
          <!-- Date To Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
            <input
              v-model="filters.date_to"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="fetchActivities"
            />
          </div>
          
          <!-- Model Type Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Model</label>
            <select
              v-model="filters.subject_type"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @change="fetchActivities"
            >
              <option value="">Semua Tipe</option>
              <option value="App\Models\Partner">Partner</option>
              <option value="App\Models\Product\Product">Product</option>
              <option value="App\Models\Sales\SaleOrder">Sales Order</option>
            </select>
          </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-between items-center mt-4">
          <button
            @click="resetFilters"
            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors"
          >
            Reset Filter
          </button>
          
          <button
            @click="showStatistics = !showStatistics"
            class="px-4 py-2 text-blue-600 bg-blue-100 rounded-md hover:bg-blue-200 transition-colors"
          >
            {{ showStatistics ? 'Sembunyikan' : 'Tampilkan' }} Statistik
          </button>
        </div>
      </div>
      
      <!-- Statistics Section -->
      <div v-if="showStatistics" class="statistics-section mb-6">
        <ActivityLogStatistics :filters="filters" />
      </div>
    </div>
    
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
      <span class="ml-2 text-gray-600">Memuat data...</span>
    </div>
    
    <!-- Activity List -->
    <div v-else-if="activities.length > 0" class="activity-list">
      <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="activity-item" v-for="activity in activities" :key="activity.id">
          <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <!-- Activity Description -->
                <div class="flex items-center mb-2">
                  <div class="activity-icon mr-3">
                    <i :class="getActivityIcon(activity.description)" class="text-lg"></i>
                  </div>
                  <h3 class="text-sm font-medium text-gray-900">
                    {{ activity.description }}
                  </h3>
                </div>
                
                <!-- Activity Details -->
                <div class="text-sm text-gray-600 mb-2">
                  <span v-if="activity.causer">
                    oleh <strong>{{ activity.causer.name }}</strong>
                  </span>
                  <span v-if="activity.subject_type" class="ml-2">
                    pada <strong>{{ formatModelName(activity.subject_type) }}</strong>
                  </span>
                  <span v-if="activity.subject_id" class="ml-1">
                    ID: {{ activity.subject_id }}
                  </span>
                </div>
                
                <!-- Properties Changes -->
                <div v-if="activity.properties && Object.keys(activity.properties).length > 0" class="mt-2">
                  <button
                    @click="toggleProperties(activity.id)"
                    class="text-xs text-blue-600 hover:text-blue-800 transition-colors"
                  >
                    {{ expandedProperties.includes(activity.id) ? 'Sembunyikan' : 'Tampilkan' }} Detail Perubahan
                  </button>
                  
                  <div v-if="expandedProperties.includes(activity.id)" class="mt-2 p-3 bg-gray-50 rounded-md">
                    <ActivityLogProperties :properties="activity.properties" />
                  </div>
                </div>
              </div>
              
              <!-- Timestamp -->
              <div class="text-xs text-gray-500 ml-4">
                <div>{{ formatDateTime(activity.created_at) }}</div>
                <div class="mt-1">{{ formatTimeAgo(activity.created_at) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="mt-6">
        <ActivityLogPagination
          :pagination="pagination"
          @page-changed="changePage"
        />
      </div>
    </div>
    
    <!-- Empty State -->
    <div v-else class="empty-state text-center py-12">
      <div class="text-gray-400 text-6xl mb-4">
        <i class="fas fa-history"></i>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada activity log</h3>
      <p class="text-gray-600">Belum ada aktivitas yang tercatat dengan filter yang dipilih.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { debounce } from 'lodash'
import ActivityLogStatistics from './ActivityLogStatistics.vue'
import ActivityLogProperties from './ActivityLogProperties.vue'
import ActivityLogPagination from './ActivityLogPagination.vue'
import { useActivityLogStore } from '@/stores/activityLogStore'
import { formatDateTime, formatTimeAgo } from '@/utils/dateHelpers'

// Store
const activityLogStore = useActivityLogStore()

// Reactive data
const loading = ref(false)
const showStatistics = ref(false)
const expandedProperties = ref([])

const filters = reactive({
  search: '',
  date_from: '',
  date_to: '',
  subject_type: '',
  user_id: '',
  per_page: 20
})

// Computed
const activities = computed(() => activityLogStore.activities)
const pagination = computed(() => activityLogStore.pagination)

// Methods
const fetchActivities = async (page = 1) => {
  loading.value = true
  try {
    await activityLogStore.fetchActivities({ ...filters, page })
  } catch (error) {
    console.error('Error fetching activities:', error)
  } finally {
    loading.value = false
  }
}

const debounceSearch = debounce(() => {
  fetchActivities()
}, 500)

const resetFilters = () => {
  Object.keys(filters).forEach(key => {
    if (key !== 'per_page') {
      filters[key] = ''
    }
  })
  fetchActivities()
}

const changePage = (page) => {
  fetchActivities(page)
}

const toggleProperties = (activityId) => {
  const index = expandedProperties.value.indexOf(activityId)
  if (index > -1) {
    expandedProperties.value.splice(index, 1)
  } else {
    expandedProperties.value.push(activityId)
  }
}

const getActivityIcon = (description) => {
  if (description.includes('created') || description.includes('dibuat')) {
    return 'fas fa-plus-circle text-green-500'
  } else if (description.includes('updated') || description.includes('diupdate')) {
    return 'fas fa-edit text-blue-500'
  } else if (description.includes('deleted') || description.includes('dihapus')) {
    return 'fas fa-trash text-red-500'
  }
  return 'fas fa-info-circle text-gray-500'
}

const formatModelName = (modelType) => {
  const modelMap = {
    'App\\Models\\Partner': 'Partner',
    'App\\Models\\Product\\Product': 'Product',
    'App\\Models\\Sales\\SaleOrder': 'Sales Order'
  }
  return modelMap[modelType] || modelType.split('\\').pop()
}

// Lifecycle
onMounted(() => {
  fetchActivities()
})
</script>

<style scoped>
.activity-log-container {
  @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6;
}

.activity-icon {
  @apply w-8 h-8 flex items-center justify-center rounded-full bg-gray-100;
}

.activity-item:last-child {
  @apply border-b-0;
}
</style>