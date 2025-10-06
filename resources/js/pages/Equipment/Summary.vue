<template>
  <div class="content">
    <!-- Modern Page Header -->
    <PageHeader :title="$t('equipment.summary.title')">
      <template #actions>
        <div class="flex gap-3">
          <!-- Date Picker -->
          <el-date-picker
            v-model="selectedDate"
            type="date"
            :placeholder="$t('common.select_date')"
            format="YYYY-MM-DD"
            value-format="YYYY-MM-DD"
            @change="handleDateChange"
            class="w-40"
          />
          
          <!-- Vessel Selector -->
          <el-select
            v-model="selectedVessel"
            :placeholder="$t('common.select_vessel')"
            @change="handleVesselChange"
            class="w-48"
          >
            <el-option
              v-for="vessel in vessels"
              :key="vessel.id"
              :label="vessel.name"
              :value="vessel.id"
            />
          </el-select>
          
          <!-- Filter by Equipment Type -->
          <el-select
            v-model="selectedEquipmentType"
            :placeholder="$t('equipment.filter_by_type')"
            @change="applyFilters"
            clearable
            class="w-48"
          >
            <el-option
              v-for="type in equipmentTypes"
              :key="type"
              :label="type"
              :value="type"
            />
          </el-select>
          
          <!-- Action Buttons -->
          <el-button type="primary" @click="refreshData" :loading="isLoading">
            <Icon icon="mdi:refresh" class="mr-2" />
            {{ $t('common.refresh') }}
          </el-button>
          
          <el-button @click="exportData" :loading="isExporting">
            <Icon icon="mdi:file-export" class="mr-2" />
            {{ $t('common.export') }}
          </el-button>
        </div>
      </template>
    </PageHeader>

    <!-- Loading State -->
    <div v-if="isLoading" class="space-y-6">
      <SkeletonTable />
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-6">
      <!-- Equipment Overview Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Equipment -->
        <StatisticCard
          :title="$t('equipment.summary.total_equipment')"
          :value="equipmentSummary?.total_equipment || 0"
          :subtitle="$t('equipment.summary.registered_equipment')"
          icon="mdi:cog-outline"
          color="blue"
          format="number"
          :loading="isLoading"
        />

        <!-- Running Equipment -->
        <StatisticCard
          :title="$t('equipment.summary.running_equipment')"
          :value="equipmentSummary?.equipment_running || 0"
          :subtitle="$t('equipment.summary.currently_running')"
          icon="mdi:cog"
          :trend="runningTrendData"
          color="green"
          format="number"
          :loading="isLoading"
        />

        <!-- Equipment Availability -->
        <StatisticCard
          :title="$t('equipment.summary.availability')"
          :value="equipmentSummary?.equipment_availability_pct || 0"
          :subtitle="$t('equipment.summary.overall_availability')"
          icon="mdi:chart-line"
          :trend="availabilityTrendData"
          color="indigo"
          format="percent"
          :loading="isLoading"
        />

        <!-- Critical Equipment Issues -->
        <StatisticCard
          :title="$t('equipment.summary.critical_issues')"
          :value="criticalIssuesCount"
          :subtitle="$t('equipment.summary.requiring_attention')"
          icon="mdi:alert-circle"
          color="red"
          format="number"
          :loading="isLoading"
        />
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Equipment Status Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('equipment.summary.status_distribution') }}
            </h3>
          </div>
          <v-chart 
            :option="statusDistributionOptions" 
            :loading="isChartLoading"
            class="h-80"
          />
        </div>

        <!-- Equipment Availability Trend -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('equipment.summary.availability_trend') }}
            </h3>
            <el-select v-model="trendPeriod" @change="updateTrendChart" class="w-32">
              <el-option label="7 Days" value="7d" />
              <el-option label="30 Days" value="30d" />
              <el-option label="90 Days" value="90d" />
            </el-select>
          </div>
          <v-chart 
            :option="availabilityTrendOptions" 
            :loading="isChartLoading"
            class="h-80"
          />
        </div>
      </div>

      <!-- Critical Equipment Monitoring -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('equipment.summary.critical_equipment') }}
            </h3>
            <div class="flex items-center gap-3">
              <el-switch
                v-model="showOnlyCritical"
                :active-text="$t('equipment.summary.critical_only')"
                @change="applyFilters"
              />
              <el-button size="small" @click="viewAllEquipment">
                {{ $t('common.view_all') }}
              </el-button>
            </div>
          </div>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div 
              v-for="equipment in filteredCriticalEquipment" 
              :key="equipment.id"
              class="border rounded-lg p-4 transition-all duration-200 hover:shadow-md cursor-pointer"
              :class="getEquipmentCardClass(equipment.operational_status)"
              @click="viewEquipmentDetails(equipment)"
            >
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                  <Icon 
                    :icon="getEquipmentIcon(equipment.equipment_type)" 
                    class="text-2xl mr-2"
                    :class="getEquipmentIconColor(equipment.operational_status)"
                  />
                  <div>
                    <h4 class="font-medium text-gray-900 text-sm truncate">
                      {{ equipment.equipment_name }}
                    </h4>
                    <p class="text-xs text-gray-500">{{ equipment.equipment_tag }}</p>
                  </div>
                </div>
                <el-tag 
                  :type="getEquipmentStatusType(equipment.operational_status)"
                  effect="light"
                  size="small"
                >
                  {{ equipment.operational_status }}
                </el-tag>
              </div>
              
              <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                  <span class="text-gray-600">{{ $t('equipment.running_hours') }}:</span>
                  <span class="font-mono">{{ formatNumber(equipment.running_hours || 0) }}h</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">{{ $t('common.last_updated') }}:</span>
                  <span>{{ formatDateTime(equipment.reading_datetime) }}</span>
                </div>
                <div v-if="equipment.is_critical" class="flex items-center text-red-600">
                  <Icon icon="mdi:star" class="mr-1" />
                  <span>{{ $t('equipment.critical') }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Equipment Status Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('equipment.summary.equipment_status') }}
            </h3>
            <div class="flex items-center gap-3">
              <!-- Search -->
              <el-input
                v-model="searchQuery"
                :placeholder="$t('equipment.search_equipment')"
                @input="handleSearch"
                prefix-icon="Search"
                class="w-64"
                clearable
              />
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <el-table 
            :data="filteredEquipmentList" 
            :loading="isTableLoading"
            stripe
            class="w-full"
            @sort-change="handleSortChange"
          >
            <el-table-column 
              prop="equipment_name" 
              :label="$t('equipment.equipment_name')"
              width="200"
              sortable="custom"
            >
              <template #default="{ row }">
                <div class="flex items-center">
                  <Icon 
                    :icon="getEquipmentIcon(row.equipment_type)" 
                    class="text-lg mr-2"
                    :class="getEquipmentIconColor(row.operational_status)"
                  />
                  <div>
                    <div class="font-medium text-gray-900">{{ row.equipment_name }}</div>
                    <div class="text-xs text-gray-500">{{ row.equipment_tag }}</div>
                  </div>
                </div>
              </template>
            </el-table-column>
            
            <el-table-column 
              prop="equipment_type" 
              :label="$t('equipment.type')"
              width="150"
              sortable="custom"
            />
            
            <el-table-column 
              prop="operational_status" 
              :label="$t('equipment.status')"
              width="120"
              sortable="custom"
            >
              <template #default="{ row }">
                <el-tag 
                  :type="getEquipmentStatusType(row.operational_status)"
                  effect="light"
                  size="small"
                >
                  {{ row.operational_status }}
                </el-tag>
              </template>
            </el-table-column>
            
            <el-table-column 
              prop="running_hours" 
              :label="$t('equipment.running_hours')"
              align="right"
              width="130"
              sortable="custom"
            >
              <template #default="{ row }">
                <span class="font-mono">
                  {{ formatNumber(row.running_hours || 0) }}h
                </span>
              </template>
            </el-table-column>
            
            <el-table-column 
              prop="shift_operator" 
              :label="$t('equipment.operator')"
              width="150"
            />
            
            <el-table-column 
              prop="reading_datetime" 
              :label="$t('common.last_updated')"
              width="150"
              sortable="custom"
            >
              <template #default="{ row }">
                {{ formatDateTime(row.reading_datetime) }}
              </template>
            </el-table-column>
            
            <el-table-column 
              prop="is_critical" 
              :label="$t('equipment.critical')"
              width="80"
              align="center"
            >
              <template #default="{ row }">
                <Icon 
                  v-if="row.is_critical" 
                  icon="mdi:star" 
                  class="text-red-500" 
                />
              </template>
            </el-table-column>
            
            <el-table-column 
              :label="$t('common.actions')" 
              width="150"
              fixed="right"
            >
              <template #default="{ row }">
                <div class="flex gap-1">
                  <el-button
                    type="text"
                    size="small"
                    @click="viewEquipmentDetails(row)"
                  >
                    <Icon icon="mdi:eye" class="mr-1" />
                    {{ $t('common.view') }}
                  </el-button>
                  <el-button
                    type="text"
                    size="small"
                    @click="viewMaintenanceHistory(row)"
                  >
                    <Icon icon="mdi:wrench" class="mr-1" />
                    {{ $t('equipment.maintenance') }}
                  </el-button>
                </div>
              </template>
            </el-table-column>
          </el-table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
          <Pagination
            :current-page="pagination.page"
            :page-size="pagination.limit"
            :total="pagination.total"
            @current-change="handlePageChange"
            @size-change="handlePerPageChange"
          />
        </div>
      </div>

      <!-- Maintenance Schedule & Alerts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Maintenance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('equipment.summary.upcoming_maintenance') }}
            </h3>
            <el-button size="small" @click="viewMaintenanceSchedule">
              {{ $t('common.view_all') }}
            </el-button>
          </div>
          
          <div class="space-y-3">
            <div 
              v-for="maintenance in upcomingMaintenance" 
              :key="maintenance.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center">
                <Icon 
                  :icon="getMaintenanceIcon(maintenance.work_type)" 
                  class="text-lg mr-3"
                  :class="getMaintenancePriorityColor(maintenance.days_until)"
                />
                <div>
                  <h4 class="font-medium text-gray-900 text-sm">
                    {{ maintenance.equipment_name }}
                  </h4>
                  <p class="text-xs text-gray-600">{{ maintenance.work_type }}</p>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm font-medium" :class="getMaintenancePriorityColor(maintenance.days_until)">
                  {{ maintenance.days_until }} {{ $t('common.days') }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ formatDate(maintenance.scheduled_date) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Equipment Alerts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('equipment.summary.active_alerts') }}
            </h3>
            <el-button size="small" @click="viewAllAlerts">
              {{ $t('common.view_all') }}
            </el-button>
          </div>
          
          <div class="space-y-3">
            <div 
              v-for="alert in activeAlerts" 
              :key="alert.id"
              class="flex items-center justify-between p-3 rounded-lg"
              :class="getAlertBackgroundClass(alert.severity)"
            >
              <div class="flex items-center">
                <Icon 
                  :icon="getAlertIcon(alert.severity)" 
                  class="text-lg mr-3"
                  :class="getAlertIconColor(alert.severity)"
                />
                <div>
                  <h4 class="font-medium text-gray-900 text-sm">
                    {{ alert.equipment_name }}
                  </h4>
                  <p class="text-xs text-gray-600">{{ alert.alert_message }}</p>
                </div>
              </div>
              <div class="text-right">
                <el-tag 
                  :type="getAlertTagType(alert.severity)"
                  effect="light"
                  size="small"
                >
                  {{ alert.severity }}
                </el-tag>
                <div class="text-xs text-gray-500 mt-1">
                  {{ formatDateTime(alert.created_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { LineChart, BarChart, PieChart } from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent,
} from 'echarts/components';
import VChart from 'vue-echarts';
import { useQuery } from '@tanstack/vue-query';
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/common/useFormatter';
import { useRouter } from 'vue-router';
import _ from 'lodash';

// Import reusable components
import PageHeader from '@/components/PageHeader.vue';
import StatisticCard from '@/components/StatisticCard.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';
import Pagination from '@/components/Pagination.vue';

// Register ECharts components
use([
  CanvasRenderer,
  LineChart,
  BarChart,
  PieChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  GridComponent,
]);

const { formatDate, formatDateTime, formatNumber } = useFormatter();
const { t } = useI18n();
const router = useRouter();

// Reactive state
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const selectedVessel = ref(null);
const selectedEquipmentType = ref(null);
const trendPeriod = ref('7d');
const showOnlyCritical = ref(false);
const searchQuery = ref('');
const isExporting = ref(false);
const isChartLoading = ref(false);
const isTableLoading = ref(false);

// Pagination
const pagination = ref({
  page: 1,
  limit: 25,
  total: 0,
});

// Data refs
const equipmentSummary = ref(null);
const equipmentList = ref([]);
const criticalEquipment = ref([]);
const upcomingMaintenance = ref([]);
const activeAlerts = ref([]);
const vessels = ref([]);
const equipmentTypes = ref([]);
const trendData = ref([]);

// API calls
const fetchEquipmentSummary = async () => {
  const response = await axios.get('/equipment/summary', {
    params: {
      date: selectedDate.value,
      vessel_id: selectedVessel.value,
    }
  });
  return response.data;
};

const fetchEquipmentList = async () => {
  const response = await axios.get('/equipment/status', {
    params: {
      vessel_id: selectedVessel.value,
      equipment_type: selectedEquipmentType.value,
      critical_only: showOnlyCritical.value,
      search: searchQuery.value,
      page: pagination.value.page,
      limit: pagination.value.limit,
    }
  });
  return response.data;
};

const fetchMaintenanceSchedule = async () => {
  const response = await axios.get('/equipment/maintenance/upcoming', {
    params: {
      vessel_id: selectedVessel.value,
      days_ahead: 30,
    }
  });
  return response.data;
};

const fetchActiveAlerts = async () => {
  const response = await axios.get('/equipment/alerts/active', {
    params: {
      vessel_id: selectedVessel.value,
    }
  });
  return response.data;
};

const fetchTrendData = async () => {
  const response = await axios.get('/equipment/availability-trend', {
    params: {
      vessel_id: selectedVessel.value,
      period: trendPeriod.value,
    }
  });
  return response.data;
};

// Vue Query for main data
const { data: summaryResponse, isLoading, refetch } = useQuery({
  queryKey: ['equipmentSummary', selectedDate, selectedVessel],
  queryFn: fetchEquipmentSummary,
  enabled: computed(() => !!selectedVessel.value),
});

// Computed properties
const runningTrendData = computed(() => {
  if (!trendData.value.running_trend) return null;
  const trend = trendData.value.running_trend;
  return {
    direction: trend > 0 ? 'up' : 'down',
    value: `${Math.abs(trend).toFixed(1)}%`,
    label: t('equipment.summary.vs_yesterday')
  };
});

const availabilityTrendData = computed(() => {
  if (!trendData.value.availability_trend) return null;
  const trend = trendData.value.availability_trend;
  return {
    direction: trend > 0 ? 'up' : 'down',
    value: `${Math.abs(trend).toFixed(1)}%`,
    label: t('equipment.summary.vs_yesterday')
  };
});

const criticalIssuesCount = computed(() => {
  return activeAlerts.value.filter(alert => 
    alert.severity === 'Critical' || alert.severity === 'High'
  ).length;
});

const filteredCriticalEquipment = computed(() => {
  let filtered = criticalEquipment.value;
  
  if (showOnlyCritical.value) {
    filtered = filtered.filter(eq => eq.is_critical);
  }
  
  if (selectedEquipmentType.value) {
    filtered = filtered.filter(eq => eq.equipment_type === selectedEquipmentType.value);
  }
  
  return filtered.slice(0, 12); // Show only first 12 for grid
});

const filteredEquipmentList = computed(() => {
  let filtered = equipmentList.value;
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(eq => 
      eq.equipment_name.toLowerCase().includes(query) ||
      eq.equipment_tag.toLowerCase().includes(query) ||
      eq.equipment_type.toLowerCase().includes(query)
    );
  }
  
  return filtered;
});

// Chart options
const statusDistributionOptions = computed(() => ({
  title: {
    text: '',
  },
  tooltip: {
    trigger: 'item',
    formatter: '{a} <br/>{b}: {c} ({d}%)',
  },
  legend: {
    orient: 'vertical',
    left: 'left',
  },
  series: [
    {
      name: 'Equipment Status',
      type: 'pie',
      radius: '50%',
      data: [
        { 
          value: equipmentSummary.value?.status_running || 0, 
          name: 'Running',
          itemStyle: { color: '#10b981' }
        },
        { 
          value: equipmentSummary.value?.status_standby || 0, 
          name: 'Standby',
          itemStyle: { color: '#f59e0b' }
        },
        { 
          value: equipmentSummary.value?.status_shutdown || 0, 
          name: 'Shutdown',
          itemStyle: { color: '#6b7280' }
        },
        { 
          value: equipmentSummary.value?.status_maintenance || 0, 
          name: 'Maintenance',
          itemStyle: { color: '#ef4444' }
        },
      ],
    },
  ],
}));

const availabilityTrendOptions = computed(() => ({
  title: {
    text: '',
  },
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'cross',
    },
  },
  grid: {
    left: '3%',
    right: '4%',
    bottom: '3%',
    containLabel: true,
  },
  xAxis: {
    type: 'category',
    boundaryGap: false,
    data: trendData.value.dates || [],
  },
  yAxis: {
    type: 'value',
    name: 'Availability (%)',
    min: 0,
    max: 100,
  },
  series: [
    {
      name: 'Availability',
      type: 'line',
      data: trendData.value.availability_data || [],
      smooth: true,
      itemStyle: { color: '#3b82f6' },
      areaStyle: {
        color: {
          type: 'linear',
          x: 0,
          y: 0,
          x2: 0,
          y2: 1,
          colorStops: [
            { offset: 0, color: 'rgba(59, 130, 246, 0.3)' },
            { offset: 1, color: 'rgba(59, 130, 246, 0.1)' }
          ]
        }
      },
    },
  ],
}));

// Methods
const handleDateChange = (date) => {
  selectedDate.value = date;
  loadAllData();
};

const handleVesselChange = (vesselId) => {
  selectedVessel.value = vesselId;
  loadAllData();
};

const refreshData = () => {
  loadAllData();
};

const applyFilters = () => {
  pagination.value.page = 1;
  loadEquipmentData();
};

const handleSearch = _.debounce(() => {
  pagination.value.page = 1;
  loadEquipmentData();
}, 500);

const handleSortChange = (sortObj) => {
  // Implement sorting logic
  loadEquipmentData();
};

const handlePageChange = (page) => {
  pagination.value.page = page;
  loadEquipmentData();
};

const handlePerPageChange = (perPage) => {
  pagination.value.limit = perPage;
  pagination.value.page = 1;
  loadEquipmentData();
};

const exportData = async () => {
  isExporting.value = true;
  try {
    const response = await axios.get('/equipment/summary/export', {
      params: {
        date: selectedDate.value,
        vessel_id: selectedVessel.value,
      },
      responseType: 'blob',
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `equipment-summary-${selectedDate.value}.xlsx`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    
    ElMessage.success(t('common.export_success'));
  } catch (error) {
    ElMessage.error(t('common.export_failed'));
  } finally {
    isExporting.value = false;
  }
};

const updateTrendChart = () => {
  isChartLoading.value = true;
  fetchTrendData()
    .then(response => {
      trendData.value = response.data;
    })
    .finally(() => {
      isChartLoading.value = false;
    });
};

const loadAllData = async () => {
  if (!selectedVessel.value) return;
  
  try {
    const [summary, equipment, maintenance, alerts, trends] = await Promise.all([
      fetchEquipmentSummary(),
      fetchEquipmentList(),
      fetchMaintenanceSchedule(),
      fetchActiveAlerts(),
      fetchTrendData(),
    ]);
    
    equipmentSummary.value = summary.data;
    equipmentList.value = equipment.data.data;
    pagination.value.total = equipment.data.total;
    criticalEquipment.value = equipment.data.critical || [];
    upcomingMaintenance.value = maintenance.data;
    activeAlerts.value = alerts.data;
    trendData.value = trends.data;
  } catch (error) {
    ElMessage.error(t('common.load_failed'));
  }
};

const loadEquipmentData = async () => {
  isTableLoading.value = true;
  try {
    const response = await fetchEquipmentList();
    equipmentList.value = response.data.data;
    pagination.value.total = response.data.total;
  } catch (error) {
    ElMessage.error(t('common.load_failed'));
  } finally {
    isTableLoading.value = false;
  }
};

// Navigation methods
const viewAllEquipment = () => {
  router.push({ name: 'equipment.status.index' });
};

const viewMaintenanceSchedule = () => {
  router.push({ name: 'equipment.maintenance.index' });
};

const viewAllAlerts = () => {
  router.push({ name: 'equipment.alerts.index' });
};

const viewEquipmentDetails = (equipment) => {
  router.push({ 
    name: 'equipment.status.show', 
    params: { id: equipment.id }
  });
};

const viewMaintenanceHistory = (equipment) => {
  router.push({ 
    name: 'equipment.maintenance.index',
    query: { equipment_id: equipment.id }
  });
};

// Helper methods
const getEquipmentIcon = (type) => {
  const iconMap = {
    'Compressor': 'mdi:pump',
    'Generator': 'mdi:lightning-bolt',
    'Pump': 'mdi:water-pump',
    'Engine': 'mdi:engine',
    'Turbine': 'mdi:fan',
    'Transformer': 'mdi:transmission-tower',
    'Crane': 'mdi:crane',
    'default': 'mdi:cog',
  };
  return iconMap[type] || iconMap.default;
};

const getEquipmentIconColor = (status) => {
  const colorMap = {
    'Running': 'text-green-600',
    'Standby': 'text-yellow-600',
    'Shutdown': 'text-gray-600',
    'Maintenance': 'text-red-600',
  };
  return colorMap[status] || 'text-gray-600';
};

const getEquipmentStatusType = (status) => {
  const typeMap = {
    'Running': 'success',
    'Standby': 'warning',
    'Shutdown': 'info',
    'Maintenance': 'danger',
  };
  return typeMap[status] || 'info';
};

const getEquipmentCardClass = (status) => {
  const classMap = {
    'Running': 'border-green-200 bg-green-50',
    'Standby': 'border-yellow-200 bg-yellow-50',
    'Shutdown': 'border-gray-200 bg-gray-50',
    'Maintenance': 'border-red-200 bg-red-50',
  };
  return classMap[status] || 'border-gray-200 bg-white';
};

const getMaintenanceIcon = (workType) => {
  const iconMap = {
    'Preventive': 'mdi:calendar-check',
    'Corrective': 'mdi:wrench',
    'Breakdown': 'mdi:alert-circle',
    'Inspection': 'mdi:magnify',
    'Calibration': 'mdi:tune',
  };
  return iconMap[workType] || 'mdi:wrench';
};

const getMaintenancePriorityColor = (daysUntil) => {
  if (daysUntil <= 3) return 'text-red-600';
  if (daysUntil <= 7) return 'text-yellow-600';
  return 'text-green-600';
};

const getAlertIcon = (severity) => {
  const iconMap = {
    'Critical': 'mdi:alert-circle',
    'High': 'mdi:alert',
    'Medium': 'mdi:information',
    'Low': 'mdi:information-outline',
  };
  return iconMap[severity] || 'mdi:information';
};

const getAlertIconColor = (severity) => {
  const colorMap = {
    'Critical': 'text-red-600',
    'High': 'text-orange-600',
    'Medium': 'text-yellow-600',
    'Low': 'text-blue-600',
  };
  return colorMap[severity] || 'text-gray-600';
};

const getAlertTagType = (severity) => {
  const typeMap = {
    'Critical': 'danger',
    'High': 'warning',
    'Medium': 'info',
    'Low': 'success',
  };
  return typeMap[severity] || 'info';
};

const getAlertBackgroundClass = (severity) => {
  const classMap = {
    'Critical': 'bg-red-50 border-red-200',
    'High': 'bg-orange-50 border-orange-200',
    'Medium': 'bg-yellow-50 border-yellow-200',
    'Low': 'bg-blue-50 border-blue-200',
  };
  return classMap[severity] || 'bg-gray-50 border-gray-200';
};

// Load vessels and equipment types on mount
const loadVessels = async () => {
  try {
    const response = await axios.get('/vessels');
    vessels.value = response.data.data;
    if (vessels.value.length > 0 && !selectedVessel.value) {
      selectedVessel.value = vessels.value[0].id;
    }
  } catch (error) {
    ElMessage.error(t('common.load_vessels_failed'));
  }
};

const loadEquipmentTypes = async () => {
  try {
    const response = await axios.get('/equipment/types');
    equipmentTypes.value = response.data.data;
  } catch (error) {
    ElMessage.error(t('common.load_failed'));
  }
};

// Watchers
watch(summaryResponse, (newData) => {
  if (newData) {
    equipmentSummary.value = newData.data;
  }
});

// Lifecycle
onMounted(async () => {
  await Promise.all([loadVessels(), loadEquipmentTypes()]);
  if (selectedVessel.value) {
    loadAllData();
  }
});
</script>

<style scoped>
/* Custom styles for better visual hierarchy */
.content {
  @apply space-y-6;
}

/* Chart container styles */
.echarts {
  width: 100% !important;
  height: 100% !important;
}

/* Equipment card hover effects */
.equipment-card {
  @apply transition-all duration-200 hover:shadow-lg;
}

/* Responsive table */
@media (max-width: 768px) {
  .el-table {
    font-size: 0.875rem;
  }
}

/* Custom scrollbar for equipment grid */
.equipment-grid {
  scrollbar-width: thin;
  scrollbar-color: #d1d5db #f3f4f6;
}

.equipment-grid::-webkit-scrollbar {
  width: 6px;
}

.equipment-grid::-webkit-scrollbar-track {
  background: #f3f4f6;
}

.equipment-grid::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}
</style>