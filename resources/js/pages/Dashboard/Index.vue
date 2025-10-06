<template>
  <div class="content">
    <content-header>
      <div class="me-2">

        <el-date-picker v-model="selectedDate" type="date" :placeholder="$t('common.date')" format="YYYY-MM-DD"
          value-format="YYYY-MM-DD" @change="handleDateChange" size="default" class="date-picker" />
      </div>
      <el-button @click="refreshDashboard" circle :loading="loading" class="refresh-btn">
        <Icon icon="lucide:refresh-cw" class="w-4 h-4" />
      </el-button>
      <el-tooltip content="Export KPI Report" placement="top">
        <el-button circle @click="exportKPI">
          <Icon icon="lucide:download" class="w-4 h-4" />
        </el-button>
      </el-tooltip>
      <el-tooltip content="Compare Vessels" placement="top" v-if="selectedVessel === 'all'">
        <el-button circle @click="openCompareModal">
          <Icon icon="lucide:bar-chart-3" class="w-4 h-4" />
        </el-button>
      </el-tooltip>
    </content-header>

    <!-- Alert Banner -->
    <el-row :gutter="20" class="mb-4">
      <el-col :span="24">
        <div v-if="alerts.length > 0">
          <el-alert v-for="alert in filteredAlerts" :key="alert.id" :title="alert.title" :type="alert.type"
            effect="dark" :description="alert.description" show-icon :closable="true" class="alert-item" />
        </div>
      </el-col>
    </el-row>

    <el-row :gutter="20">
      <!-- Production KPI -->
      <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
        <el-card shadow="hover" class="kpi-card h-full">
          <div class="kpi-header">
            <div class="kpi-icon production-icon">
              <Icon icon="lucide:trending-up" class="w-6 h-6" />
            </div>
            <div class="kpi-trend">
              <Icon icon="lucide:arrow-up" v-if="kpiData.productionTrend >= 0" class="trend-up w-4 h-4" />
              <Icon icon="lucide:arrow-down" v-else class="trend-down w-4 h-4" />
              <span :class="kpiData.productionTrend >= 0 ? 'trend-up-text' : 'trend-down-text'">
                {{ Math.abs(kpiData.productionTrend) }}%
              </span>
            </div>
          </div>
          <div class="kpi-content">
            <h3 class="kpi-title">
              {{ selectedVessel === 'all' ? $t('dashboard.kpi.fleet_production') : $t('dashboard.kpi.production') }}
            </h3>
            <div class="kpi-value">
              <span class="value-number">{{ formatNumber(kpiData.totalOilProduction) }}</span>
              <span class="value-unit">BBL/day</span>
            </div>
            <div class="kpi-subtitle">
              vs {{ formatNumber(kpiData.targetProduction) }} target
              <span v-if="selectedVessel === 'all'" class="vessel-count">({{ vessels.length }} vessels)</span>
            </div>
          </div>
          <div class="kpi-progress">
            <div class="progress-bar">
              <div class="progress-fill production-progress"
                :style="{ width: (kpiData.totalOilProduction / kpiData.targetProduction * 100) + '%' }"></div>
            </div>
          </div>
        </el-card>
      </el-col>

      <!-- Equipment Availability KPI -->
      <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
        <el-card shadow="hover" class="kpi-card h-full">
          <div class="kpi-header">
            <div class="kpi-icon equipment-icon">
              <Icon icon="lucide:settings" class="w-6 h-6" />
            </div>
            <div class="equipment-status">
              <div class="status-dot online"></div>
              <span class="status-label">{{ kpiData.runningEquipment }}/{{ kpiData.totalEquipment }}</span>
            </div>
          </div>
          <div class="kpi-content">
            <h3 class="kpi-title">{{ $t('dashboard.kpi.equipment_availability') }}</h3>
            <div class="kpi-value">
              <span class="value-number">{{ kpiData.equipmentAvailability }}</span>
              <span class="value-unit">%</span>
            </div>
            <div class="kpi-subtitle">
              {{ kpiData.criticalEquipment }} critical systems
              <span v-if="selectedVessel === 'all'" class="vessel-count">(fleet-wide)</span>
            </div>
          </div>
          <div class="kpi-progress">
            <div class="progress-bar">
              <div class="progress-fill equipment-progress" :style="{ width: kpiData.equipmentAvailability + '%' }">
              </div>
            </div>
          </div>
        </el-card>
      </el-col>

      <!-- Safety Performance KPI -->
      <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
        <el-card shadow="hover" class="kpi-card h-full">
          <div class="kpi-header">
            <div class="kpi-icon safety-icon">
              <Icon icon="lucide:shield-check" class="w-6 h-6" />
            </div>
            <div class="safety-badge">
              <span class="safety-label">LTIF: {{ kpiData.ltif }}</span>
            </div>
          </div>
          <div class="kpi-content">
            <h3 class="kpi-title">{{ $t('dashboard.kpi.safety_performance') }}</h3>
            <div class="kpi-value">
              <span class="value-number">{{ kpiData.daysWithoutLTI }}</span>
              <span class="value-unit">days</span>
            </div>
            <div class="kpi-subtitle">
              without Lost Time Incident
              <span v-if="selectedVessel === 'all'" class="vessel-count">(fleet record)</span>
            </div>
          </div>
          <div class="safety-milestone">
            <div class="milestone-progress">
              <div class="milestone-bar" :style="{ width: (kpiData.daysWithoutLTI / 365 * 100) + '%' }"></div>
            </div>
            <span class="milestone-text">Target: 365 days</span>
          </div>
        </el-card>
      </el-col>

      <!-- Personnel KPI -->
      <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
        <el-card shadow="hover" class="kpi-card h-full">
          <div class="kpi-header">
            <div class="kpi-icon personnel-icon">
              <Icon icon="lucide:users" class="w-6 h-6" />
            </div>
            <div class="pob-indicator">
              <div class="pob-status" :class="kpiData.totalPOB > kpiData.maxPOB * 0.9 ? 'warning' : 'normal'">
                {{ Math.round(kpiData.totalPOB / kpiData.maxPOB * 100) }}%
              </div>
            </div>
          </div>
          <div class="kpi-content">
            <h3 class="kpi-title">{{ $t('dashboard.kpi.personnel_onboard') }}</h3>
            <div class="kpi-value">
              <span class="value-number">{{ kpiData.totalPOB }}</span>
              <span class="value-unit">/ {{ kpiData.maxPOB }}</span>
            </div>
            <div class="kpi-subtitle">
              Personnel on Board
              <span v-if="selectedVessel === 'all'" class="vessel-count">(all vessels)</span>
            </div>
          </div>
          <div class="pob-breakdown">
            <div class="breakdown-item">
              <span class="breakdown-label">Company</span>
              <span class="breakdown-value">{{ kpiData.companyPOB }}</span>
            </div>
            <div class="breakdown-item">
              <span class="breakdown-label">Contractor</span>
              <span class="breakdown-value">{{ kpiData.contractorPOB }}</span>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- Charts Row -->
    <el-row :gutter="20" class="mb-6">
      <el-col :xs="24" :sm="24" :md="16" class="mb-4">
        <el-card>
          <template #header>
            <div class="flex justify-between items-center">
              <span>{{ selectedVessel === 'all' ? $t('dashboard.widgets.fleet_production_summary') :
                $t('dashboard.widgets.production_summary') }}</span>
              <div class="flex">
                <el-select v-model="productionPeriod" @change="updateProductionChart"
                  class="min-w-28 me-2">
                  <el-option label="7 Days" value="7d" />
                  <el-option label="30 Days" value="30d" />
                  <el-option label="90 Days" value="90d" />
                </el-select>
                <el-button-group class="view-toggle flex">
                  <el-button :type="chartType === 'line' ? 'primary' : 'default'"
                    @click="chartType = 'line'">
                    <Icon icon="lucide:trending-up" class="w-4 h-4" />
                  </el-button>
                  <el-button :type="chartType === 'bar' ? 'primary' : 'default'"
                    @click="chartType = 'bar'">
                    <Icon icon="lucide:bar-chart-3" class="w-4 h-4" />
                  </el-button>
                </el-button-group>
              </div>
            </div>
          </template>
          <div class="h-[20rem]">
            <v-chart :option="productionChartOption" class="w-full h-full" />
          </div>
          <div class="production-summary">
              <div class="summary-item">
                  <span class="summary-label">Oil Production</span>
                  <span class="summary-value oil">{{ formatNumber(productionSummary.oil) }} BBL</span>
              </div>
              <div class="summary-item">
                  <span class="summary-label">Gas Production</span>
                  <span class="summary-value gas">{{ formatNumber(productionSummary.gas) }} MMSCF</span>
              </div>
              <div class="summary-item">
                  <span class="summary-label">Water Cut</span>
                  <span class="summary-value water">{{ productionSummary.waterCut }}%</span>
              </div>
              <div class="summary-item">
                  <span class="summary-label">Efficiency</span>
                  <span class="summary-value efficiency">{{ productionSummary.efficiency }}%</span>
              </div>
          </div>
        </el-card>
      </el-col>
      <el-col :xs="24" :sm="24" :md="8" class="mb-4">
        <el-card>
          <template #header>
            <div class="flex justify-between items-center">
              <span>{{ selectedVessel === 'all' ? $t('dashboard.widgets.fleet_equipment_status') :
                $t('dashboard.widgets.equipment_status') }}</span>
              <router-link to="/equipment/status" class="view-all-link">
                {{ $t('actions.view_all') }}
                <Icon icon="lucide:arrow-right" class="w-4 h-4" />
              </router-link>
            </div>
          </template>
          <div class="h-[20rem]">
            <v-chart :option="equipmentChartOption" class="w-full h-full" />
          </div>
          <div class="equipment-legend">
            <div class="legend-item running">
              <div class="legend-dot"></div>
              <span class="legend-label">Running ({{ equipmentStatus.running }})</span>
            </div>
            <div class="legend-item standby">
              <div class="legend-dot"></div>
              <span class="legend-label">Standby ({{ equipmentStatus.standby }})</span>
            </div>
            <div class="legend-item maintenance">
              <div class="legend-dot"></div>
              <span class="legend-label">Maintenance ({{ equipmentStatus.maintenance }})</span>
            </div>
            <div class="legend-item breakdown">
              <div class="legend-dot"></div>
              <span class="legend-label">Breakdown ({{ equipmentStatus.breakdown }})</span>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- Fleet Vessels Grid (shown when "All Vessels" selected) -->
    <el-card v-if="selectedVessel === 'all'" class="mb-6">
      <template #header>
        <div class="flex justify-between items-center">
          <span>Fleet Vessels Overview</span>
          <div class="section-actions">
            <el-button-group>
              <el-button size="small" :type="vesselViewMode === 'grid' ? 'primary' : 'default'"
                @click="vesselViewMode = 'grid'">
                <Icon icon="lucide:grid-3x3" class="w-4 h-4" />
              </el-button>
              <el-button size="small" :type="vesselViewMode === 'list' ? 'primary' : 'default'"
                @click="vesselViewMode = 'list'">
                <Icon icon="lucide:list" class="w-4 h-4" />
              </el-button>
            </el-button-group>
          </div>
        </div>
      </template>

      <el-row :gutter="20" v-if="vesselViewMode === 'grid'">
        <el-col v-for="vessel in vessels" :key="vessel.id" :xs="24" :sm="12" :lg="8" class="mb-4">
          <el-card shadow="hover" class="vessel-card h-full" @click="selectVessel(vessel.id)">
            <div class="vessel-header">
              <div class="vessel-title">
                <Icon icon="lucide:ship" class="w-5 h-5" />
                <div class="vessel-name-info">
                  <h3 class="vessel-name">{{ vessel.name }}</h3>
                  <span class="vessel-code">{{ vessel.code }}</span>
                </div>
              </div>
              <div class="vessel-status-badge" :class="vessel.status.toLowerCase()">
                <div class="status-dot"></div>
                <span>{{ vessel.status }}</span>
              </div>
            </div>

            <div class="vessel-metrics">
              <div class="metric-item">
                <span class="metric-label">Production</span>
                <span class="metric-value">{{ formatNumber(vessel.dailyProduction) }} BBL</span>
              </div>
              <div class="metric-item">
                <span class="metric-label">Equipment</span>
                <span class="metric-value">{{ vessel.equipmentAvailability }}%</span>
              </div>
              <div class="metric-item">
                <span class="metric-label">POB</span>
                <span class="metric-value">{{ vessel.currentPOB }}</span>
              </div>
              <div class="metric-item">
                <span class="metric-label">Uptime</span>
                <span class="metric-value">{{ vessel.uptime }}%</span>
              </div>
            </div>

            <div class="vessel-location">
              <Icon icon="lucide:map-pin" class="w-4 h-4" />
              <span>{{ vessel.field_name }}</span>
            </div>
          </el-card>
        </el-col>
      </el-row>

      <div v-else class="vessels-list">
        <el-card v-for="vessel in vessels" :key="vessel.id" shadow="hover" class="mb-4"
          @click="selectVessel(vessel.id)">
          <div class="vessel-header">
            <div class="vessel-title">
              <Icon icon="lucide:ship" class="w-5 h-5" />
              <div class="vessel-name-info">
                <h3 class="vessel-name">{{ vessel.name }}</h3>
                <span class="vessel-code">{{ vessel.code }}</span>
              </div>
            </div>
            <div class="vessel-status-badge" :class="vessel.status.toLowerCase()">
              <div class="status-dot"></div>
              <span>{{ vessel.status }}</span>
            </div>
          </div>

          <div class="vessel-metrics">
            <div class="metric-item">
              <span class="metric-label">Production</span>
              <span class="metric-value">{{ formatNumber(vessel.dailyProduction) }} BBL</span>
            </div>
            <div class="metric-item">
              <span class="metric-label">Equipment</span>
              <span class="metric-value">{{ vessel.equipmentAvailability }}%</span>
            </div>
            <div class="metric-item">
              <span class="metric-label">POB</span>
              <span class="metric-value">{{ vessel.currentPOB }}</span>
            </div>
            <div class="metric-item">
              <span class="metric-label">Uptime</span>
              <span class="metric-value">{{ vessel.uptime }}%</span>
            </div>
          </div>

          <div class="vessel-location">
            <Icon icon="lucide:map-pin" class="w-4 h-4" />
            <span>{{ vessel.field_name }}</span>
          </div>
        </el-card>
      </div>
    </el-card>

    <!-- Main Content Grid -->
    <el-row :gutter="20" class="mb-6">
      <!-- HSE Dashboard -->
      <el-col :xs="24" :sm="24" :md="12" class="mb-4">
        <el-card>
          <template #header>
            <div class="flex justify-between items-center">
              <span>{{ selectedVessel === 'all' ? $t('dashboard.widgets.fleet_hse_events') :
                $t('dashboard.widgets.recent_incidents') }}</span>
              <div class="hse-indicators">
                <div class="indicator-item green">
                  <Icon icon="lucide:check-circle" class="w-4 h-4" />
                  <span>{{ kpiData.daysWithoutLTI }} Days Safe</span>
                </div>
              </div>
            </div>
          </template>
          <div class="hse-content">
            <div v-if="filteredIncidents.length === 0" class="no-incidents">
              <div class="no-incidents-icon">
                <Icon icon="lucide:check-circle" class="w-16 h-16 text-green-500" />
              </div>
              <h3 class="no-incidents-title">All Clear!</h3>
              <p class="no-incidents-text">
                {{ selectedVessel === 'all' ? 'No incidents reported across the fleet in the last 30 days' : 'No incidents reported in the last 30 days' }}
              </p>
            </div>
            <div v-else class="incidents-list">
              <div v-for="incident in filteredIncidents" :key="incident.id" class="incident-item"
                :class="incident.severity.toLowerCase()">
                <div class="incident-indicator">
                  <div class="severity-dot" :class="incident.severity.toLowerCase()"></div>
                </div>
                <div class="incident-content">
                  <div class="incident-header">
                    <h4 class="incident-type">{{ incident.incident_type }}</h4>
                    <span v-if="selectedVessel === 'all'" class="incident-vessel">{{ incident.vessel_name }}</span>
                  </div>
                  <p class="incident-description">{{ incident.description }}</p>
                  <div class="incident-meta">
                    <span class="incident-date">{{ formatDateTime(incident.occurred_at) }}</span>
                    <el-tag :type="getSeverityTagType(incident.severity)" size="small">
                      {{ incident.severity }}
                    </el-tag>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </el-card>
      </el-col>

      <!-- Weather & Environment -->
      <el-col :xs="24" :sm="24" :md="12" class="mb-4">
        <el-card>
          <template #header>
            <div class="flex justify-between items-center">
              <span>{{ selectedVessel === 'all' ? $t('dashboard.widgets.fleet_weather_conditions') :
                $t('dashboard.widgets.weather_conditions') }}</span>
              <div class="weather-status" :class="getWeatherStatusClass()">
                <Icon icon="lucide:cloud" class="w-4 h-4" />
                <span>{{ getWeatherConditionText() }}</span>
              </div>
            </div>
          </template>
          <div v-if="currentWeatherData" class="weather-content">
            <el-row :gutter="20">
              <el-col :xs="12" :sm="6" class="mb-4">
                <div class="weather-item wind">
                  <div class="weather-icon">
                    <Icon icon="lucide:wind" class="w-8 h-8 text-gray-600" />
                  </div>
                  <div class="weather-info">
                    <span class="weather-value">{{ currentWeatherData.wind_speed_knots }}</span>
                    <span class="weather-unit">knots</span>
                    <span class="weather-label">Wind Speed</span>
                  </div>
                </div>
              </el-col>
              <el-col :xs="12" :sm="6" class="mb-4">
                <div class="weather-item wave">
                  <div class="weather-icon">
                    <Icon icon="lucide:waves" class="w-8 h-8 text-gray-600" />
                  </div>
                  <div class="weather-info">
                    <span class="weather-value">{{ currentWeatherData.wave_height_m }}</span>
                    <span class="weather-unit">m</span>
                    <span class="weather-label">Wave Height</span>
                  </div>
                </div>
              </el-col>
              <el-col :xs="12" :sm="6" class="mb-4">
                <div class="weather-item temp">
                  <div class="weather-icon">
                    <Icon icon="lucide:thermometer" class="w-8 h-8 text-gray-600" />
                  </div>
                  <div class="weather-info">
                    <span class="weather-value">{{ currentWeatherData.air_temperature_c }}</span>
                    <span class="weather-unit">°C</span>
                    <span class="weather-label">Temperature</span>
                  </div>
                </div>
              </el-col>
              <el-col :xs="12" :sm="6" class="mb-4">
                <div class="weather-item visibility">
                  <div class="weather-icon">
                    <Icon icon="lucide:eye" class="w-8 h-8 text-gray-600" />
                  </div>
                  <div class="weather-info">
                    <span class="weather-value">{{ currentWeatherData.visibility_nm }}</span>
                    <span class="weather-unit">nm</span>
                    <span class="weather-label">Visibility</span>
                  </div>
                </div>
              </el-col>
            </el-row>
            <div class="sea-state">
              <span class="sea-state-label">Sea State:</span>
              <el-tag :type="getSeaStateTagType()" size="small">{{ currentWeatherData.sea_state }}</el-tag>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- Quick Actions Panel -->
    <el-card class="mb-6">
      <template #header>
        <span>{{ $t('dashboard.quick_actions.title') }}</span>
      </template>
      <el-row :gutter="20">
        <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
          <el-card shadow="hover" class="action-card primary h-full" @click="navigateTo('/reports/dvr')">
            <div class="action-icon">
              <Icon icon="lucide:file-text" class="w-5 h-5" />
            </div>
            <div class="action-content">
              <h3 class="action-title">{{ $t('dashboard.quick_actions.create_dvr') }}</h3>
              <p class="action-description">
                {{ selectedVessel === 'all' ? 'Generate fleet reports' : 'Generate daily vessel report' }}
              </p>
            </div>
            <div class="action-arrow">
              <Icon icon="lucide:arrow-right" class="w-4 h-4" />
            </div>
          </el-card>
        </el-col>
        <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
          <el-card shadow="hover" class="action-card success h-full" @click="navigateTo('/production/wells')">
            <div class="action-icon">
              <Icon icon="lucide:plus" class="w-5 h-5" />
            </div>
            <div class="action-content">
              <h3 class="action-title">{{ $t('dashboard.quick_actions.production_entry') }}</h3>
              <p class="action-description">Record production data</p>
            </div>
            <div class="action-arrow">
              <Icon icon="lucide:arrow-right" class="w-4 h-4" />
            </div>
          </el-card>
        </el-col>
        <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
          <el-card shadow="hover" class="action-card warning h-full" @click="navigateTo('/hse/incidents/create')">
            <div class="action-icon">
              <Icon icon="lucide:alert-triangle" class="w-5 h-5" />
            </div>
            <div class="action-content">
              <h3 class="action-title">{{ $t('dashboard.quick_actions.incident_report') }}</h3>
              <p class="action-description">Report safety incident</p>
            </div>
            <div class="action-arrow">
              <Icon icon="lucide:arrow-right" class="w-4 h-4" />
            </div>
          </el-card>
        </el-col>
        <el-col :xs="24" :sm="12" :lg="6" class="mb-4">
          <el-card shadow="hover" class="action-card info h-full" @click="navigateTo('/maintenance/work-orders')">
            <div class="action-icon">
              <Icon icon="lucide:wrench" class="w-5 h-5" />
            </div>
            <div class="action-content">
              <h3 class="action-title">{{ $t('dashboard.quick_actions.maintenance_log') }}</h3>
              <p class="action-description">Create work order</p>
            </div>
            <div class="action-arrow">
              <Icon icon="lucide:arrow-right" class="w-4 h-4" />
            </div>
          </el-card>
        </el-col>
      </el-row>

      <!-- Recent Reports -->
      <div class="recent-reports">
        <h3 class="reports-title">{{ $t('dashboard.widgets.daily_reports') }}</h3>
        <div class="reports-list">
          <el-card v-for="report in filteredReports" :key="report.id" shadow="never" class="report-item">
            <div class="report-icon">
              <Icon icon="lucide:file-text" class="w-4 h-4 text-gray-400" />
            </div>
            <div class="report-content">
              <h4 class="report-title">DVR {{ formatDate(report.report_date) }}</h4>
              <p class="report-vessel">{{ report.vessel_name }}</p>
            </div>
            <div class="report-status">
              <el-tag :type="report.status === 'Completed' ? 'success' : 'warning'" size="small">
                {{ report.status }}
              </el-tag>
            </div>
          </el-card>
        </div>
      </div>
    </el-card>

    <!-- System Status Footer -->
    <el-card>
      <el-row :gutter="20">
        <el-col :xs="12" :sm="6" class="mb-4">
          <div class="status-item">
            <div class="status-icon wells">
              <Icon icon="lucide:zap" class="w-5 h-5" />
            </div>
            <div class="status-content">
              <span class="status-value">{{ operationsStatus.activeWells }}</span>
              <span class="status-label">
                {{ selectedVessel === 'all' ? 'Total Active Wells' : 'Active Wells' }}
              </span>
            </div>
          </div>
        </el-col>
        <el-col :xs="12" :sm="6" class="mb-4">
          <div class="status-item">
            <div class="status-icon equipment">
              <Icon icon="lucide:settings" class="w-5 h-5" />
            </div>
            <div class="status-content">
              <span class="status-value">{{ operationsStatus.criticalEquipmentRunning }}</span>
              <span class="status-label">Critical Equipment</span>
            </div>
          </div>
        </el-col>
        <el-col :xs="12" :sm="6" class="mb-4">
          <div class="status-item">
            <div class="status-icon gas">
              <Icon icon="lucide:activity" class="w-5 h-5" />
            </div>
            <div class="status-content">
              <span class="status-value">{{ formatNumber(operationsStatus.gasExportMMSCFD) }}</span>
              <span class="status-label">Gas Export MMSCFD</span>
            </div>
          </div>
        </el-col>
        <el-col :xs="12" :sm="6" class="mb-4">
          <div class="status-item">
            <div class="status-icon uptime">
              <Icon icon="lucide:clock" class="w-5 h-5" />
            </div>
            <div class="status-content">
              <span class="status-value">{{ operationsStatus.facilityUptime }}%</span>
              <span class="status-label">
                {{ selectedVessel === 'all' ? 'Fleet Uptime' : 'Facility Uptime' }}
              </span>
            </div>
          </div>
        </el-col>
      </el-row>
    </el-card>

    <!-- Vessel Comparison Modal -->
    <el-dialog v-model="showCompareModal" title="Fleet Vessels Comparison" width="80%"
      :before-close="closeCompareModal">
      <div class="comparison-content">
        <div class="comparison-chart">
          <v-chart :option="comparisonChartOption" class="w-full h-96" />
        </div>
        <div class="comparison-table">
          <el-table :data="vessels" style="width: 100%">
            <el-table-column prop="name" label="Vessel" width="200" />
            <el-table-column prop="dailyProduction" label="Production (BBL)" width="150">
              <template #default="scope">
                {{ formatNumber(scope.row.dailyProduction) }}
              </template>
            </el-table-column>
            <el-table-column prop="equipmentAvailability" label="Equipment %" width="130">
              <template #default="scope">
                <el-tag
                  :type="scope.row.equipmentAvailability >= 95 ? 'success' : scope.row.equipmentAvailability >= 90 ? 'warning' : 'danger'">
                  {{ scope.row.equipmentAvailability }}%
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="currentPOB" label="POB" width="100" />
            <el-table-column prop="uptime" label="Uptime %" width="120">
              <template #default="scope">
                <el-progress :percentage="scope.row.uptime" :stroke-width="8" :show-text="false" />
                <span class="ml-2">{{ scope.row.uptime }}%</span>
              </template>
            </el-table-column>
            <el-table-column prop="status" label="Status" width="120">
              <template #default="scope">
                <el-tag :type="scope.row.status === 'Active' ? 'success' : 'warning'">
                  {{ scope.row.status }}
                </el-tag>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
  
  <script setup>
  import { ref, reactive, onMounted, onBeforeUnmount, computed } from 'vue'
  import ContentHeader from '@/components/Custom/ContentHeader.vue'
  import CardStats from '@/components/Custom/CardStats.vue'
  import VesselDropdown from '@/components/Custom/VesselDropdown.vue'
  import { useRoute, useRouter } from 'vue-router'
  import { use } from 'echarts/core'
  import { CanvasRenderer } from 'echarts/renderers'
  import { LineChart, BarChart, PieChart } from 'echarts/charts'
  import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent
  } from 'echarts/components'
  import VChart from 'vue-echarts'
  import { Icon } from '@iconify/vue'
  
  // Register ECharts components
  use([
    CanvasRenderer,
    LineChart,
    BarChart,
    PieChart,
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent
  ])
  
  const route = useRoute()
  const router = useRouter()
  
  const title = route.meta.title || 'dashboard'
  const loading = ref(false)
  const selectedDate = ref(new Date().toISOString().split('T')[0])
  const productionPeriod = ref('7d')
  const chartType = ref('line')
  const lastUpdated = ref(new Date())
  const selectedVessel = ref('all') // 'all' for fleet view or vessel ID
  const vesselViewMode = ref('grid') // 'grid' or 'list'
  const showCompareModal = ref(false)
  
  // Multi-Vessel Data
  const vessels = ref([
    {
        id: 1,
        code: 'FPU-TJ01',
        name: 'FPU Trunojoyo-01',
        field_name: 'Trunojoyo Field',
        status: 'Active',
        dailyProduction: 15420,
        equipmentAvailability: 94.5,
        currentPOB: 89,
        uptime: 96.2,
        coordinates: { lat: -6.8856, lng: 112.0317 }
    },
    {
        id: 2,
        code: 'FPU-TJ02',
        name: 'FPU Trunojoyo-02',
        field_name: 'Trunojoyo Field',
        status: 'Active',
        dailyProduction: 12800,
        equipmentAvailability: 92.1,
        currentPOB: 78,
        uptime: 94.8,
        coordinates: { lat: -6.9056, lng: 112.0517 }
    },
    {
        id: 3,
        code: 'MOPU-PS08',
        name: 'MOPU Prameswari-8',
        field_name: 'Prameswari Field',
        status: 'Maintenance',
        dailyProduction: 8650,
        equipmentAvailability: 87.3,
        currentPOB: 65,
        uptime: 89.5,
        coordinates: { lat: -6.8656, lng: 112.0117 }
    }
  ])
  
  // Fleet Statistics
  const fleetStats = computed(() => ({
    totalVessels: vessels.value.length,
    activeVessels: vessels.value.filter(v => v.status === 'Active').length,
    totalProduction: vessels.value.reduce((sum, v) => sum + v.dailyProduction, 0),
    totalPOB: vessels.value.reduce((sum, v) => sum + v.currentPOB, 0)
  }))
  
  // Current Vessel Info
  const currentVesselInfo = computed(() => {
    if (selectedVessel.value === 'all') {
        return {
            name: 'Fleet Overview',
            field_name: `${fleetStats.value.totalVessels} Vessels`
        }
    }
    return vessels.value.find(v => v.id === selectedVessel.value)
  })
  
  // Alerts (filtered by vessel)
  const alerts = ref([
    {
        id: 1,
        type: 'warning',
        title: 'Maintenance Due',
        description: 'Gas Turbine GT-6640A scheduled maintenance due in 48 hours',
        vessel_id: 1
    },
    {
        id: 2,
        type: 'info',
        title: 'Weather Advisory',
        description: 'Moderate sea conditions expected in next 24 hours',
        vessel_id: null // Fleet-wide alert
    }
  ])
  
  const filteredAlerts = computed(() => {
    if (selectedVessel.value === 'all') {
        return alerts.value
    }
    return alerts.value.filter(alert => 
        alert.vessel_id === null || alert.vessel_id === selectedVessel.value
    )
  })
  
  // Enhanced KPI Data (reactive to vessel selection)
  const kpiData = computed(() => {
    if (selectedVessel.value === 'all') {
        // Fleet aggregated data
        return {
            totalOilProduction: fleetStats.value.totalProduction,
            targetProduction: 45000, // Fleet target
            productionTrend: 2.1,
            equipmentAvailability: 91.3,
            runningEquipment: 68,
            totalEquipment: 75,
            criticalEquipment: 35,
            daysWithoutLTI: 127,
            ltif: 0.0,
            totalPOB: fleetStats.value.totalPOB,
            maxPOB: 350, // Fleet capacity
            companyPOB: 125,
            contractorPOB: 107
        }
    } else {
        // Single vessel data
        const vessel = vessels.value.find(v => v.id === selectedVessel.value)
        return {
            totalOilProduction: vessel?.dailyProduction || 0,
            targetProduction: 16000,
            productionTrend: 2.3,
            equipmentAvailability: vessel?.equipmentAvailability || 0,
            runningEquipment: 18,
            totalEquipment: 21,
            criticalEquipment: 12,
            daysWithoutLTI: 127,
            ltif: 0.0,
            totalPOB: vessel?.currentPOB || 0,
            maxPOB: 120,
            companyPOB: 45,
            contractorPOB: 44
        }
    }
  })
  
  // Production Summary (reactive to vessel selection)
  const productionSummary = computed(() => {
    if (selectedVessel.value === 'all') {
        return {
            oil: fleetStats.value.totalProduction,
            gas: 285.6,
            waterCut: 32.8,
            efficiency: 94.1
        }
    } else {
        const vessel = vessels.value.find(v => v.id === selectedVessel.value)
        return {
            oil: vessel?.dailyProduction || 0,
            gas: 125.6,
            waterCut: 34.2,
            efficiency: 96.2
        }
    }
  })
  
  // Equipment Status (reactive to vessel selection)
  const equipmentStatus = computed(() => {
    if (selectedVessel.value === 'all') {
        return {
            running: 68,
            standby: 5,
            maintenance: 2,
            breakdown: 0
        }
    } else {
        return {
            running: 18,
            standby: 2,
            maintenance: 1,
            breakdown: 0
        }
    }
  })
  
  // Operations Status (reactive to vessel selection)
  const operationsStatus = computed(() => {
    if (selectedVessel.value === 'all') {
        return {
            activeWells: 28,
            criticalEquipmentRunning: 35,
            gasExportMMSCFD: 285.6,
            facilityUptime: 93.5
        }
    } else {
        return {
            activeWells: 12,
            criticalEquipmentRunning: 12,
            gasExportMMSCFD: 125.6,
            facilityUptime: 96.2
        }
    }
  })
  
  // Weather Data (context-aware)
  const weatherData = ref({
    // Fleet-wide weather for main location
    fleet: {
        wind_speed_knots: 12.5,
        wave_height_m: 1.8,
        air_temperature_c: 28,
        visibility_nm: 8.5,
        sea_state: 'Slight',
        weather_condition: 'Partly Cloudy',
        observation_time: new Date()
    },
    // Individual vessel weather (could be different)
    vessels: {
        1: {
            wind_speed_knots: 12.5,
            wave_height_m: 1.8,
            air_temperature_c: 28,
            visibility_nm: 8.5,
            sea_state: 'Slight',
            weather_condition: 'Partly Cloudy',
            observation_time: new Date()
        },
        2: {
            wind_speed_knots: 14.2,
            wave_height_m: 2.1,
            air_temperature_c: 27,
            visibility_nm: 7.8,
            sea_state: 'Moderate',
            weather_condition: 'Cloudy',
            observation_time: new Date()
        }
    }
  })
  
  const currentWeatherData = computed(() => {
    if (selectedVessel.value === 'all') {
        return weatherData.value.fleet
    }
    return weatherData.value.vessels[selectedVessel.value] || weatherData.value.fleet
  })
  
  // Recent Incidents (filtered by vessel)
  const recentIncidents = ref([
    {
        id: 1,
        incident_type: 'Near Miss',
        description: 'Personnel slipped on wet deck during routine inspection',
        severity: 'Low',
        occurred_at: new Date(Date.now() - 86400000 * 2),
        vessel_id: 1,
        vessel_name: 'FPU Trunojoyo-01'
    },
    {
        id: 2,
        incident_type: 'Equipment Alarm',
        description: 'High vibration alarm on compressor GTC-A',
        severity: 'Medium',
        occurred_at: new Date(Date.now() - 86400000 * 5),
        vessel_id: 2,
        vessel_name: 'FPU Trunojoyo-02'
    }
  ])
  
  const filteredIncidents = computed(() => {
    if (selectedVessel.value === 'all') {
        return recentIncidents.value
    }
    return recentIncidents.value.filter(incident => 
        incident.vessel_id === selectedVessel.value
    )
  })
  
  // Recent Reports (filtered by vessel)
  const recentReports = ref([
    {
        id: 1,
        report_date: new Date(Date.now() - 86400000),
        vessel_name: 'FPU Trunojoyo-01',
        vessel_id: 1,
        status: 'Completed'
    },
    {
        id: 2,
        report_date: new Date(Date.now() - 86400000 * 2),
        vessel_name: 'FPU Trunojoyo-02',
        vessel_id: 2,
        status: 'Completed'
    },
    {
        id: 3,
        report_date: new Date(),
        vessel_name: 'MOPU Prameswari-8',
        vessel_id: 3,
        status: 'In Progress'
    }
  ])
  
  const filteredReports = computed(() => {
    if (selectedVessel.value === 'all') {
        return recentReports.value.slice(0, 3)
    }
    return recentReports.value.filter(report => 
        report.vessel_id === selectedVessel.value
    )
  })
  
  // Enhanced Production Chart (context-aware)
  const productionChartOption = computed(() => {
    const title = selectedVessel.value === 'all' ? 'Fleet Production Trend' : 'Production Trend'
    const data = selectedVessel.value === 'all' 
        ? {
            oil: [32200, 35800, 33900, 38100, 34600, 36900, 36870],
            gas: [272, 298, 258, 315, 285, 308, 285],
            water: [18200, 19400, 17800, 20600, 18100, 19300, 19050]
          }
        : {
            oil: [15200, 15800, 14900, 16100, 15600, 15900, 15420],
            gas: [122, 128, 118, 135, 125, 130, 126],
            water: [8200, 8400, 7800, 8600, 8100, 8300, 8050]
          }
  
    return {
        title: {
            text: title,
            left: 'center',
            textStyle: {
                fontSize: 16,
                fontWeight: 'normal'
            }
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'cross'
            },
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#e5e7eb',
            borderWidth: 1,
            textStyle: {
                color: '#374151'
            }
        },
        legend: {
            data: ['Oil Production', 'Gas Production', 'Water Production'],
            bottom: 10,
            textStyle: {
                color: '#6b7280'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            top: '15%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            axisLine: {
                lineStyle: {
                    color: '#e5e7eb'
                }
            },
            axisLabel: {
                color: '#6b7280'
            }
        },
        yAxis: [
            {
                type: 'value',
                name: 'Oil/Water (BBL)',
                position: 'left',
                axisLine: {
                    lineStyle: {
                        color: '#e5e7eb'
                    }
                },
                axisLabel: {
                    color: '#6b7280'
                },
                splitLine: {
                    lineStyle: {
                        color: '#f3f4f6'
                    }
                }
            },
            {
                type: 'value',
                name: 'Gas (MMSCF)',
                position: 'right',
                axisLine: {
                    lineStyle: {
                        color: '#e5e7eb'
                    }
                },
                axisLabel: {
                    color: '#6b7280'
                }
            }
        ],
        series: [
            {
                name: 'Oil Production',
                type: chartType.value,
                data: data.oil,
                smooth: true,
                itemStyle: { 
                    color: '#3b82f6' 
                },
                areaStyle: chartType.value === 'line' ? {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(59, 130, 246, 0.3)' },
                            { offset: 1, color: 'rgba(59, 130, 246, 0.05)' }
                        ]
                    }
                } : null
            },
            {
                name: 'Gas Production',
                type: chartType.value,
                yAxisIndex: 1,
                data: data.gas,
                smooth: true,
                itemStyle: { 
                    color: '#10b981' 
                },
                areaStyle: chartType.value === 'line' ? {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(16, 185, 129, 0.3)' },
                            { offset: 1, color: 'rgba(16, 185, 129, 0.05)' }
                        ]
                    }
                } : null
            },
            {
                name: 'Water Production',
                type: chartType.value,
                data: data.water,
                smooth: true,
                itemStyle: { 
                    color: '#f59e0b' 
                },
                areaStyle: chartType.value === 'line' ? {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(245, 158, 11, 0.3)' },
                            { offset: 1, color: 'rgba(245, 158, 11, 0.05)' }
                        ]
                    }
                } : null
            }
        ]
    }
  })
  
  // Enhanced Equipment Chart
  const equipmentChartOption = computed(() => ({
    title: {
        text: selectedVessel.value === 'all' ? 'Fleet Equipment Status' : 'Equipment Status',
        left: 'center',
        textStyle: {
            fontSize: 16,
            fontWeight: 'normal'
        }
    },
    tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b}: {c} ({d}%)',
        backgroundColor: 'rgba(255, 255, 255, 0.95)',
        borderColor: '#e5e7eb',
        borderWidth: 1,
        textStyle: {
            color: '#374151'
        }
    },
    series: [
        {
            name: 'Equipment Status',
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['50%', '50%'],
            avoidLabelOverlap: false,
            label: {
                show: false
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: '14',
                    fontWeight: 'bold'
                },
                itemStyle: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },
            labelLine: {
                show: false
            },
            data: [
                { 
                    value: equipmentStatus.value.running, 
                    name: 'Running', 
                    itemStyle: { color: '#10b981' } 
                },
                { 
                    value: equipmentStatus.value.standby, 
                    name: 'Standby', 
                    itemStyle: { color: '#f59e0b' } 
                },
                { 
                    value: equipmentStatus.value.maintenance, 
                    name: 'Maintenance', 
                    itemStyle: { color: '#ef4444' } 
                },
                { 
                    value: equipmentStatus.value.breakdown, 
                    name: 'Breakdown', 
                    itemStyle: { color: '#6b7280' } 
                }
            ]
        }
    ]
  }))
  
  // Fleet Comparison Chart
  const comparisonChartOption = computed(() => ({
    title: {
        text: 'Fleet Vessels Performance Comparison',
        left: 'center'
    },
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        }
    },
    legend: {
        data: ['Daily Production (BBL)', 'Equipment Availability (%)', 'Uptime (%)']
    },
    xAxis: {
        type: 'category',
        data: vessels.value.map(v => v.name)
    },
    yAxis: [
        {
            type: 'value',
            name: 'Production (BBL)',
            position: 'left'
        },
        {
            type: 'value',
            name: 'Percentage (%)',
            position: 'right',
            max: 100
        }
    ],
    series: [
        {
            name: 'Daily Production (BBL)',
            type: 'bar',
            data: vessels.value.map(v => v.dailyProduction),
            itemStyle: { color: '#3b82f6' }
        },
        {
            name: 'Equipment Availability (%)',
            type: 'bar',
            yAxisIndex: 1,
            data: vessels.value.map(v => v.equipmentAvailability),
            itemStyle: { color: '#10b981' }
        },
        {
            name: 'Uptime (%)',
            type: 'bar',
            yAxisIndex: 1,
            data: vessels.value.map(v => v.uptime),
            itemStyle: { color: '#f59e0b' }
        }
    ]
  }))
  
  // Helper Methods
  const formatNumber = (num) => {
    return new Intl.NumberFormat().format(num)
  }
  
  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    })
  }
  
  const formatDateTime = (date) => {
    return new Date(date).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
  }
  
  const getSeverityTagType = (severity) => {
    const types = {
        'Critical': 'danger',
        'High': 'danger',
        'Medium': 'warning',
        'Low': 'info'
    }
    return types[severity] || 'info'
  }
  
  const getVesselStatusClass = () => {
    if (selectedVessel.value === 'all') {
        return fleetStats.value.activeVessels === fleetStats.value.totalVessels ? 'online' : 'warning'
    }
    const vessel = currentVesselInfo.value
    return vessel?.status === 'Active' ? 'online' : 'warning'
  }
  
  const getVesselStatusText = () => {
    if (selectedVessel.value === 'all') {
        return `${fleetStats.value.activeVessels}/${fleetStats.value.totalVessels} Active`
    }
    return currentVesselInfo.value?.status || 'Unknown'
  }
  
  const getWeatherStatusClass = () => {
    if (!currentWeatherData.value) return 'unknown'
    const condition = currentWeatherData.value.weather_condition?.toLowerCase() || ''
    if (condition.includes('clear') || condition.includes('sunny')) return 'clear'
    if (condition.includes('cloud')) return 'cloudy'
    if (condition.includes('rain') || condition.includes('storm')) return 'rainy'
    return 'normal'
  }
  
  const getWeatherConditionText = () => {
    if (selectedVessel.value === 'all') {
        return 'Fleet Area: ' + (currentWeatherData.value?.weather_condition || 'Unknown')
    }
    return currentWeatherData.value?.weather_condition || 'Unknown'
  }
  
  const getSeaStateTagType = () => {
    if (!currentWeatherData.value) return 'info'
    const seaState = currentWeatherData.value.sea_state?.toLowerCase() || ''
    if (seaState.includes('calm') || seaState.includes('slight')) return 'success'
    if (seaState.includes('moderate')) return 'warning'
    if (seaState.includes('rough') || seaState.includes('high')) return 'danger'
    return 'info'
  }
  
  // Event Handlers
  const handleVesselChange = (vesselId) => {
    selectedVessel.value = vesselId
    refreshDashboard()
  }
  
  const selectVessel = (vesselId) => {
    selectedVessel.value = vesselId
    refreshDashboard()
  }
  
  const handleDateChange = (date) => {
    refreshDashboard()
  }

  const refreshDashboard = () => {
    loading.value = true
    lastUpdated.value = new Date()
    // Simulate API call
    setTimeout(() => {
      loading.value = false
    }, 1000)
  }

  const updateProductionChart = () => {
    // Chart will auto-update via computed properties
  }

  const exportKPI = () => {
    // Export KPI functionality
    console.log('Exporting KPI report for:', selectedVessel.value)
  }

  const openCompareModal = () => {
    showCompareModal.value = true
  }

  const closeCompareModal = () => {
    showCompareModal.value = false
  }

  const navigateTo = (path) => {
    router.push(path)
  }

  // Initialize dashboard on mount
  onMounted(() => {
    // Check URL parameters for vessel selection
    const vesselParam = route.query.vessel
    if (vesselParam && vesselParam !== 'all') {
      const vesselId = parseInt(vesselParam)
      if (vessels.value.find(v => v.id === vesselId)) {
        selectedVessel.value = vesselId
      }
    }
    refreshDashboard()
  })
  </script>

  <style scoped>
  /* Dashboard Container */
  .dashboard-container {
    @apply min-h-screen bg-gray-50 dark:bg-gray-900;
  }

  /* Header Section */
  .dashboard-header {
    @apply bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6;
  }

  .header-content {
    @apply flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4;
  }

  .header-left {
    @apply flex flex-col md:flex-row md:items-center gap-4;
  }

  .header-title {
    @apply flex-1;
  }

  .title-text {
    @apply text-2xl font-bold text-gray-900 dark:text-white;
  }

  .subtitle-text {
    @apply text-sm text-gray-600 dark:text-gray-400 mt-1;
  }

  /* Vessel Selector */
  .vessel-selector {
    @apply flex flex-col md:flex-row md:items-center gap-3;
  }

  .vessel-controls {
    @apply flex items-center gap-3;
  }

  .vessel-select {
    @apply min-w-64;
  }

  .vessel-option {
    @apply flex items-center gap-2 p-2;
  }

  .vessel-option.fleet {
    @apply font-medium text-blue-600;
  }

  .vessel-info {
    @apply flex flex-col;
  }

  .vessel-name {
    @apply font-medium text-gray-900 dark:text-white;
  }

  .vessel-code {
    @apply text-xs text-gray-500 dark:text-gray-400;
  }

  .vessel-status {
    @apply flex items-center;
  }

  .vessel-status .status-dot {
    @apply w-2 h-2 rounded-full;
  }

  .vessel-status.active .status-dot {
    @apply bg-green-500;
  }

  .vessel-status.maintenance .status-dot {
    @apply bg-yellow-500;
  }

  .vessel-status.standby .status-dot {
    @apply bg-gray-500;
  }

  .current-vessel {
    @apply flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg;
  }

  .vessel-badge {
    @apply flex items-center gap-2;
  }

  .vessel-field {
    @apply text-xs text-gray-500 dark:text-gray-400;
  }

  .status-indicator {
    @apply flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium;
  }

  .status-indicator.online {
    @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
  }

  .status-indicator.warning {
    @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200;
  }

  .status-indicator .status-dot {
    @apply w-1.5 h-1.5 rounded-full;
  }

  .status-indicator.online .status-dot {
    @apply bg-green-500;
  }

  .status-indicator.warning .status-dot {
    @apply bg-yellow-500;
  }

  .header-right {
    @apply flex flex-col gap-3;
  }

  .header-controls {
    @apply flex items-center gap-3;
  }

  .date-picker {
    @apply min-w-48;
  }

  .refresh-btn {
    @apply gap-2;
  }

  .last-updated {
    @apply flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400;
  }

  /* Fleet Overview Banner */
  .fleet-overview {
    @apply bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6;
  }

  .fleet-stats {
    @apply grid grid-cols-2 lg:grid-cols-4 gap-4;
  }

  .fleet-stat-item {
    @apply flex items-center gap-3 p-4 bg-white/10 rounded-lg backdrop-blur-sm;
  }

  .stat-content {
    @apply flex flex-col;
  }

  .stat-value {
    @apply text-2xl font-bold;
  }

  .stat-label {
    @apply text-sm opacity-90;
  }

  /* Alert Banner */
  .alert-banner {
    @apply p-4 space-y-2;
  }

  .alert-item {
    @apply mb-2;
  }

  /* KPI Section */
  .kpi-section {
    @apply p-6;
  }

  .section-header {
    @apply flex items-center justify-between mb-6;
  }

  .section-title {
    @apply text-xl font-semibold text-gray-900 dark:text-white;
  }

  .section-actions {
    @apply flex items-center gap-2;
  }

  .kpi-grid {
    @apply grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6;
  }

  .kpi-card {
    @apply bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm;
    transition: all 0.3s ease;
  }

  .kpi-card:hover {
    @apply shadow-lg transform -translate-y-1;
  }

  .kpi-header {
    @apply flex items-center justify-between mb-4;
  }

  .kpi-icon {
    @apply p-3 rounded-lg;
  }

  .production-icon {
    @apply bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300;
  }

  .equipment-icon {
    @apply bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300;
  }

  .safety-icon {
    @apply bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300;
  }

  .personnel-icon {
    @apply bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-300;
  }

  .kpi-trend {
    @apply flex items-center gap-1 text-sm;
  }

  .trend-up {
    @apply text-green-500;
  }

  .trend-down {
    @apply text-red-500;
  }

  .trend-up-text {
    @apply text-green-600 font-medium;
  }

  .trend-down-text {
    @apply text-red-600 font-medium;
  }

  .kpi-content {
    @apply mb-4;
  }

  .kpi-title {
    @apply text-sm font-medium text-gray-600 dark:text-gray-400 mb-2;
  }

  .kpi-value {
    @apply flex items-baseline gap-1;
  }

  .value-number {
    @apply text-3xl font-bold text-gray-900 dark:text-white;
  }

  .value-unit {
    @apply text-sm text-gray-500 dark:text-gray-400;
  }

  .kpi-subtitle {
    @apply text-sm text-gray-600 dark:text-gray-400 mt-1;
  }

  .vessel-count {
    @apply text-xs text-gray-500 dark:text-gray-500;
  }

  .kpi-progress {
    @apply mt-4;
  }

  .progress-bar {
    @apply w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2;
  }

  .progress-fill {
    @apply h-2 rounded-full transition-all duration-300;
  }

  .production-progress {
    @apply bg-blue-500;
  }

  .equipment-progress {
    @apply bg-green-500;
  }

  .equipment-status {
    @apply flex items-center gap-1 text-sm;
  }

  .equipment-status .status-dot {
    @apply w-2 h-2 rounded-full bg-green-500;
  }

  .status-label {
    @apply text-gray-600 dark:text-gray-300;
  }

  .safety-badge {
    @apply px-2 py-1 bg-purple-100 dark:bg-purple-900 rounded text-xs;
  }

  .safety-label {
    @apply text-purple-700 dark:text-purple-300;
  }

  .safety-milestone {
    @apply mt-4;
  }

  .milestone-progress {
    @apply w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-2;
  }

  .milestone-bar {
    @apply h-2 rounded-full bg-purple-500 transition-all duration-300;
  }

  .milestone-text {
    @apply text-xs text-gray-500 dark:text-gray-400;
  }

  .pob-indicator {
    @apply text-sm;
  }

  .pob-status {
    @apply px-2 py-1 rounded-full text-xs font-medium;
  }

  .pob-status.normal {
    @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
  }

  .pob-status.warning {
    @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200;
  }

  .pob-breakdown {
    @apply mt-4 grid grid-cols-2 gap-2;
  }

  .breakdown-item {
    @apply flex justify-between text-sm;
  }

  .breakdown-label {
    @apply text-gray-600 dark:text-gray-400;
  }

  .breakdown-value {
    @apply font-medium text-gray-900 dark:text-white;
  }

  /* Fleet Vessels Section */
  .fleet-vessels-section {
    @apply p-6 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700;
  }

  .vessels-grid {
    @apply grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6;
  }

  .vessels-list {
    @apply space-y-4;
  }

  .vessel-card {
    @apply bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 cursor-pointer;
    transition: all 0.3s ease;
  }

  .vessel-card:hover {
    @apply shadow-md transform scale-105 border-blue-300 dark:border-blue-600;
  }

  .vessel-header {
    @apply flex items-center justify-between mb-3;
  }

  .vessel-title {
    @apply flex items-center gap-2;
  }

  .vessel-name-info {
    @apply flex flex-col;
  }

  .vessel-name {
    @apply font-medium text-gray-900 dark:text-white;
  }

  .vessel-code {
    @apply text-sm text-gray-500 dark:text-gray-400;
  }

  .vessel-status-badge {
    @apply flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium;
  }

  .vessel-status-badge.active {
    @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
  }

  .vessel-status-badge.maintenance {
    @apply bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200;
  }

  .vessel-status-badge .status-dot {
    @apply w-1.5 h-1.5 rounded-full;
  }

  .vessel-status-badge.active .status-dot {
    @apply bg-green-500;
  }

  .vessel-status-badge.maintenance .status-dot {
    @apply bg-yellow-500;
  }

  .vessel-metrics {
    @apply grid grid-cols-2 gap-3 mb-3;
  }

  .metric-item {
    @apply flex flex-col;
  }

  .metric-label {
    @apply text-xs text-gray-500 dark:text-gray-400;
  }

  .metric-value {
    @apply font-medium text-gray-900 dark:text-white;
  }

  .vessel-location {
    @apply flex items-center gap-1 text-xs text-gray-600 dark:text-gray-300;
  }

  /* Main Content */
  .main-content {
    @apply grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 p-6;
  }

  .main-content.single-vessel {
    @apply xl:grid-cols-4;
  }

  .content-section {
    @apply bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6;
  }

  .production-section {
    @apply lg:col-span-2;
  }

  .section-controls {
    @apply flex items-center gap-3;
  }

  .period-select {
    @apply min-w-24;
  }

  .view-toggle {
    @apply gap-0;
  }

  .chart-container {
    @apply h-80 mb-4;
  }

  .production-chart,
  .equipment-chart {
    @apply w-full h-full;
  }

  .production-summary {
    @apply grid grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700;
  }

  .summary-item {
    @apply flex flex-col items-center text-center;
  }

  .summary-label {
    @apply text-xs text-gray-500 dark:text-gray-400 mb-1;
  }

  .summary-value {
    @apply font-semibold;
  }

  .summary-value.oil {
    @apply text-blue-600;
  }

  .summary-value.gas {
    @apply text-green-600;
  }

  .summary-value.water {
    @apply text-yellow-600;
  }

  .summary-value.efficiency {
    @apply text-purple-600;
  }

  .view-all-link {
    @apply flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300;
  }

  .equipment-legend {
    @apply grid grid-cols-2 gap-2 pt-4 border-t border-gray-200 dark:border-gray-700;
  }

  .legend-item {
    @apply flex items-center gap-2 text-sm;
  }

  .legend-dot {
    @apply w-3 h-3 rounded-full;
  }

  .legend-item.running .legend-dot {
    @apply bg-green-500;
  }

  .legend-item.standby .legend-dot {
    @apply bg-yellow-500;
  }

  .legend-item.maintenance .legend-dot {
    @apply bg-red-500;
  }

  .legend-item.breakdown .legend-dot {
    @apply bg-gray-500;
  }

  .legend-label {
    @apply text-gray-600 dark:text-gray-400;
  }

  /* HSE Section */
  .hse-indicators {
    @apply flex items-center gap-2;
  }

  .indicator-item {
    @apply flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium;
  }

  .indicator-item.green {
    @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
  }

  .hse-content {
    @apply mt-4;
  }

  .no-incidents {
    @apply text-center py-8;
  }

  .no-incidents-icon {
    @apply mb-4;
  }

  .no-incidents-title {
    @apply text-lg font-semibold text-gray-900 dark:text-white mb-2;
  }

  .no-incidents-text {
    @apply text-gray-600 dark:text-gray-400;
  }

  .incidents-list {
    @apply space-y-4;
  }

  .incident-item {
    @apply flex gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg;
  }

  .incident-indicator {
    @apply flex-shrink-0;
  }

  .severity-dot {
    @apply w-3 h-3 rounded-full;
  }

  .severity-dot.low {
    @apply bg-blue-500;
  }

  .severity-dot.medium {
    @apply bg-yellow-500;
  }

  .severity-dot.high {
    @apply bg-red-500;
  }

  .incident-content {
    @apply flex-1;
  }

  .incident-header {
    @apply flex items-center justify-between mb-1;
  }

  .incident-type {
    @apply font-medium text-gray-900 dark:text-white;
  }

  .incident-vessel {
    @apply text-xs text-gray-500 dark:text-gray-400;
  }

  .incident-description {
    @apply text-sm text-gray-600 dark:text-gray-400 mb-2;
  }

  .incident-meta {
    @apply flex items-center justify-between;
  }

  .incident-date {
    @apply text-xs text-gray-500 dark:text-gray-400;
  }

  /* Weather Section */
  .weather-status {
    @apply flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium;
  }

  .weather-status.clear {
    @apply bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200;
  }

  .weather-status.cloudy {
    @apply bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200;
  }

  .weather-status.rainy {
    @apply bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200;
  }

  .weather-status.normal {
    @apply bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200;
  }

  .weather-content {
    @apply mt-4;
  }

  .weather-grid {
    @apply grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4;
  }

  .weather-item {
    @apply flex flex-col items-center text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg;
  }

  .weather-icon {
    @apply mb-2;
  }

  .weather-info {
    @apply flex flex-col items-center;
  }

  .weather-value {
    @apply text-lg font-bold text-gray-900 dark:text-white;
  }

  .weather-unit {
    @apply text-xs text-gray-500 dark:text-gray-400;
  }

  .weather-label {
    @apply text-xs text-gray-600 dark:text-gray-300 mt-1;
  }

  .sea-state {
    @apply flex items-center justify-center gap-2 text-sm;
  }

  .sea-state-label {
    @apply text-gray-600 dark:text-gray-400;
  }

  /* Actions Section */
  .actions-grid {
    @apply grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6;
  }

  .action-card {
    @apply flex items-center gap-3 p-4 rounded-lg border-2 cursor-pointer;
    transition: all 0.3s ease;
  }

  .action-card:hover {
    @apply transform -translate-y-1 shadow-lg;
  }

  .action-card.primary {
    @apply border-blue-200 bg-blue-50 hover:border-blue-300 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-900/20 dark:hover:border-blue-700 dark:hover:bg-blue-900/30;
  }

  .action-card.success {
    @apply border-green-200 bg-green-50 hover:border-green-300 hover:bg-green-100 dark:border-green-800 dark:bg-green-900/20 dark:hover:border-green-700 dark:hover:bg-green-900/30;
  }

  .action-card.warning {
    @apply border-yellow-200 bg-yellow-50 hover:border-yellow-300 hover:bg-yellow-100 dark:border-yellow-800 dark:bg-yellow-900/20 dark:hover:border-yellow-700 dark:hover:bg-yellow-900/30;
  }

  .action-card.info {
    @apply border-gray-200 bg-gray-50 hover:border-gray-300 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-gray-600 dark:hover:bg-gray-700;
  }

  .action-icon {
    @apply flex-shrink-0 p-2 rounded-lg;
  }

  .action-card.primary .action-icon {
    @apply bg-blue-100 text-blue-600 dark:bg-blue-800 dark:text-blue-300;
  }

  .action-card.success .action-icon {
    @apply bg-green-100 text-green-600 dark:bg-green-800 dark:text-green-300;
  }

  .action-card.warning .action-icon {
    @apply bg-yellow-100 text-yellow-600 dark:bg-yellow-800 dark:text-yellow-300;
  }

  .action-card.info .action-icon {
    @apply bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300;
  }

  .action-content {
    @apply flex-1;
  }

  .action-title {
    @apply font-medium text-gray-900 dark:text-white mb-1;
  }

  .action-description {
    @apply text-sm text-gray-600 dark:text-gray-400;
  }

  .action-arrow {
    @apply flex-shrink-0 text-gray-400;
  }

  .recent-reports {
    @apply border-t border-gray-200 dark:border-gray-700 pt-6;
  }

  .reports-title {
    @apply font-medium text-gray-900 dark:text-white mb-4;
  }

  .reports-list {
    @apply space-y-3;
  }

  .report-item {
    @apply flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg;
  }

  .report-icon {
    @apply flex-shrink-0;
  }

  .report-content {
    @apply flex-1;
  }

  .report-title {
    @apply font-medium text-gray-900 dark:text-white;
  }

  .report-vessel {
    @apply text-sm text-gray-600 dark:text-gray-400;
  }

  .report-status {
    @apply flex-shrink-0;
  }

  /* System Status Footer */
  .system-status {
    @apply bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-6;
  }

  .status-grid {
    @apply grid grid-cols-2 lg:grid-cols-4 gap-6;
  }

  .status-item {
    @apply flex items-center gap-3;
  }

  .status-icon {
    @apply p-3 rounded-lg;
  }

  .status-icon.wells {
    @apply bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300;
  }

  .status-icon.equipment {
    @apply bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300;
  }

  .status-icon.gas {
    @apply bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300;
  }

  .status-icon.uptime {
    @apply bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300;
  }

  .status-content {
    @apply flex flex-col;
  }

  .status-value {
    @apply text-xl font-bold text-gray-900 dark:text-white;
  }

  .status-label {
    @apply text-sm text-gray-600 dark:text-gray-400;
  }

  /* Comparison Modal */
  .comparison-content {
    @apply space-y-6;
  }

  .comparison-chart {
    @apply h-96;
  }

  .comparison-table {
    @apply border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .header-content {
      @apply flex-col gap-4;
    }
    
    .vessel-selector {
      @apply flex-col gap-2;
    }
    
    .current-vessel {
      @apply flex-col items-start gap-2;
    }
    
    .kpi-grid {
      @apply grid-cols-1;
    }
    
    .main-content {
      @apply grid-cols-1 gap-4;
    }
    
    .fleet-stats {
      @apply grid-cols-1 gap-3;
    }
    
    .vessels-grid {
      @apply grid-cols-1;
    }
    
    .weather-grid {
      @apply grid-cols-2;
    }
    
    .status-grid {
      @apply grid-cols-1 gap-4;
    }
  }

  @media (max-width: 640px) {
    .production-summary {
      @apply grid-cols-2;
    }
    
    .equipment-legend {
      @apply grid-cols-1;
    }
    
    .actions-grid {
      @apply grid-cols-1;
    }
    
    .fleet-stats {
      @apply grid-cols-2;
    }
  }
  </style>