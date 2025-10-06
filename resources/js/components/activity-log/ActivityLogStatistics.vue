<template>
  <div class="statistics-container">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Activity Log</h3>
    
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
      <span class="ml-2 text-gray-600">Memuat statistik...</span>
    </div>
    
    <!-- Statistics Content -->
    <div v-else-if="statistics" class="statistics-content">
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Activities -->
        <div class="stat-card bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="stat-icon bg-blue-500 text-white rounded-full p-2 mr-3">
              <i class="fas fa-chart-line text-sm"></i>
            </div>
            <div>
              <p class="text-sm text-blue-600 font-medium">Total Aktivitas</p>
              <p class="text-2xl font-bold text-blue-800">{{ statistics.total_activities }}</p>
            </div>
          </div>
        </div>
        
        <!-- Date Range -->
        <div class="stat-card bg-green-50 border border-green-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="stat-icon bg-green-500 text-white rounded-full p-2 mr-3">
              <i class="fas fa-calendar text-sm"></i>
            </div>
            <div>
              <p class="text-sm text-green-600 font-medium">Periode</p>
              <p class="text-xs text-green-800 font-semibold">
                {{ formatDate(statistics.date_range.from) }} -<br>
                {{ formatDate(statistics.date_range.to) }}
              </p>
            </div>
          </div>
        </div>
        
        <!-- Most Active User -->
        <div class="stat-card bg-purple-50 border border-purple-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="stat-icon bg-purple-500 text-white rounded-full p-2 mr-3">
              <i class="fas fa-user text-sm"></i>
            </div>
            <div>
              <p class="text-sm text-purple-600 font-medium">User Teraktif</p>
              <p class="text-sm font-bold text-purple-800">
                {{ mostActiveUser?.causer?.name || 'N/A' }}
              </p>
              <p class="text-xs text-purple-600">
                {{ mostActiveUser?.count || 0 }} aktivitas
              </p>
            </div>
          </div>
        </div>
        
        <!-- Most Active Model -->
        <div class="stat-card bg-orange-50 border border-orange-200 rounded-lg p-4">
          <div class="flex items-center">
            <div class="stat-icon bg-orange-500 text-white rounded-full p-2 mr-3">
              <i class="fas fa-database text-sm"></i>
            </div>
            <div>
              <p class="text-sm text-orange-600 font-medium">Model Teraktif</p>
              <p class="text-sm font-bold text-orange-800">
                {{ formatModelName(mostActiveModel?.subject_type) || 'N/A' }}
              </p>
              <p class="text-xs text-orange-600">
                {{ mostActiveModel?.count || 0 }} aktivitas
              </p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activities by Type Chart -->
        <div class="chart-container bg-white border rounded-lg p-4">
          <h4 class="text-md font-semibold text-gray-800 mb-4">Aktivitas per Tipe Model</h4>
          <div v-if="statistics.activities_by_type.length > 0">
            <div v-for="item in statistics.activities_by_type" :key="item.subject_type" class="mb-3">
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm text-gray-700">{{ formatModelName(item.subject_type) }}</span>
                <span class="text-sm font-semibold text-gray-900">{{ item.count }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                  :style="{ width: (item.count / maxTypeCount * 100) + '%' }"
                ></div>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-4">
            Tidak ada data
          </div>
        </div>
        
        <!-- Activities by User Chart -->
        <div class="chart-container bg-white border rounded-lg p-4">
          <h4 class="text-md font-semibold text-gray-800 mb-4">Top 10 User Teraktif</h4>
          <div v-if="statistics.activities_by_user.length > 0">
            <div v-for="item in statistics.activities_by_user" :key="item.causer_id" class="mb-3">
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm text-gray-700">{{ item.causer?.name || 'Unknown' }}</span>
                <span class="text-sm font-semibold text-gray-900">{{ item.count }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-green-500 h-2 rounded-full transition-all duration-300"
                  :style="{ width: (item.count / maxUserCount * 100) + '%' }"
                ></div>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-4">
            Tidak ada data
          </div>
        </div>
      </div>
      
      <!-- Activities by Day Chart -->
      <div class="chart-container bg-white border rounded-lg p-4 mt-6">
        <h4 class="text-md font-semibold text-gray-800 mb-4">Aktivitas Harian</h4>
        <div v-if="statistics.activities_by_day.length > 0" class="overflow-x-auto">
          <div class="flex items-end space-x-2 min-w-full" style="height: 200px;">
            <div 
              v-for="item in statistics.activities_by_day" 
              :key="item.date"
              class="flex flex-col items-center flex-1 min-w-0"
            >
              <div class="text-xs text-gray-600 mb-1">{{ item.count }}</div>
              <div 
                class="bg-indigo-500 rounded-t transition-all duration-300 w-full min-w-4"
                :style="{ height: (item.count / maxDayCount * 160) + 'px' }"
              ></div>
              <div class="text-xs text-gray-500 mt-1 transform rotate-45 origin-left">
                {{ formatShortDate(item.date) }}
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center text-gray-500 py-8">
          Tidak ada data
        </div>
      </div>
    </div>
    
    <!-- Error State -->
    <div v-else class="text-center text-red-500 py-8">
      <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
      <p>Gagal memuat statistik</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useActivityLogStore } from '@/stores/activityLogStore'
import { formatDate, formatShortDate } from '@/utils/dateHelpers'

// Props
const props = defineProps({
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Store
const activityLogStore = useActivityLogStore()

// Reactive data
const loading = ref(false)

// Computed
const statistics = computed(() => activityLogStore.statistics)

const mostActiveUser = computed(() => {
  if (!statistics.value?.activities_by_user?.length) return null
  return statistics.value.activities_by_user[0]
})

const mostActiveModel = computed(() => {
  if (!statistics.value?.activities_by_type?.length) return null
  return statistics.value.activities_by_type.reduce((max, current) => 
    current.count > max.count ? current : max
  )
})

const maxTypeCount = computed(() => {
  if (!statistics.value?.activities_by_type?.length) return 1
  return Math.max(...statistics.value.activities_by_type.map(item => item.count))
})

const maxUserCount = computed(() => {
  if (!statistics.value?.activities_by_user?.length) return 1
  return Math.max(...statistics.value.activities_by_user.map(item => item.count))
})

const maxDayCount = computed(() => {
  if (!statistics.value?.activities_by_day?.length) return 1
  return Math.max(...statistics.value.activities_by_day.map(item => item.count))
})

// Methods
const fetchStatistics = async () => {
  loading.value = true
  try {
    const params = {
      date_from: props.filters.date_from,
      date_to: props.filters.date_to
    }
    await activityLogStore.fetchStatistics(params)
  } catch (error) {
    console.error('Error fetching statistics:', error)
  } finally {
    loading.value = false
  }
}

const formatModelName = (modelType) => {
  if (!modelType) return 'Unknown'
  const modelMap = {
    'App\\Models\\Partner': 'Partner',
    'App\\Models\\Product\\Product': 'Product',
    'App\\Models\\Sales\\SaleOrder': 'Sales Order'
  }
  return modelMap[modelType] || modelType.split('\\').pop()
}

// Watchers
watch(
  () => [props.filters.date_from, props.filters.date_to],
  () => {
    fetchStatistics()
  },
  { deep: true }
)

// Lifecycle
onMounted(() => {
  fetchStatistics()
})
</script>

<style scoped>
.statistics-container {
  @apply bg-gray-50 rounded-lg p-6;
}

.stat-card {
  @apply transition-all duration-200 hover:shadow-md;
}

.stat-icon {
  @apply w-8 h-8 flex items-center justify-center;
}

.chart-container {
  @apply shadow-sm;
}
</style>