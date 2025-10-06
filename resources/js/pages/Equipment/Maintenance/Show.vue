<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center space-x-3">
            <router-link 
              to="/equipment/status" 
              class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <Icon icon="heroicons:arrow-left" class="w-5 h-5 mr-2" />
              {{ $t('base.actions.back') }}
            </router-link>
            <div class="h-6 border-l border-gray-300 dark:border-gray-600"></div>
            <div>
              <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ equipment?.name || $t('equipment.status.detail') }}
              </h1>
              <p v-if="equipment" class="text-sm text-gray-600 dark:text-gray-400">
                {{ equipment.tag }} • {{ $t(`equipment.types.${equipment.type}`) }}
              </p>
            </div>
          </div>
          
          <!-- Actions -->
          <div class="flex items-center space-x-3">
            <el-button 
              @click="refreshData" 
              :loading="loading"
              size="small"
            >
              <Icon icon="heroicons:arrow-path" class="w-4 h-4 mr-2" />
              {{ $t('base.actions.refresh') }}
            </el-button>
            
            <router-link 
              :to="`/equipment/status/${route.params.id}/edit`"
              v-if="equipment"
            >
              <el-button type="primary" size="small">
                <Icon icon="heroicons:pencil" class="w-4 h-4 mr-2" />
                {{ $t('base.actions.edit') }}
              </el-button>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div v-if="loading" class="flex justify-center items-center py-12">
        <el-loading class="w-12 h-12" />
      </div>

      <div v-else-if="equipment" class="space-y-6">
        <!-- Equipment Overview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
              <Icon icon="heroicons:information-circle" class="w-5 h-5 mr-2 text-blue-500" />
              {{ $t('equipment.status.equipment_overview') }}
            </h3>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Equipment Info -->
              <div class="lg:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ $t('equipment.status.fields.equipment_name') }}
                    </label>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                      {{ equipment.name }}
                    </p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ $t('equipment.status.fields.equipment_tag') }}
                    </label>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                      {{ equipment.tag }}
                    </p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ $t('base.common.type') }}
                    </label>
                    <el-tag :type="getEquipmentTypeColor(equipment.type)" class="mt-1">
                      {{ $t(`equipment.types.${equipment.type}`) }}
                    </el-tag>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ $t('base.common.category') }}
                    </label>
                    <el-tag :type="getCategoryColor(equipment.category)" class="mt-1">
                      {{ $t(`equipment.categories.${equipment.category}`) }}
                    </el-tag>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ $t('base.common.vessel') }}
                    </label>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                      {{ equipment.vessel_name }}
                    </p>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                      {{ $t('base.common.updated_at') }}
                    </label>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                      {{ formatDateTime(equipment.updated_at) }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Current Status Card -->
              <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6">
                <div class="text-center">
                  <div class="flex justify-center mb-3">
                    <div :class="`w-20 h-20 rounded-full flex items-center justify-center ${getStatusBgColor(equipment.current_status)}`">
                      <Icon :icon="getStatusIcon(equipment.current_status)" class="w-8 h-8 text-white" />
                    </div>
                  </div>
                  <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $t('equipment.status.current_status') }}
                  </h4>
                  <el-tag :type="getStatusColor(equipment.current_status)" size="large">
                    {{ $t(`equipment.status.operational_status.${equipment.current_status}`) }}
                  </el-tag>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    {{ $t('equipment.status.since') }}: {{ formatDateTime(equipment.status_changed_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Current Readings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
              <Icon icon="heroicons:chart-bar" class="w-5 h-5 mr-2 text-green-500" />
              {{ $t('equipment.status.current_readings') }}
            </h3>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              <!-- Running Hours -->
              <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex justify-center mb-2">
                  <Icon icon="heroicons:clock" class="w-8 h-8 text-blue-500" />
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ equipment.current_readings?.running_hours || '0' }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ $t('equipment.status.fields.running_hours') }}
                </p>
              </div>

              <!-- Load Percentage -->
              <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <div class="flex justify-center mb-2">
                  <Icon icon="heroicons:bolt" class="w-8 h-8 text-green-500" />
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ equipment.current_readings?.load_percentage || '0' }}%
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ $t('equipment.status.fields.load_percentage') }}
                </p>
              </div>

              <!-- Temperature -->
              <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <div class="flex justify-center mb-2">
                  <Icon icon="heroicons:fire" class="w-8 h-8 text-orange-500" />
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ equipment.current_readings?.temperature_reading || '0' }}°C
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ $t('equipment.status.fields.temperature_reading') }}
                </p>
              </div>

              <!-- Efficiency -->
              <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                <div class="flex justify-center mb-2">
                  <Icon icon="heroicons:cog-6-tooth" class="w-8 h-8 text-purple-500" />
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ equipment.current_readings?.efficiency_percentage || '0' }}%
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ $t('equipment.status.fields.efficiency_percentage') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Historical Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Performance Trend -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                  <Icon icon="heroicons:chart-line" class="w-5 h-5 mr-2 text-blue-500" />
                  {{ $t('equipment.status.performance_trend') }}
                </h3>
                <el-select v-model="performanceMetric" size="small" class="w-40">
                  <el-option value="load_percentage" :label="$t('equipment.status.fields.load_percentage')" />
                  <el-option value="efficiency_percentage" :label="$t('equipment.status.fields.efficiency_percentage')" />
                  <el-option value="running_hours" :label="$t('equipment.status.fields.running_hours')" />
                </el-select>
              </div>
            </div>
            <div class="p-6">
              <div ref="performanceChartRef" class="w-full h-80"></div>
            </div>
          </div>

          <!-- Temperature Trend -->
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                <Icon icon="heroicons:fire" class="w-5 h-5 mr-2 text-orange-500" />
                {{ $t('equipment.status.temperature_trend') }}
              </h3>
            </div>
            <div class="p-6">
              <div ref="temperatureChartRef" class="w-full h-80"></div>
            </div>
          </div>
        </div>

        <!-- Status History -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                <Icon icon="heroicons:clock" class="w-5 h-5 mr-2 text-gray-500" />
                {{ $t('equipment.status.status_history') }}
              </h3>
              <el-button @click="exportHistory" size="small">
                <Icon icon="heroicons:arrow-down-tray" class="w-4 h-4 mr-2" />
                {{ $t('base.actions.export') }}
              </el-button>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <el-table
              :data="statusHistory"
              class="w-full"
              :loading="loadingHistory"
              empty-text="No historical data available"
            >
              <el-table-column 
                :label="$t('equipment.status.fields.recorded_at')" 
                prop="recorded_at"
                width="180"
              >
                <template #default="{ row }">
                  {{ formatDateTime(row.recorded_at) }}
                </template>
              </el-table-column>
              
              <el-table-column 
                :label="$t('equipment.status.fields.operational_status')" 
                prop="operational_status"
                width="150"
              >
                <template #default="{ row }">
                  <el-tag :type="getStatusColor(row.operational_status)" size="small">
                    {{ $t(`equipment.status.operational_status.${row.operational_status}`) }}
                  </el-tag>
                </template>
              </el-table-column>
              
              <el-table-column 
                :label="$t('equipment.status.fields.load_percentage')" 
                prop="load_percentage"
                width="120"
                align="right"
              >
                <template #default="{ row }">
                  {{ row.load_percentage || '-' }}%
                </template>
              </el-table-column>
              
              <el-table-column 
                :label="$t('equipment.status.fields.temperature_reading')" 
                prop="temperature_reading"
                width="140"
                align="right"
              >
                <template #default="{ row }">
                  {{ row.temperature_reading || '-' }}°C
                </template>
              </el-table-column>
              
              <el-table-column 
                :label="$t('equipment.status.fields.efficiency_percentage')" 
                prop="efficiency_percentage"
                width="130"
                align="right"
              >
                <template #default="{ row }">
                  {{ row.efficiency_percentage || '-' }}%
                </template>
              </el-table-column>
              
              <el-table-column 
                :label="$t('base.common.remarks')" 
                prop="remarks"
                min-width="200"
              >
                <template #default="{ row }">
                  <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ row.remarks || '-' }}
                  </span>
                </template>
              </el-table-column>
            </el-table>
          </div>
          
          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <el-pagination
              v-model:current-page="currentPage"
              v-model:page-size="pageSize"
              :total="totalRecords"
              :page-sizes="[10, 25, 50, 100]"
              layout="total, sizes, prev, pager, next, jumper"
              @size-change="handleSizeChange"
              @current-change="handleCurrentChange"
              class="justify-center"
            />
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else class="text-center py-12">
        <Icon icon="heroicons:exclamation-circle" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
          {{ $t('equipment.status.not_found') }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
          {{ $t('equipment.status.not_found_desc') }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'
import * as echarts from 'echarts'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()

// Reactive data
const loading = ref(true)
const loadingHistory = ref(false)
const performanceMetric = ref('load_percentage')
const currentPage = ref(1)
const pageSize = ref(25)
const totalRecords = ref(0)

// Chart refs
const performanceChartRef = ref()
const temperatureChartRef = ref()
let performanceChart = null
let temperatureChart = null

// Sample equipment data
const equipment = ref({
  id: 1,
  name: 'Gas Turbine Generator #1',
  tag: 'GTG-001',
  type: 'gas_turbine',
  category: 'production',
  vessel_name: 'FPU Trunojoyo-01',
  current_status: 'running',
  status_changed_at: '2024-01-15 08:30:00',
  updated_at: '2024-01-15 14:45:00',
  current_readings: {
    running_hours: 8756.5,
    load_percentage: 87.5,
    temperature_reading: 485.2,
    pressure_reading: 23.8,
    efficiency_percentage: 89.2,
    speed_rpm: 3565,
    power_output_kw: 25680
  }
})

// Historical data
const statusHistory = ref([
  {
    id: 1,
    recorded_at: '2024-01-15 14:45:00',
    operational_status: 'running',
    load_percentage: 87.5,
    temperature_reading: 485.2,
    efficiency_percentage: 89.2,
    remarks: 'Normal operation'
  },
  {
    id: 2,
    recorded_at: '2024-01-15 12:00:00',
    operational_status: 'running',
    load_percentage: 92.1,
    temperature_reading: 492.8,
    efficiency_percentage: 88.7,
    remarks: 'High load operation'
  },
  {
    id: 3,
    recorded_at: '2024-01-15 08:30:00',
    operational_status: 'standby',
    load_percentage: 0,
    temperature_reading: 45.2,
    efficiency_percentage: null,
    remarks: 'Startup sequence initiated'
  },
  {
    id: 4,
    recorded_at: '2024-01-15 06:00:00',
    operational_status: 'maintenance',
    load_percentage: 0,
    temperature_reading: 35.8,
    efficiency_percentage: null,
    remarks: 'Routine maintenance completed'
  }
])

// Methods
const refreshData = async () => {
  loading.value = true
  try {
    // API call to refresh data
    await new Promise(resolve => setTimeout(resolve, 1000))
    ElMessage.success(t('equipment.status.data_refreshed'))
  } catch (error) {
    ElMessage.error(t('common.error_msg'))
  } finally {
    loading.value = false
  }
}

const formatDateTime = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getEquipmentTypeColor = (type) => {
  const colors = {
    gas_turbine: 'danger',
    generator: 'warning',
    compressor: 'primary',
    pump: 'success',
    motor: 'info'
  }
  return colors[type] || 'info'
}

const getCategoryColor = (category) => {
  const colors = {
    production: 'success',
    utilities: 'primary',
    safety: 'danger',
    marine: 'info',
    hvac: 'warning'
  }
  return colors[category] || 'info'
}

const getStatusColor = (status) => {
  const colors = {
    running: 'success',
    standby: 'warning',
    maintenance: 'info',
    breakdown: 'danger',
    not_available: 'info'
  }
  return colors[status] || 'info'
}

const getStatusBgColor = (status) => {
  const colors = {
    running: 'bg-green-500',
    standby: 'bg-yellow-500',
    maintenance: 'bg-blue-500',
    breakdown: 'bg-red-500',
    not_available: 'bg-gray-500'
  }
  return colors[status] || 'bg-gray-500'
}

const getStatusIcon = (status) => {
  const icons = {
    running: 'heroicons:play',
    standby: 'heroicons:pause',
    maintenance: 'heroicons:wrench-screwdriver',
    breakdown: 'heroicons:exclamation-triangle',
    not_available: 'heroicons:x-mark'
  }
  return icons[status] || 'heroicons:question-mark-circle'
}

const initPerformanceChart = () => {
  if (!performanceChartRef.value) return
  
  performanceChart = echarts.init(performanceChartRef.value)
  
  const option = {
    title: {
      text: t(`equipment.status.fields.${performanceMetric.value}`),
      textStyle: { fontSize: 14 }
    },
    tooltip: {
      trigger: 'axis',
      formatter: function(params) {
        return `${params[0].name}<br/>${params[0].seriesName}: ${params[0].value}${performanceMetric.value.includes('percentage') ? '%' : ''}`
      }
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '3%',
      containLabel: true
    },
    xAxis: {
      type: 'category',
      data: ['06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00']
    },
    yAxis: {
      type: 'value',
      axisLabel: {
        formatter: `{value}${performanceMetric.value.includes('percentage') ? '%' : ''}`
      }
    },
    series: [{
      name: t(`equipment.status.fields.${performanceMetric.value}`),
      type: 'line',
      smooth: true,
      data: [0, 0, 75, 92, 87, 89, 85],
      itemStyle: { color: '#3b82f6' },
      areaStyle: { opacity: 0.1 }
    }]
  }
  
  performanceChart.setOption(option)
}

const initTemperatureChart = () => {
  if (!temperatureChartRef.value) return
  
  temperatureChart = echarts.init(temperatureChartRef.value)
  
  const option = {
    title: {
      text: t('equipment.status.fields.temperature_reading'),
      textStyle: { fontSize: 14 }
    },
    tooltip: {
      trigger: 'axis',
      formatter: function(params) {
        return `${params[0].name}<br/>${params[0].seriesName}: ${params[0].value}°C`
      }
    },
    grid: {
      left: '3%',
      right: '4%',
      bottom: '3%',
      containLabel: true
    },
    xAxis: {
      type: 'category',
      data: ['06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00']
    },
    yAxis: {
      type: 'value',
      axisLabel: {
        formatter: '{value}°C'
      }
    },
    series: [{
      name: t('equipment.status.fields.temperature_reading'),
      type: 'line',
      smooth: true,
      data: [35, 45, 420, 492, 485, 478, 465],
      itemStyle: { color: '#f97316' },
      areaStyle: { opacity: 0.1 }
    }]
  }
  
  temperatureChart.setOption(option)
}

const exportHistory = () => {
  ElMessage.info(t('equipment.status.export_started'))
  // Implement export functionality
}

const handleSizeChange = (val) => {
  pageSize.value = val
  loadStatusHistory()
}

const handleCurrentChange = (val) => {
  currentPage.value = val
  loadStatusHistory()
}

const loadStatusHistory = async () => {
  loadingHistory.value = true
  try {
    // API call to load history with pagination
    await new Promise(resolve => setTimeout(resolve, 500))
    totalRecords.value = 50 // Sample total
  } catch (error) {
    ElMessage.error(t('common.error_msg'))
  } finally {
    loadingHistory.value = false
  }
}

// Watch for metric changes
watch(performanceMetric, () => {
  if (performanceChart) {
    initPerformanceChart()
  }
})

// Lifecycle
onMounted(async () => {
  try {
    // Load equipment data
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Initialize charts
    await nextTick()
    initPerformanceChart()
    initTemperatureChart()
    
    // Load history
    totalRecords.value = statusHistory.value.length
    
    // Handle window resize
    window.addEventListener('resize', () => {
      if (performanceChart) performanceChart.resize()
      if (temperatureChart) temperatureChart.resize()
    })
  } catch (error) {
    ElMessage.error(t('common.error_msg'))
  } finally {
    loading.value = false
  }
})

// Cleanup
onUnmounted(() => {
  if (performanceChart) {
    performanceChart.dispose()
  }
  if (temperatureChart) {
    temperatureChart.dispose()
  }
  window.removeEventListener('resize', () => {})
})
</script>

<style scoped>
/* Chart containers */
.chart-container {
  width: 100%;
  height: 320px;
}

/* Mobile responsive adjustments */
@media (max-width: 640px) {
  .chart-container {
    height: 250px;
  }
}
</style>