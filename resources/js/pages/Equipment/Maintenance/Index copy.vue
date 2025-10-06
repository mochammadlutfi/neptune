<template>
  <div class="equipment-status-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
      <div class="header-content">
        <div class="header-left">
          <h1 class="page-title">{{ $t('equipment.status.title') }}</h1>
          <p class="page-subtitle">Real-time equipment monitoring and status tracking</p>
        </div>
        <div class="header-right">
          <div class="header-controls">
            <el-select v-model="selectedVessel" @change="handleVesselChange" placeholder="All Vessels" size="default" class="vessel-filter">
              <el-option key="all" label="All Vessels" value="all" />
              <el-option v-for="vessel in vessels" :key="vessel.id" :label="vessel.name" :value="vessel.id" />
            </el-select>
            <el-button type="primary" @click="refreshData" :loading="loading" class="refresh-btn">
              <Icon icon="lucide:refresh-cw" class="w-4 h-4" />
              Refresh
            </el-button>
            <el-button type="success" @click="navigateToForm" class="add-btn">
              <Icon icon="lucide:plus" class="w-4 h-4" />
              Update Status
            </el-button>
          </div>
        </div>
      </div>
    </div>

    <!-- Alert Notifications -->
    <div v-if="criticalAlerts.length > 0" class="alerts-section">
      <el-alert
        v-for="alert in criticalAlerts"
        :key="alert.id"
        :title="alert.title"
        :type="alert.type"
        :description="alert.description"
        show-icon
        :closable="false"
        class="alert-item"
      >
        <template #default>
          <div class="alert-content">
            <span class="alert-equipment">{{ alert.equipment_name }}</span>
            <span class="alert-time">{{ formatDateTime(alert.timestamp) }}</span>
          </div>
        </template>
      </el-alert>
    </div>

    <!-- Equipment Availability Summary -->
    <div class="availability-summary">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
        <StatsCard
          title="Total Equipment"
          :value="equipmentSummary.total"
          icon="lucide:settings"
          icon-color="blue"
        />
        
        <StatsCard
          title="Running"
          :value="equipmentSummary.running"
          icon="lucide:play-circle"
          icon-color="green"
          :additional-info="{
            type: 'breakdown',
            value: Math.round((equipmentSummary.running / equipmentSummary.total) * 100),
            label: '%'
          }"
        />
        
        <StatsCard
          title="Standby"
          :value="equipmentSummary.standby"
          icon="lucide:pause-circle"
          icon-color="yellow"
          :additional-info="{
            type: 'breakdown',
            value: Math.round((equipmentSummary.standby / equipmentSummary.total) * 100),
            label: '%'
          }"
        />
        
        <StatsCard
          title="Maintenance"
          :value="equipmentSummary.maintenance"
          icon="lucide:wrench"
          icon-color="orange"
          :additional-info="{
            type: 'breakdown',
            value: Math.round((equipmentSummary.maintenance / equipmentSummary.total) * 100),
            label: '%'
          }"
        />
        
        <StatsCard
          title="Breakdown"
          :value="equipmentSummary.breakdown"
          icon="lucide:alert-triangle"
          icon-color="red"
          :additional-info="{
            type: 'breakdown',
            value: Math.round((equipmentSummary.breakdown / equipmentSummary.total) * 100),
            label: '%'
          }"
        />
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Filters and Controls -->
      <div class="content-header">
        <div class="filters-section">
          <el-select v-model="selectedCategory" @change="applyFilters" placeholder="All Categories" size="small" class="filter-select">
            <el-option key="all" label="All Categories" value="all" />
            <el-option v-for="(label, key) in categories" :key="key" :label="label" :value="key" />
          </el-select>
          <el-select v-model="selectedType" @change="applyFilters" placeholder="All Types" size="small" class="filter-select">
            <el-option key="all" label="All Types" value="all" />
            <el-option v-for="(label, key) in equipmentTypes" :key="key" :label="label" :value="key" />
          </el-select>
          <el-select v-model="selectedStatus" @change="applyFilters" placeholder="All Status" size="small" class="filter-select">
            <el-option key="all" label="All Status" value="all" />
            <el-option v-for="(label, key) in operationalStatus" :key="key" :label="label" :value="key" />
          </el-select>
        </div>
        <div class="view-controls">
          <el-button-group>
            <el-button :type="viewMode === 'grid' ? 'primary' : 'default'" size="small" @click="viewMode = 'grid'">
              <Icon icon="lucide:grid-3x3" class="w-4 h-4" />
            </el-button>
            <el-button :type="viewMode === 'list' ? 'primary' : 'default'" size="small" @click="viewMode = 'list'">
              <Icon icon="lucide:list" class="w-4 h-4" />
            </el-button>
          </el-button-group>
        </div>
      </div>

      <!-- Critical Equipment Section -->
      <div class="critical-section">
        <div class="section-header">
          <h2 class="section-title">
            <Icon icon="lucide:alert-circle" class="w-5 h-5 text-red-500" />
            Critical Equipment
          </h2>
          <span class="critical-count">{{ criticalEquipment.length }} Items</span>
        </div>
        <div class="critical-grid">
          <div 
            v-for="equipment in criticalEquipment" 
            :key="equipment.id"
            class="critical-card"
            :class="getStatusClass(equipment.operational_status)"
            @click="viewEquipment(equipment.id)"
          >
            <div class="critical-header">
              <div class="equipment-info">
                <h3 class="equipment-name">{{ equipment.name }}</h3>
                <p class="equipment-tag">{{ equipment.tag }}</p>
              </div>
              <div class="status-indicator" :class="equipment.operational_status.toLowerCase()">
                <div class="status-dot"></div>
                <span class="status-text">{{ $t(`equipment.status.operational_status.${equipment.operational_status.toLowerCase()}`) }}</span>
              </div>
            </div>
            <div class="critical-metrics">
              <div class="metric-item">
                <span class="metric-label">Load</span>
                <span class="metric-value">{{ equipment.load_percentage || 0 }}%</span>
              </div>
              <div class="metric-item">
                <span class="metric-label">Running Hours</span>
                <span class="metric-value">{{ equipment.running_hours || 0 }}h</span>
              </div>
              <div class="metric-item">
                <span class="metric-label">Efficiency</span>
                <span class="metric-value">{{ equipment.efficiency_percentage || 0 }}%</span>
              </div>
            </div>
            <div v-if="equipment.active_alarms && equipment.active_alarms.length > 0" class="alarms-indicator">
              <Icon icon="lucide:alert-triangle" class="w-4 h-4 text-red-500" />
              <span class="alarms-count">{{ equipment.active_alarms.length }} Active Alarms</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Equipment Grid/List -->
      <div class="equipment-section">
        <div class="section-header">
          <h2 class="section-title">All Equipment Status</h2>
          <div class="section-stats">
            <span class="stats-text">{{ filteredEquipment.length }} of {{ equipmentList.length }} equipment</span>
          </div>
        </div>

        <!-- Grid View -->
        <div v-if="viewMode === 'grid'" class="equipment-grid">
          <div 
            v-for="equipment in paginatedEquipment" 
            :key="equipment.id"
            class="equipment-card"
            :class="getStatusClass(equipment.operational_status)"
            @click="viewEquipment(equipment.id)"
          >
            <div class="card-header">
              <div class="equipment-type-icon">
                <Icon :icon="getEquipmentIcon(equipment.type)" class="w-6 h-6" />
              </div>
              <div class="status-badge" :class="equipment.operational_status.toLowerCase()">
                <div class="status-dot"></div>
              </div>
            </div>
            <div class="card-content">
              <h3 class="equipment-name">{{ equipment.name }}</h3>
              <p class="equipment-details">{{ equipment.tag }} • {{ $t(`equipment.types.${equipment.type.toLowerCase().replace(' ', '_')}`) }}</p>
              <div class="performance-metrics">
                <div class="metric">
                  <span class="metric-label">Load</span>
                  <div class="metric-bar">
                    <div class="metric-fill" :style="{ width: (equipment.load_percentage || 0) + '%' }"></div>
                  </div>
                  <span class="metric-value">{{ equipment.load_percentage || 0 }}%</span>
                </div>
                <div class="metric">
                  <span class="metric-label">Efficiency</span>
                  <div class="metric-bar">
                    <div class="metric-fill efficiency" :style="{ width: (equipment.efficiency_percentage || 0) + '%' }"></div>
                  </div>
                  <span class="metric-value">{{ equipment.efficiency_percentage || 0 }}%</span>
                </div>
              </div>
              <div class="card-footer">
                <span class="last-updated">Updated {{ formatRelativeTime(equipment.reading_timestamp) }}</span>
                <div v-if="equipment.trip_status" class="trip-indicator">
                  <Icon icon="lucide:zap-off" class="w-4 h-4 text-red-500" />
                  <span class="trip-text">Tripped</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- List View -->
        <div v-else class="equipment-list">
          <el-table :data="paginatedEquipment" @row-click="viewEquipment" class="equipment-table">
            <el-table-column width="60">
              <template #default="scope">
                <div class="status-indicator-small" :class="scope.row.operational_status.toLowerCase()">
                  <div class="status-dot-small"></div>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="name" label="Equipment Name" min-width="200">
              <template #default="scope">
                <div class="equipment-cell">
                  <Icon :icon="getEquipmentIcon(scope.row.type)" class="w-5 h-5 equipment-icon" />
                  <div class="equipment-info">
                    <span class="equipment-name">{{ scope.row.name }}</span>
                    <span class="equipment-tag">{{ scope.row.tag }}</span>
                  </div>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="type" label="Type" width="150">
              <template #default="scope">
                {{ $t(`equipment.types.${scope.row.type.toLowerCase().replace(' ', '_')}`) }}
              </template>
            </el-table-column>
            <el-table-column prop="operational_status" label="Status" width="120">
              <template #default="scope">
                <el-tag :type="getTagType(scope.row.operational_status)" size="small">
                  {{ $t(`equipment.status.operational_status.${scope.row.operational_status.toLowerCase()}`) }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="load_percentage" label="Load %" width="100" align="center">
              <template #default="scope">
                <div class="progress-cell">
                  <el-progress :percentage="scope.row.load_percentage || 0" :stroke-width="6" :show-text="false" />
                  <span class="progress-text">{{ scope.row.load_percentage || 0 }}%</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="efficiency_percentage" label="Efficiency %" width="120" align="center">
              <template #default="scope">
                <span :class="getEfficiencyClass(scope.row.efficiency_percentage)">
                  {{ scope.row.efficiency_percentage || 0 }}%
                </span>
              </template>
            </el-table-column>
            <el-table-column prop="running_hours_today" label="Runtime (h)" width="110" align="center" />
            <el-table-column label="Alarms" width="80" align="center">
              <template #default="scope">
                <div v-if="scope.row.active_alarms && scope.row.active_alarms.length > 0" class="alarms-cell">
                  <Icon icon="lucide:alert-triangle" class="w-4 h-4 text-red-500" />
                  <span class="alarms-count">{{ scope.row.active_alarms.length }}</span>
                </div>
                <span v-else class="no-alarms">-</span>
              </template>
            </el-table-column>
            <el-table-column label="Actions" width="100" align="center">
              <template #default="scope">
                <el-button size="small" type="primary" link @click.stop="updateStatus(scope.row.id)">
                  Update
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>

        <!-- Pagination -->
        <div class="pagination-section">
          <el-pagination
            v-model:current-page="currentPage"
            v-model:page-size="pageSize"
            :page-sizes="[10, 20, 50, 100]"
            :total="filteredEquipment.length"
            layout="total, sizes, prev, pager, next, jumper"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
          />
        </div>
      </div>
    </div>

    <!-- Performance Chart Modal -->
    <el-dialog v-model="showPerformanceChart" title="Equipment Performance Trends" width="80%">
      <div class="chart-container">
        <v-chart :option="performanceChartOption" class="performance-chart" />
      </div>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import VChart from 'vue-echarts'
import { use } from 'echarts/core'
import { LineChart, BarChart } from 'echarts/charts'
import { TitleComponent, TooltipComponent, LegendComponent, GridComponent } from 'echarts/components'
import { CanvasRenderer } from 'echarts/renderers'
import StatsCard from '@/components/StatsCard.vue'

// Register ECharts components
use([CanvasRenderer, LineChart, BarChart, TitleComponent, TooltipComponent, LegendComponent, GridComponent])

const route = useRoute()
const router = useRouter()

// Reactive data
const loading = ref(false)
const selectedVessel = ref('all')
const selectedCategory = ref('all')
const selectedType = ref('all')
const selectedStatus = ref('all')
const viewMode = ref('grid')
const currentPage = ref(1)
const pageSize = ref(20)
const showPerformanceChart = ref(false)

// Sample data
const vessels = ref([
  { id: 1, name: 'FPU Trunojoyo-01' },
  { id: 2, name: 'FPU Trunojoyo-02' },
  { id: 3, name: 'MOPU Prameswari-8' }
])

// Equipment data from database structure
const equipmentList = ref([
  {
    id: 1,
    vessel_id: 1,
    code: 'GT-6640A',
    tag: 'GT-6640A',
    name: 'Gas Turbine Generator',
    type: 'Gas Turbine',
    category: 'Production',
    is_critical: true,
    operational_status: 'Running',
    load_percentage: 85.5,
    efficiency_percentage: 92.3,
    running_hours_today: 22.5,
    running_hours_meter: 15420.8,
    power_output_kw: 6800,
    fuel_consumption_lph: 1250,
    inlet_temperature_f: 85.2,
    outlet_temperature_f: 1050.5,
    vibration_mm_s: 2.8,
    active_alarms: ['High Exhaust Temperature'],
    trip_status: false,
    reading_timestamp: new Date(Date.now() - 300000) // 5 minutes ago
  },
  {
    id: 2,
    vessel_id: 1,
    code: 'COMP-001',
    tag: 'COMP-001',
    name: 'Gas Export Compressor',
    type: 'Compressor',
    category: 'Production',
    is_critical: true,
    operational_status: 'Running',
    load_percentage: 78.2,
    efficiency_percentage: 89.1,
    running_hours_today: 24.0,
    running_hours_meter: 8965.2,
    power_output_kw: 2500,
    inlet_pressure_psi: 150.5,
    outlet_pressure_psi: 1200.8,
    vibration_mm_s: 3.2,
    active_alarms: [],
    trip_status: false,
    reading_timestamp: new Date(Date.now() - 180000) // 3 minutes ago
  },
  {
    id: 3,
    vessel_id: 1,
    code: 'SEP-HP01',
    tag: 'SEP-HP01',
    name: 'HP Production Separator',
    type: 'Separator',
    category: 'Production',
    is_critical: true,
    operational_status: 'Running',
    load_percentage: 92.0,
    efficiency_percentage: 94.5,
    running_hours_today: 24.0,
    running_hours_meter: 12450.5,
    inlet_pressure_psi: 1800.5,
    outlet_pressure_psi: 1750.2,
    active_alarms: [],
    trip_status: false,
    reading_timestamp: new Date(Date.now() - 120000) // 2 minutes ago
  },
  {
    id: 4,
    vessel_id: 1,
    code: 'PUMP-001',
    tag: 'PUMP-001',
    name: 'Export Oil Pump',
    type: 'Pump',
    category: 'Production',
    is_critical: false,
    operational_status: 'Standby',
    load_percentage: 0,
    efficiency_percentage: 0,
    running_hours_today: 0,
    running_hours_meter: 5420.8,
    active_alarms: [],
    trip_status: false,
    reading_timestamp: new Date(Date.now() - 600000) // 10 minutes ago
  },
  {
    id: 5,
    vessel_id: 1,
    code: 'GEN-001',
    tag: 'GEN-001',
    name: 'Emergency Generator',
    type: 'Generator',
    category: 'Utilities',
    is_critical: true,
    operational_status: 'Maintenance',
    load_percentage: 0,
    efficiency_percentage: 0,
    running_hours_today: 0,
    running_hours_meter: 2150.5,
    active_alarms: ['Scheduled Maintenance'],
    trip_status: false,
    reading_timestamp: new Date(Date.now() - 1800000) // 30 minutes ago
  }
])

// Static reference data
const categories = {
  production: 'Production',
  utilities: 'Utilities', 
  safety: 'Safety',
  marine: 'Marine',
  hvac: 'HVAC',
  telecommunications: 'Telecommunications'
}

const equipmentTypes = {
  gas_turbine: 'Gas Turbine',
  generator: 'Generator',
  compressor: 'Compressor',
  separator: 'Separator',
  pump: 'Pump',
  heat_exchanger: 'Heat Exchanger',
  motor: 'Motor',
  fan: 'Fan',
  cooler: 'Cooler',
  other: 'Other'
}

const operationalStatus = {
  running: 'Running',
  standby: 'Standby',
  maintenance: 'Maintenance',
  breakdown: 'Breakdown',
  not_available: 'Not Available'
}

// Computed properties
const filteredEquipment = computed(() => {
  let filtered = equipmentList.value

  if (selectedVessel.value !== 'all') {
    filtered = filtered.filter(eq => eq.vessel_id === selectedVessel.value)
  }
  if (selectedCategory.value !== 'all') {
    filtered = filtered.filter(eq => eq.category.toLowerCase() === selectedCategory.value)
  }
  if (selectedType.value !== 'all') {
    filtered = filtered.filter(eq => eq.type.toLowerCase().replace(' ', '_') === selectedType.value)
  }
  if (selectedStatus.value !== 'all') {
    filtered = filtered.filter(eq => eq.operational_status.toLowerCase() === selectedStatus.value)
  }

  return filtered
})

const paginatedEquipment = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value
  const end = start + pageSize.value
  return filteredEquipment.value.slice(start, end)
})

const equipmentSummary = computed(() => {
  const total = filteredEquipment.value.length
  const running = filteredEquipment.value.filter(eq => eq.operational_status === 'Running').length
  const standby = filteredEquipment.value.filter(eq => eq.operational_status === 'Standby').length
  const maintenance = filteredEquipment.value.filter(eq => eq.operational_status === 'Maintenance').length
  const breakdown = filteredEquipment.value.filter(eq => eq.operational_status === 'Breakdown').length

  return { total, running, standby, maintenance, breakdown }
})

const criticalEquipment = computed(() => {
  return filteredEquipment.value.filter(eq => eq.is_critical)
})

const criticalAlerts = computed(() => {
  return filteredEquipment.value
    .filter(eq => eq.active_alarms && eq.active_alarms.length > 0)
    .map(eq => ({
      id: eq.id,
      type: eq.operational_status === 'Breakdown' ? 'error' : 'warning',
      title: eq.active_alarms[0],
      description: `${eq.name} (${eq.tag}) requires attention`,
      equipment_name: eq.name,
      timestamp: eq.reading_timestamp
    }))
})

const performanceChartOption = computed(() => ({
  title: {
    text: 'Equipment Performance Trends',
    left: 'center'
  },
  tooltip: {
    trigger: 'axis'
  },
  legend: {
    bottom: 10
  },
  xAxis: {
    type: 'category',
    data: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00']
  },
  yAxis: {
    type: 'value',
    name: 'Efficiency %'
  },
  series: [{
    name: 'Equipment Efficiency',
    type: 'line',
    data: [92, 91, 93, 89, 94, 92, 90],
    smooth: true,
    itemStyle: { color: '#3b82f6' }
  }]
}))

// Methods
const refreshData = async () => {
  loading.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    // Update reading timestamps
    equipmentList.value.forEach(eq => {
      eq.reading_timestamp = new Date()
    })
  } finally {
    loading.value = false
  }
}

const handleVesselChange = (vesselId) => {
  selectedVessel.value = vesselId
  applyFilters()
}

const applyFilters = () => {
  currentPage.value = 1
}

const handleSizeChange = (val) => {
  pageSize.value = val
  currentPage.value = 1
}

const handleCurrentChange = (val) => {
  currentPage.value = val
}

const navigateToForm = () => {
  router.push('/equipment/status/form')
}

const viewEquipment = (equipmentId) => {
  router.push(`/equipment/status/${equipmentId}`)
}

const updateStatus = (equipmentId) => {
  router.push(`/equipment/status/form?equipment=${equipmentId}`)
}

const getStatusClass = (status) => {
  return `status-${status.toLowerCase().replace(' ', '-')}`
}

const getTagType = (status) => {
  const types = {
    'Running': 'success',
    'Standby': 'warning', 
    'Maintenance': 'info',
    'Breakdown': 'danger',
    'Not Available': 'info'
  }
  return types[status] || 'info'
}

const getEfficiencyClass = (efficiency) => {
  if (efficiency >= 90) return 'efficiency-excellent'
  if (efficiency >= 80) return 'efficiency-good'
  if (efficiency >= 70) return 'efficiency-fair'
  return 'efficiency-poor'
}

const getEquipmentIcon = (type) => {
  const icons = {
    'Gas Turbine': 'lucide:zap',
    'Generator': 'lucide:battery',
    'Compressor': 'lucide:wind',
    'Separator': 'lucide:filter',
    'Pump': 'lucide:droplets',
    'Heat Exchanger': 'lucide:thermometer',
    'Motor': 'lucide:rotate-cw',
    'Fan': 'lucide:fan',
    'Cooler': 'lucide:snowflake'
  }
  return icons[type] || 'lucide:settings'
}

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatRelativeTime = (date) => {
  const now = new Date()
  const diff = now - new Date(date)
  const minutes = Math.floor(diff / 60000)
  
  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  const hours = Math.floor(minutes / 60)
  return `${hours}h ago`
}

// Lifecycle
onMounted(() => {
  refreshData()
})
</script>

<style scoped>
.equipment-status-dashboard {
  @apply min-h-screen bg-gray-50 dark:bg-gray-900;
}

/* Header */
.dashboard-header {
  @apply bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6;
}

.header-content {
  @apply flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4;
}

.page-title {
  @apply text-2xl font-bold text-gray-900 dark:text-white;
}

.page-subtitle {
  @apply text-sm text-gray-600 dark:text-gray-400 mt-1;
}

.header-controls {
  @apply flex items-center gap-3;
}

.vessel-filter {
  @apply min-w-48;
}

.refresh-btn, .add-btn {
  @apply gap-2;
}

/* Alerts */
.alerts-section {
  @apply p-4 space-y-2;
}

.alert-content {
  @apply flex items-center justify-between;
}

.alert-equipment {
  @apply font-medium;
}

.alert-time {
  @apply text-xs text-gray-500;
}

/* Summary Cards */
.availability-summary {
  @apply p-6;
}

.summary-cards {
  @apply grid grid-cols-1 md:grid-cols-5 gap-6;
}

.summary-card {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm;
}

.card-icon {
  @apply mb-4;
}

.summary-card.total .card-icon {
  @apply text-gray-600 dark:text-gray-400;
}

.summary-card.running .card-icon {
  @apply text-green-600;
}

.summary-card.standby .card-icon {
  @apply text-yellow-600;
}

.summary-card.maintenance .card-icon {
  @apply text-blue-600;
}

.summary-card.breakdown .card-icon {
  @apply text-red-600;
}

.card-value {
  @apply text-3xl font-bold text-gray-900 dark:text-white mb-1;
}

.card-label {
  @apply text-sm text-gray-600 dark:text-gray-400;
}

.card-percentage {
  @apply text-xs text-gray-500 dark:text-gray-500 mt-1;
}

/* Main Content */
.main-content {
  @apply p-6 space-y-6;
}

.content-header {
  @apply flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4;
}

.filters-section {
  @apply flex items-center gap-3;
}

.filter-select {
  @apply min-w-32;
}

/* Critical Equipment */
.critical-section {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700;
}

.section-header {
  @apply flex items-center justify-between mb-6;
}

.section-title {
  @apply flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white;
}

.critical-count {
  @apply text-sm text-gray-500 dark:text-gray-400;
}

.critical-grid {
  @apply grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4;
}

.critical-card {
  @apply border-2 rounded-lg p-4 cursor-pointer transition-all duration-200;
}

.critical-card:hover {
  @apply shadow-md transform -translate-y-1;
}

.critical-card.status-running {
  @apply border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-900/20;
}

.critical-card.status-standby {
  @apply border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-900/20;
}

.critical-card.status-maintenance {
  @apply border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-900/20;
}

.critical-card.status-breakdown {
  @apply border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20;
}

.critical-header {
  @apply flex items-start justify-between mb-3;
}

.equipment-name {
  @apply font-semibold text-gray-900 dark:text-white;
}

.equipment-tag {
  @apply text-sm text-gray-600 dark:text-gray-400;
}

.status-indicator {
  @apply flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium;
}

.status-indicator.running {
  @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
}

.status-indicator.standby {
  @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200;
}

.status-indicator.maintenance {
  @apply bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200;
}

.status-indicator.breakdown {
  @apply bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200;
}

.status-dot {
  @apply w-1.5 h-1.5 rounded-full;
}

.status-indicator.running .status-dot {
  @apply bg-green-500;
}

.status-indicator.standby .status-dot {
  @apply bg-yellow-500;
}

.status-indicator.maintenance .status-dot {
  @apply bg-blue-500;
}

.status-indicator.breakdown .status-dot {
  @apply bg-red-500;
}

.critical-metrics {
  @apply grid grid-cols-3 gap-2 mb-3;
}

.metric-item {
  @apply text-center;
}

.metric-label {
  @apply text-xs text-gray-500 dark:text-gray-400;
}

.metric-value {
  @apply block text-sm font-semibold text-gray-900 dark:text-white;
}

.alarms-indicator {
  @apply flex items-center gap-1 text-xs text-red-600 dark:text-red-400;
}

/* Equipment Section */
.equipment-section {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700;
}

.section-stats {
  @apply text-sm text-gray-500 dark:text-gray-400;
}

/* Grid View */
.equipment-grid {
  @apply grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6;
}

.equipment-card {
  @apply border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer transition-all duration-200;
}

.equipment-card:hover {
  @apply shadow-md border-blue-300 dark:border-blue-600;
}

.card-header {
  @apply flex items-center justify-between mb-3;
}

.equipment-type-icon {
  @apply p-2 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-400;
}

.status-badge {
  @apply w-3 h-3 rounded-full;
}

.status-badge.running {
  @apply bg-green-500;
}

.status-badge.standby {
  @apply bg-yellow-500;
}

.status-badge.maintenance {
  @apply bg-blue-500;
}

.status-badge.breakdown {
  @apply bg-red-500;
}

.card-content .equipment-name {
  @apply font-semibold text-gray-900 dark:text-white mb-1;
}

.equipment-details {
  @apply text-sm text-gray-600 dark:text-gray-400 mb-3;
}

.performance-metrics {
  @apply space-y-2 mb-3;
}

.metric {
  @apply flex items-center gap-2;
}

.metric-label {
  @apply text-xs text-gray-500 dark:text-gray-400 w-16;
}

.metric-bar {
  @apply flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5;
}

.metric-fill {
  @apply bg-blue-500 h-full rounded-full;
}

.metric-fill.efficiency {
  @apply bg-green-500;
}

.metric-value {
  @apply text-xs font-medium text-gray-900 dark:text-white w-10 text-right;
}

.card-footer {
  @apply flex items-center justify-between text-xs;
}

.last-updated {
  @apply text-gray-500 dark:text-gray-400;
}

.trip-indicator {
  @apply flex items-center gap-1;
}

.trip-text {
  @apply text-red-600 dark:text-red-400 font-medium;
}

/* List View */
.equipment-table {
  @apply w-full;
}

.status-indicator-small {
  @apply flex items-center justify-center;
}

.status-dot-small {
  @apply w-2 h-2 rounded-full;
}

.status-indicator-small.running .status-dot-small {
  @apply bg-green-500;
}

.status-indicator-small.standby .status-dot-small {
  @apply bg-yellow-500;
}

.status-indicator-small.maintenance .status-dot-small {
  @apply bg-blue-500;
}

.status-indicator-small.breakdown .status-dot-small {
  @apply bg-red-500;
}

.equipment-cell {
  @apply flex items-center gap-3;
}

.equipment-icon {
  @apply text-gray-500 dark:text-gray-400;
}

.equipment-info {
  @apply flex flex-col;
}

.equipment-cell .equipment-name {
  @apply font-medium text-gray-900 dark:text-white;
}

.equipment-cell .equipment-tag {
  @apply text-xs text-gray-500 dark:text-gray-400;
}

.progress-cell {
  @apply flex items-center gap-2;
}

.progress-text {
  @apply text-xs text-gray-600 dark:text-gray-400;
}

.efficiency-excellent {
  @apply text-green-600 font-medium;
}

.efficiency-good {
  @apply text-blue-600 font-medium;
}

.efficiency-fair {
  @apply text-yellow-600 font-medium;
}

.efficiency-poor {
  @apply text-red-600 font-medium;
}

.alarms-cell {
  @apply flex items-center gap-1;
}

.alarms-count {
  @apply text-xs;
}

.no-alarms {
  @apply text-gray-400;
}

/* Pagination */
.pagination-section {
  @apply mt-6 flex justify-center;
}

/* Chart */
.chart-container {
  @apply h-96;
}

.performance-chart {
  @apply w-full h-full;
}

/* Responsive */
@media (max-width: 768px) {
  .summary-cards {
    @apply grid-cols-2;
  }
  
  .critical-grid {
    @apply grid-cols-1;
  }
  
  .equipment-grid {
    @apply grid-cols-1;
  }
  
  .content-header {
    @apply flex-col items-start;
  }
  
  .filters-section {
    @apply flex-wrap;
  }
}
</style>