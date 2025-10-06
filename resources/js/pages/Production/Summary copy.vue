<template>
  <div class="content">
    <content-header>
      <div class="me-2">
        <el-date-picker 
          v-model="selectedDate" 
          type="date" 
          :placeholder="$t('common.date')" 
          format="YYYY-MM-DD"
          value-format="YYYY-MM-DD" 
          @change="handleDateChange" 
          size="default" 
          class="date-picker" 
        />
      </div>
      <el-button @click="refreshData" circle :loading="loading" class="refresh-btn">
        <Icon icon="lucide:refresh-cw" class="w-4 h-4" />
      </el-button>
      <el-tooltip content="Export Production Report" placement="top">
        <el-button circle @click="exportReport">
          <Icon icon="lucide:download" class="w-4 h-4" />
        </el-button>
      </el-tooltip>
      <el-tooltip content="View Production Trends" placement="top">
        <el-button circle @click="openTrendsModal">
          <Icon icon="lucide:trending-up" class="w-4 h-4" />
        </el-button>
      </el-tooltip>
    </content-header>

    <!-- Alert Banner -->
    <el-row :gutter="20" class="mb-4" v-if="alerts.length > 0">
      <el-col :span="24">
        <div>
          <el-alert 
            v-for="alert in alerts" 
            :key="alert.id" 
            :title="alert.title" 
            :type="alert.type"
            effect="dark" 
            :description="alert.description" 
            show-icon 
            :closable="true" 
            class="alert-item mb-2" 
          />
        </div>
      </el-col>
    </el-row>

    <!-- Filters Section -->
    <div class="filters-section">
      <div class="filter-group">
        <label class="filter-label">{{ $t('production.summary.filters.date_range') }}</label>
        <div class="date-range-picker">
          <input 
            type="date" 
            v-model="filters.startDate" 
            class="date-input"
            @change="applyFilters"
          >
          <span class="date-separator">to</span>
          <input 
            type="date" 
            v-model="filters.endDate" 
            class="date-input"
            @change="applyFilters"
          >
        </div>
      </div>
      
      <div class="filter-group">
        <label class="filter-label">{{ $t('production.summary.filters.vessel') }}</label>
        <select v-model="filters.vesselId" @change="applyFilters" class="filter-select">
          <option value="">{{ $t('production.summary.filters.all_vessels') }}</option>
          <option v-for="vessel in vessels" :key="vessel.id" :value="vessel.id">
            {{ vessel.name }}
          </option>
        </select>
      </div>
      
      <div class="filter-group">
        <label class="filter-label">{{ $t('production.summary.filters.well_status') }}</label>
        <select v-model="filters.wellStatus" @change="applyFilters" class="filter-select">
          <option value="">{{ $t('production.summary.filters.all_wells') }}</option>
          <option value="active">{{ $t('production.summary.filters.active_wells') }}</option>
          <option value="shut_in">{{ $t('production.summary.filters.shut_in_wells') }}</option>
          <option value="testing">{{ $t('production.summary.filters.testing_wells') }}</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>{{ $t('common.loading') }}</p>
    </div>

    <!-- Main Content -->
    <div v-else class="main-content">
      <!-- Production KPI Cards -->
      <el-row :gutter="20">
        <!-- Oil Production KPI -->
        <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
          <el-card shadow="hover" class="kpi-card h-full">
            <div class="kpi-header">
              <div class="kpi-icon production-icon">
                <Icon icon="lucide:droplets" class="w-6 h-6" />
              </div>
              <div class="kpi-trend">
                <Icon icon="lucide:arrow-up" v-if="totalMetrics.oil_change >= 0" class="trend-up w-4 h-4" />
                <Icon icon="lucide:arrow-down" v-else class="trend-down w-4 h-4" />
                <span :class="totalMetrics.oil_change >= 0 ? 'trend-up-text' : 'trend-down-text'">
                  {{ Math.abs(totalMetrics.oil_change).toFixed(1) }}%
                </span>
              </div>
            </div>
            <div class="kpi-content">
              <h3 class="kpi-title">{{ $t('production.summary.metrics.oil_production') }}</h3>
              <div class="kpi-value">
                <span class="value-number">{{ formatNumber(totalMetrics.oil_bbl) }}</span>
                <span class="value-unit">BBL</span>
              </div>
              <div class="kpi-subtitle">
                {{ $t('production.summary.metrics.vs_yesterday') }}
              </div>
            </div>
          </el-card>
        </el-col>

        <!-- Gas Production KPI -->
        <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
          <el-card shadow="hover" class="kpi-card h-full">
            <div class="kpi-header">
              <div class="kpi-icon gas-icon">
                <Icon icon="lucide:flame" class="w-6 h-6" />
              </div>
              <div class="kpi-trend">
                <Icon icon="lucide:arrow-up" v-if="totalMetrics.gas_change >= 0" class="trend-up w-4 h-4" />
                <Icon icon="lucide:arrow-down" v-else class="trend-down w-4 h-4" />
                <span :class="totalMetrics.gas_change >= 0 ? 'trend-up-text' : 'trend-down-text'">
                  {{ Math.abs(totalMetrics.gas_change).toFixed(1) }}%
                </span>
              </div>
            </div>
            <div class="kpi-content">
              <h3 class="kpi-title">{{ $t('production.summary.metrics.gas_production') }}</h3>
              <div class="kpi-value">
                <span class="value-number">{{ formatNumber(totalMetrics.gas_mmscf) }}</span>
                <span class="value-unit">MMSCF</span>
              </div>
              <div class="kpi-subtitle">
                {{ $t('production.summary.metrics.vs_yesterday') }}
              </div>
            </div>
          </el-card>
        </el-col>

        <!-- Well Status KPI -->
        <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
          <el-card shadow="hover" class="kpi-card h-full">
            <div class="kpi-header">
              <div class="kpi-icon wells-icon">
                <Icon icon="lucide:zap" class="w-6 h-6" />
              </div>
              <div class="well-status">
                <div class="status-dot online"></div>
                <span class="status-label">{{ totalMetrics.active_wells }}/{{ totalMetrics.total_wells }}</span>
              </div>
            </div>
            <div class="kpi-content">
              <h3 class="kpi-title">{{ $t('production.summary.well_status') }}</h3>
              <div class="kpi-value">
                <span class="value-number">{{ totalMetrics.active_wells }}</span>
                <span class="value-unit">Active</span>
              </div>
              <div class="kpi-subtitle">
                {{ $t('production.summary.fields.active_wells') }}
              </div>
            </div>
          </el-card>
        </el-col>

        <!-- Production Efficiency KPI -->
        <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
          <el-card shadow="hover" class="kpi-card h-full">
            <div class="kpi-header">
              <div class="kpi-icon efficiency-icon">
                <Icon icon="lucide:gauge" class="w-6 h-6" />
              </div>
              <div class="efficiency-indicator">
                <div class="efficiency-status" :class="totalMetrics.avg_efficiency > 90 ? 'excellent' : totalMetrics.avg_efficiency > 80 ? 'good' : 'warning'">
                  {{ Math.round(totalMetrics.avg_efficiency) }}%
                </div>
              </div>
            </div>
            <div class="kpi-content">
              <h3 class="kpi-title">{{ $t('production.summary.metrics.production_efficiency') }}</h3>
              <div class="kpi-value">
                <span class="value-number">{{ formatPercentage(totalMetrics.avg_efficiency) }}</span>
                <span class="value-unit">%</span>
              </div>
              <div class="kpi-subtitle">
                {{ $t('production.summary.metrics.mtd_average') }}
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>

      <!-- Charts Section -->
      <el-row :gutter="20" class="mb-6">
        <el-col :xs="24" :sm="24" :md="16" class="mb-4">
          <el-card>
            <template #header>
              <div class="flex justify-between items-center">
                <span>{{ $t('production.summary.charts.production_overview') }}</span>
                <div class="flex">
                  <el-select v-model="selectedPeriod" @change="initCharts" class="min-w-28 me-2">
                    <el-option label="7 Days" value="7d" />
                    <el-option label="30 Days" value="30d" />
                    <el-option label="90 Days" value="90d" />
                  </el-select>
                  <el-button-group class="view-toggle flex">
                    <el-button type="primary">
                      <Icon icon="lucide:trending-up" class="w-4 h-4" />
                    </el-button>
                    <el-button type="default">
                      <Icon icon="lucide:bar-chart-3" class="w-4 h-4" />
                    </el-button>
                  </el-button-group>
                </div>
              </div>
            </template>
            <div class="h-[20rem]">
              <div ref="productionChartRef" class="w-full h-full"></div>
            </div>
            <div class="production-summary">
              <div class="summary-item">
                <span class="summary-label">Oil Production</span>
                <span class="summary-value oil">{{ formatNumber(totalMetrics.oil_bbl) }} BBL</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">Gas Production</span>
                <span class="summary-value gas">{{ formatNumber(totalMetrics.gas_mmscf) }} MMSCF</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">Water Cut</span>
                <span class="summary-value water">{{ formatPercentage(25.5) }}%</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">Efficiency</span>
                <span class="summary-value efficiency">{{ formatPercentage(totalMetrics.avg_efficiency) }}%</span>
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :xs="24" :sm="24" :md="8" class="mb-4">
          <el-card>
            <template #header>
              <div class="flex justify-between items-center">
                <span>{{ $t('production.summary.charts.efficiency_trend') }}</span>
                <router-link to="/production/wells" class="view-all-link">
                  {{ $t('common.view_all') }}
                  <Icon icon="lucide:arrow-right" class="w-4 h-4" />
                </router-link>
              </div>
            </template>
            <div class="h-[20rem]">
              <div ref="trendChartRef" class="w-full h-full"></div>
            </div>
            <div class="efficiency-legend">
              <div class="legend-item oil">
                <div class="legend-dot"></div>
                <span class="legend-label">Oil Efficiency ({{ formatPercentage(totalMetrics.avg_efficiency) }}%)</span>
              </div>
              <div class="legend-item gas">
                <div class="legend-dot"></div>
                <span class="legend-label">Gas Efficiency (95.2%)</span>
              </div>
              <div class="legend-item uptime">
                <div class="legend-dot"></div>
                <span class="legend-label">Uptime (98.5%)</span>
              </div>
            </div>
          </el-card>
        </el-col>
      </el-row>

      <!-- Summary Table -->
      <el-card>
        <template #header>
          <div class="flex justify-between items-center">
            <span>{{ $t('production.summary.daily_overview') }}</span>
            <div class="flex gap-2">
              <el-button @click="refreshData" :loading="loading">
                <Icon icon="lucide:refresh-cw" class="w-4 h-4 mr-2" />
                {{ $t('common.refresh') }}
              </el-button>
              <el-button type="primary" @click="generateReport">
                <Icon icon="lucide:download" class="w-4 h-4 mr-2" />
                {{ $t('production.summary.actions.generate_report') }}
              </el-button>
            </div>
          </div>
        </template>
        
        <el-table 
          :data="summaryData" 
          v-loading="loading"
          stripe
          class="w-full"
          :default-sort="{ prop: 'summary_date', order: 'descending' }"
        >
          <el-table-column 
            prop="summary_date" 
            :label="$t('production.summary.fields.summary_date')"
            width="120"
            sortable
          >
            <template #default="{ row }">
              <span class="font-medium">{{ formatDate(row.summary_date) }}</span>
            </template>
          </el-table-column>
          
          <el-table-column 
            prop="vessel_name" 
            :label="$t('production.summary.fields.vessel_name')"
            width="140"
          >
            <template #default="{ row }">
              <div class="flex items-center">
                <Icon icon="lucide:ship" class="w-4 h-4 text-blue-500 mr-2" />
                <span class="font-medium">{{ row.vessel_name }}</span>
              </div>
            </template>
          </el-table-column>
          
          <el-table-column 
            prop="gross_oil_bbl" 
            :label="$t('production.summary.fields.gross_oil_bbl')"
            width="140"
            sortable
          >
            <template #default="{ row }">
              <div class="flex items-center">
                <Icon icon="lucide:droplets" class="w-4 h-4 text-amber-500 mr-2" />
                <span class="font-semibold text-amber-600">{{ formatNumber(row.gross_oil_bbl) }}</span>
                <span class="text-xs text-gray-500 ml-1">BBL</span>
              </div>
            </template>
          </el-table-column>
          
          <el-table-column 
            prop="export_gas_mmscf" 
            :label="$t('production.summary.fields.export_gas_mmscf')"
            width="140"
            sortable
          >
            <template #default="{ row }">
              <div class="flex items-center">
                <Icon icon="lucide:flame" class="w-4 h-4 text-blue-500 mr-2" />
                <span class="font-semibold text-blue-600">{{ formatNumber(row.export_gas_mmscf) }}</span>
                <span class="text-xs text-gray-500 ml-1">MMSCF</span>
              </div>
            </template>
          </el-table-column>
          
          <el-table-column 
            prop="water_cut_pct" 
            :label="$t('production.summary.fields.water_cut_pct')"
            width="120"
            sortable
          >
            <template #default="{ row }">
              <el-tag 
                :type="getWaterCutTagType(row.water_cut_pct)"
                size="small"
              >
                {{ formatPercentage(row.water_cut_pct) }}%
              </el-tag>
            </template>
          </el-table-column>
          
          <el-table-column 
            prop="oil_efficiency_pct" 
            :label="$t('production.summary.fields.oil_efficiency_pct')"
            width="120"
            sortable
          >
            <template #default="{ row }">
              <el-tag 
                :type="getEfficiencyTagType(row.oil_efficiency_pct)"
                size="small"
              >
                {{ formatPercentage(row.oil_efficiency_pct) }}%
              </el-tag>
            </template>
          </el-table-column>
          
          <el-table-column 
            prop="calculation_status" 
            :label="$t('production.summary.fields.calculation_status')"
            width="120"
          >
            <template #default="{ row }">
              <el-tag 
                :type="getStatusTagType(row.calculation_status)"
                size="small"
              >
                {{ $t(`production.summary.status.${row.calculation_status.toLowerCase()}`) }}
              </el-tag>
            </template>
          </el-table-column>
          
          <el-table-column 
            :label="$t('common.actions')"
            width="140"
            align="center"
            fixed="right"
          >
            <template #default="{ row }">
              <div class="flex justify-center gap-1">
                <el-button 
                  size="small" 
                  type="primary" 
                  @click="viewDetails(row)"
                  circle
                >
                  <Icon icon="lucide:eye" class="w-4 h-4" />
                </el-button>
                <el-button 
                  v-if="row.calculation_status === 'Completed'"
                  size="small" 
                  type="success" 
                  @click="approveData(row)"
                  circle
                >
                  <Icon icon="lucide:check" class="w-4 h-4" />
                </el-button>
                <el-button 
                  size="small" 
                  type="warning" 
                  @click="recalculate(row)"
                  circle
                >
                  <Icon icon="lucide:refresh-cw" class="w-4 h-4" />
                </el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
        
        <!-- Empty State -->
        <div v-if="summaryData.length === 0 && !loading" class="flex flex-col items-center justify-center py-12">
          <Icon icon="lucide:database" class="w-16 h-16 text-gray-300 mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('common.no_data') }}</h3>
          <p class="text-gray-500 text-center max-w-md">{{ $t('production.summary.description') }}</p>
        </div>
      </el-card>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed, nextTick, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'
import * as echarts from 'echarts'
import { ElMessage, ElMessageBox } from 'element-plus'

export default {
  name: 'ProductionSummary',
  setup() {
    const { t } = useI18n()
    
    // Reactive data
    const loading = ref(false)
    const summaryData = ref([])
    const vessels = ref([])
    const selectedDate = ref(new Date())
    const selectedPeriod = ref('7d')
    const currentPage = ref(1)
    const pageSize = ref(20)
    const totalRecords = ref(0)
    const alerts = ref([])
    
    // Chart refs
    const productionChartRef = ref(null)
    const trendChartRef = ref(null)
    
    // Chart instances
    let productionChart = null
    let trendChart = null
    
    // Filters
    const filters = reactive({
      vessel_id: '',
      date_from: '',
      date_to: '',
      status: ''
    })
    
    // Chart periods
    const chartPeriods = [
      { value: '7d', label: '7D' },
      { value: '30d', label: '30D' },
      { value: '90d', label: '90D' },
      { value: '1y', label: '1Y' }
    ]
    
    // Computed metrics
    const totalMetrics = computed(() => {
      if (summaryData.value.length === 0) {
        return {
          oil_bbl: 0,
          gas_mmscf: 0,
          active_wells: 0,
          total_wells: 0,
          avg_efficiency: 0,
          oil_change: 0,
          gas_change: 0
        }
      }
      
      const latest = summaryData.value[0] || {}
      const previous = summaryData.value[1] || {}
      
      // Calculate percentage changes
      const oilChange = previous.gross_oil_bbl ? 
        ((latest.gross_oil_bbl - previous.gross_oil_bbl) / previous.gross_oil_bbl) * 100 : 0
      const gasChange = previous.export_gas_mmscf ? 
        ((latest.export_gas_mmscf - previous.export_gas_mmscf) / previous.export_gas_mmscf) * 100 : 0
      
      return {
        oil_bbl: latest.gross_oil_bbl || 0,
        gas_mmscf: latest.export_gas_mmscf || 0,
        active_wells: latest.active_wells || 0,
        total_wells: latest.total_wells || 0,
        avg_efficiency: latest.oil_efficiency_pct || 0,
        oil_change: oilChange,
        gas_change: gasChange
      }
    })
    
    // Methods
    const loadSummaryData = async () => {
      loading.value = true
      try {
        // Mock data - replace with actual API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        summaryData.value = [
          {
            id: 1,
            vessel_id: 1,
            vessel_name: 'FPSO Belanak',
            summary_date: '2024-01-15',
            total_wells: 12,
            active_wells: 11,
            gross_oil_bbl: 8450.5,
            net_oil_bbl: 8200.3,
            oil_efficiency_pct: 97.1,
            gross_gas_mmscf: 15.8,
            export_gas_mmscf: 14.2,
            gas_efficiency_pct: 89.9,
            total_water_bbl: 2150.0,
            water_cut_pct: 20.3,
            production_uptime_pct: 98.5,
            oil_deferment_bbl: 250.2,
            gas_deferment_mmscf: 1.6,
            calculation_status: 'Completed',
            created_at: '2024-01-15T23:59:59Z'
          },
          {
            id: 2,
            vessel_id: 1,
            vessel_name: 'FPSO Belanak',
            summary_date: '2024-01-14',
            total_wells: 12,
            active_wells: 10,
            gross_oil_bbl: 7890.2,
            net_oil_bbl: 7650.8,
            oil_efficiency_pct: 96.9,
            gross_gas_mmscf: 14.5,
            export_gas_mmscf: 13.1,
            gas_efficiency_pct: 90.3,
            total_water_bbl: 1980.5,
            water_cut_pct: 20.1,
            production_uptime_pct: 95.2,
            oil_deferment_bbl: 239.4,
            gas_deferment_mmscf: 1.4,
            calculation_status: 'Completed',
            created_at: '2024-01-14T23:59:59Z'
          },
          {
            id: 3,
            vessel_id: 1,
            vessel_name: 'FPSO Belanak',
            summary_date: '2024-01-13',
            total_wells: 12,
            active_wells: 12,
            gross_oil_bbl: 9120.8,
            net_oil_bbl: 8890.4,
            oil_efficiency_pct: 97.5,
            gross_gas_mmscf: 16.2,
            export_gas_mmscf: 14.8,
            gas_efficiency_pct: 91.4,
            total_water_bbl: 2280.3,
            water_cut_pct: 20.0,
            production_uptime_pct: 99.1,
            oil_deferment_bbl: 230.4,
            gas_deferment_mmscf: 1.4,
            calculation_status: 'Completed',
            created_at: '2024-01-13T23:59:59Z'
          },
          {
            id: 4,
            vessel_id: 1,
            vessel_name: 'FPSO Belanak',
            summary_date: '2024-01-12',
            total_wells: 12,
            active_wells: 11,
            gross_oil_bbl: 8750.3,
            net_oil_bbl: 8520.1,
            oil_efficiency_pct: 97.4,
            gross_gas_mmscf: 15.5,
            export_gas_mmscf: 14.0,
            gas_efficiency_pct: 90.3,
            total_water_bbl: 2050.8,
            water_cut_pct: 19.0,
            production_uptime_pct: 97.8,
            oil_deferment_bbl: 230.2,
            gas_deferment_mmscf: 1.5,
            calculation_status: 'Pending',
            created_at: '2024-01-12T23:59:59Z'
          },
          {
            id: 5,
            vessel_id: 1,
            vessel_name: 'FPSO Belanak',
            summary_date: '2024-01-11',
            total_wells: 12,
            active_wells: 9,
            gross_oil_bbl: 7250.5,
            net_oil_bbl: 7050.2,
            oil_efficiency_pct: 97.2,
            gross_gas_mmscf: 13.8,
            export_gas_mmscf: 12.5,
            gas_efficiency_pct: 90.6,
            total_water_bbl: 1850.3,
            water_cut_pct: 20.3,
            production_uptime_pct: 92.5,
            oil_deferment_bbl: 200.3,
            gas_deferment_mmscf: 1.3,
            calculation_status: 'Error',
            created_at: '2024-01-11T23:59:59Z'
          }
        ]
        
        // Initialize charts after data is loaded
        await nextTick()
        initCharts()
      } catch (error) {
        console.error('Error loading summary data:', error)
      } finally {
        loading.value = false
      }
    }
    
    const loadVessels = async () => {
      try {
        // Mock data - replace with actual API call
        vessels.value = [
          { id: 1, name: 'FPSO Neptune' },
          { id: 2, name: 'FPSO Poseidon' }
        ]
      } catch (error) {
        console.error('Error loading vessels:', error)
      }
    }
    
    const refreshData = async () => {
      loading.value = true
      try {
        await loadSummaryData()
        ElMessage.success(t('common.data_refreshed'))
      } catch (error) {
        console.error('Error refreshing data:', error)
        ElMessage.error(t('common.error_occurred'))
      } finally {
        loading.value = false
      }
    }
    
    const exportSummary = async () => {
      try {
        loading.value = true
        // TODO: Implement actual export functionality
        await new Promise(resolve => setTimeout(resolve, 2000))
        ElMessage.success(t('production.summary.export_success'))
      } catch (error) {
        ElMessage.error(t('production.summary.export_error'))
      } finally {
        loading.value = false
      }
    }
    
    const generateReport = async () => {
      try {
        loading.value = true
        // TODO: Generate DVR report
        await new Promise(resolve => setTimeout(resolve, 3000))
        ElMessage.success(t('production.summary.report_generated'))
      } catch (error) {
        ElMessage.error(t('production.summary.report_error'))
      } finally {
        loading.value = false
      }
    }
    
    const viewDetails = (summary) => {
      // TODO: Navigate to detail view or open modal
      console.log('Viewing details for:', summary)
      ElMessage.info(`Viewing details for ${formatDate(summary.summary_date)}`)
    }
    
    const approveData = async (summary) => {
      try {
        await ElMessageBox.confirm(
          t('production.summary.confirm_approve'),
          t('common.confirm'),
          {
            confirmButtonText: t('common.approve'),
            cancelButtonText: t('common.cancel'),
            type: 'warning'
          }
        )
        
        // TODO: Approve production data via API
        summary.calculation_status = 'Approved'
        ElMessage.success(t('production.summary.data_approved'))
      } catch (error) {
        if (error !== 'cancel') {
          ElMessage.error(t('common.error_occurred'))
        }
      }
    }
    
    const recalculate = async (summary) => {
      try {
        await ElMessageBox.confirm(
          t('production.summary.confirm_recalculate'),
          t('common.confirm'),
          {
            confirmButtonText: t('common.recalculate'),
            cancelButtonText: t('common.cancel'),
            type: 'warning'
          }
        )
        
        loading.value = true
        // TODO: Recalculate production summary via API
        await new Promise(resolve => setTimeout(resolve, 2000))
        summary.calculation_status = 'Pending'
        ElMessage.success(t('production.summary.recalculation_started'))
      } catch (error) {
        if (error !== 'cancel') {
          ElMessage.error(t('common.error_occurred'))
        }
      } finally {
        loading.value = false
      }
    }
    
    // Additional action methods
    const handleDateChange = (date) => {
      selectedDate.value = date
      loadSummaryData()
    }
    
    const applyFilters = () => {
      loadSummaryData()
    }
    
    const exportReport = () => {
      exportSummary()
    }
    
    const openTrendsModal = () => {
      // TODO: Open trends modal
      ElMessage.info('Opening production trends modal')
    }
    
    // Chart initialization functions
    const initCharts = () => {
      initProductionChart()
      initTrendChart()
    }
    
    const initProductionChart = () => {
      if (!productionChartRef.value) return
      
      productionChart = echarts.init(productionChartRef.value)
      
      const latestData = summaryData.value[0] || {}
      
      const option = {
        title: {
          text: 'Current Production Overview',
          left: 'center',
          textStyle: {
            fontSize: 16,
            fontWeight: 'bold',
            color: '#1a1a1a'
          }
        },
        tooltip: {
          trigger: 'item',
          formatter: '{a} <br/>{b}: {c} ({d}%)'
        },
        legend: {
          orient: 'vertical',
          left: 'left',
          top: 'middle'
        },
        series: [
          {
            name: 'Production',
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['60%', '50%'],
            data: [
              { 
                value: latestData.gross_oil_bbl || 0, 
                name: 'Oil (BBL)', 
                itemStyle: { color: '#2563eb' } 
              },
              { 
                value: (latestData.export_gas_mmscf || 0) * 1000, 
                name: 'Gas (MSCF)', 
                itemStyle: { color: '#d97706' } 
              },
              { 
                value: (latestData.gross_oil_bbl || 0) * (latestData.water_cut_pct || 0) / 100, 
                name: 'Water (BBL)', 
                itemStyle: { color: '#059669' } 
              }
            ],
            emphasis: {
              itemStyle: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
              }
            }
          }
        ]
      }
      
      productionChart.setOption(option)
    }
    
    const initTrendChart = () => {
      if (!trendChartRef.value) return
      
      trendChart = echarts.init(trendChartRef.value)
      
      const dates = summaryData.value.map(item => formatDate(item.summary_date)).reverse()
      const oilData = summaryData.value.map(item => item.gross_oil_bbl).reverse()
      const gasData = summaryData.value.map(item => item.export_gas_mmscf).reverse()
      const efficiencyData = summaryData.value.map(item => item.oil_efficiency_pct).reverse()
      
      const option = {
        title: {
          text: 'Production Trend (7 Days)',
          left: 'center',
          textStyle: {
            fontSize: 16,
            fontWeight: 'bold',
            color: '#1a1a1a'
          }
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'cross'
          }
        },
        legend: {
          data: ['Oil Production', 'Gas Production', 'Efficiency'],
          top: 30
        },
        grid: {
          left: '3%',
          right: '4%',
          bottom: '3%',
          containLabel: true
        },
        xAxis: {
          type: 'category',
          boundaryGap: false,
          data: dates
        },
        yAxis: [
          {
            type: 'value',
            name: 'Production',
            position: 'left',
            axisLabel: {
              formatter: '{value}'
            }
          },
          {
            type: 'value',
            name: 'Efficiency (%)',
            position: 'right',
            min: 80,
            max: 100,
            axisLabel: {
              formatter: '{value}%'
            }
          }
        ],
        series: [
          {
            name: 'Oil Production',
            type: 'line',
            yAxisIndex: 0,
            data: oilData,
            smooth: true,
            itemStyle: {
              color: '#2563eb'
            },
            areaStyle: {
              color: 'rgba(37, 99, 235, 0.1)'
            }
          },
          {
            name: 'Gas Production',
            type: 'line',
            yAxisIndex: 0,
            data: gasData,
            smooth: true,
            itemStyle: {
              color: '#d97706'
            },
            areaStyle: {
              color: 'rgba(217, 119, 6, 0.1)'
            }
          },
          {
            name: 'Efficiency',
            type: 'line',
            yAxisIndex: 1,
            data: efficiencyData,
            smooth: true,
            itemStyle: {
              color: '#7c3aed'
            }
          }
        ]
      }
      
      trendChart.setOption(option)
    }
    
    // Resize charts function
    const resizeCharts = () => {
      if (productionChart) productionChart.resize()
      if (trendChart) trendChart.resize()
    }
    
    // Cleanup function
    const cleanup = () => {
      if (productionChart) {
        productionChart.dispose()
        productionChart = null
      }
      if (trendChart) {
        trendChart.dispose()
        trendChart = null
      }
      window.removeEventListener('resize', resizeCharts)
    }
    
    // Utility functions
    const formatNumber = (value) => {
      if (!value) return '0'
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(value)
    }
    
    const formatPercentage = (value) => {
      if (!value) return '0.00'
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(value)
    }
    
    const formatDate = (date) => {
      if (!date) return '-'
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }
    
    const getChangeClass = (change) => {
      if (change > 0) return 'positive'
      if (change < 0) return 'negative'
      return 'neutral'
    }
    
    const getChangeIcon = (change) => {
      if (change > 0) return 'arrow-up-24-regular'
      if (change < 0) return 'arrow-down-24-regular'
      return 'subtract-24-regular'
    }
    
    // Tag type helpers
    const getEfficiencyTagType = (efficiency) => {
      if (efficiency >= 95) return 'success'
      if (efficiency >= 90) return 'warning'
      return 'danger'
    }
    
    const getUptimeTagType = (uptime) => {
      if (uptime >= 98) return 'success'
      if (uptime >= 95) return 'warning'
      return 'danger'
    }
    
    const getWaterCutTagType = (waterCut) => {
      if (waterCut <= 15) return 'success'
      if (waterCut <= 25) return 'warning'
      return 'danger'
    }
    
    const getStatusTagType = (status) => {
      switch (status.toLowerCase()) {
        case 'completed': return 'success'
        case 'pending': return 'warning'
        case 'error': return 'danger'
        default: return 'info'
      }
    }
    
    // Lifecycle
    onMounted(async () => {
      await loadVessels()
      await loadSummaryData()
      
      // Add resize listener
      window.addEventListener('resize', resizeCharts)
    })
    
    onBeforeUnmount(() => {
      cleanup()
    })
    
    return {
      // Data
      loading,
      selectedDate,
      selectedPeriod,
      summaryData,
      vessels,
      filters,
      chartPeriods,
      currentPage,
      pageSize,
      totalRecords,
      alerts,
      
      // Computed
      totalMetrics,
      
      // Chart refs
      productionChartRef,
      trendChartRef,
      
      // Methods
      loadSummaryData,
      refreshData,
      exportData: exportSummary,
      generateReport,
      viewDetails,
      approveData,
      recalculate,
      handleDateChange,
      handleSizeChange: (newSize) => {
        pageSize.value = newSize
        currentPage.value = 1
        loadSummaryData()
      },
      handleCurrentChange: (newPage) => {
        currentPage.value = newPage
        loadSummaryData()
      },
      applyFilters,
      exportReport,
      openTrendsModal,
      initCharts,
      
      // Utility functions
      formatNumber,
      formatPercentage,
      formatDate,
      getChangeClass,
      getChangeIcon,
      getEfficiencyTagType,
      getUptimeTagType,
      getWaterCutTagType,
      getStatusTagType
    }
  }
}
</script>

<style scoped>
/* Production Summary Styles */
.content {
  @apply p-6 bg-gray-50 min-h-screen;
}

/* Filters Section */
.filters-section {
  @apply bg-white rounded-lg p-6 mb-6 shadow-sm;
}

.filter-group {
  @apply flex flex-col gap-2;
}

.filter-label {
  @apply text-sm font-medium text-gray-700;
}

.date-range-picker {
  @apply flex items-center gap-2;
}

.date-input {
  @apply px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
}

.date-separator {
  @apply text-gray-500 text-sm;
}

.filter-select {
  @apply px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
}

/* KPI Cards */
.kpi-card {
  @apply bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow;
}

.kpi-header {
  @apply flex justify-between items-start mb-4;
}

.kpi-icon {
  @apply w-12 h-12 rounded-lg flex items-center justify-center;
}

.production-icon {
  @apply bg-amber-100 text-amber-600;
}

.gas-icon {
  @apply bg-blue-100 text-blue-600;
}

.wells-icon {
  @apply bg-green-100 text-green-600;
}

.efficiency-icon {
  @apply bg-purple-100 text-purple-600;
}

.kpi-trend {
  @apply flex items-center gap-1;
}

.trend-up {
  @apply text-green-500;
}

.trend-down {
  @apply text-red-500;
}

.trend-up-text {
  @apply text-green-600 text-sm font-medium;
}

.trend-down-text {
  @apply text-red-600 text-sm font-medium;
}

.kpi-content {
  @apply space-y-2;
}

.kpi-title {
  @apply text-sm font-medium text-gray-600;
}

.kpi-value {
  @apply flex items-baseline gap-2;
}

.value-number {
  @apply text-2xl font-bold text-gray-900;
}

.value-unit {
  @apply text-sm text-gray-500;
}

.kpi-subtitle {
  @apply text-xs text-gray-500;
}

.well-status {
  @apply flex items-center gap-2;
}

.status-dot {
  @apply w-2 h-2 rounded-full;
}

.status-dot.online {
  @apply bg-green-500;
}

.status-label {
  @apply text-sm font-medium text-gray-700;
}

.efficiency-indicator {
  @apply flex items-center;
}

.efficiency-status {
  @apply px-2 py-1 rounded-full text-xs font-medium;
}

.efficiency-status.excellent {
  @apply bg-green-100 text-green-800;
}

.efficiency-status.good {
  @apply bg-yellow-100 text-yellow-800;
}

.efficiency-status.warning {
  @apply bg-red-100 text-red-800;
}

/* Production Summary */
.production-summary {
  @apply mt-4 space-y-2;
}

.summary-item {
  @apply flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0;
}

.summary-label {
  @apply text-sm text-gray-600 font-medium;
}

.summary-value {
  @apply text-sm font-semibold;
}

.summary-value.oil {
  @apply text-amber-600;
}

.summary-value.gas {
  @apply text-blue-600;
}

.summary-value.water {
  @apply text-cyan-600;
}

.summary-value.efficiency {
  @apply text-green-600;
}

/* Efficiency Legend */
.efficiency-legend {
  @apply mt-4 space-y-2;
}

.legend-item {
  @apply flex items-center;
}

.legend-dot {
  @apply w-3 h-3 rounded-full mr-2;
}

.legend-item.oil .legend-dot {
  @apply bg-amber-500;
}

.legend-item.gas .legend-dot {
  @apply bg-blue-500;
}

.legend-item.uptime .legend-dot {
  @apply bg-green-500;
}

.legend-label {
  @apply text-xs text-gray-600;
}

/* View All Link */
.view-all-link {
  @apply flex items-center text-sm text-blue-600 hover:text-blue-800 transition-colors;
}

/* Chart Container */
.chart-container {
  @apply h-80;
}

/* Element Plus Customizations */
:deep(.el-table) {
  @apply border-0;
}

:deep(.el-table th) {
  @apply bg-gray-50 text-gray-700 font-semibold;
}

:deep(.el-table td) {
  @apply border-b border-gray-100;
}

:deep(.el-table .el-table__row:hover > td) {
  @apply bg-blue-50;
}

:deep(.el-card__header) {
  @apply bg-gray-50 border-b border-gray-200;
}

:deep(.el-card__body) {
  @apply p-6;
}

:deep(.el-button--primary) {
  @apply bg-blue-600 border-blue-600 hover:bg-blue-700 hover:border-blue-700;
}

:deep(.el-button--warning) {
  @apply bg-amber-500 border-amber-500 hover:bg-amber-600 hover:border-amber-600;
}

:deep(.el-button--success) {
  @apply bg-green-600 border-green-600 hover:bg-green-700 hover:border-green-700;
}

:deep(.el-tag--success) {
  @apply bg-green-100 text-green-800 border-green-200;
}

:deep(.el-tag--warning) {
  @apply bg-amber-100 text-amber-800 border-amber-200;
}

:deep(.el-tag--danger) {
  @apply bg-red-100 text-red-800 border-red-200;
}

:deep(.el-tag--info) {
  @apply bg-gray-100 text-gray-800 border-gray-200;
}

:deep(.el-pagination) {
  @apply justify-end;
}

/* Responsive Design */
@media (max-width: 768px) {
  .content {
    @apply p-4;
  }
  
  :deep(.el-table) {
    @apply text-sm;
  }
  
  :deep(.el-card__body) {
    @apply p-4;
  }
  
  .chart-container {
    @apply h-64;
  }
}

@media (max-width: 640px) {
  :deep(.el-table .el-table__cell) {
    @apply px-2 py-3;
  }
  
  :deep(.el-button) {
    @apply px-2 py-1;
  }
  
  .chart-container {
    @apply h-48;
  }
}
</style>
