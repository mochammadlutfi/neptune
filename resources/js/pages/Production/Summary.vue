<template>
  <div class="content">
    <!-- Modern Page Header -->
    <PageHeader :title="$t('production.summary.title')">
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
      <!-- Key Metrics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Oil Production -->
        <StatisticCard
          :title="$t('production.summary.oil_production')"
          :value="`${formatNumber(summaryData?.total_oil_bbl || 0)} ${$t('units.bbl')}`"
          :subtitle="$t('production.summary.daily_production')"
          icon="mdi:oil"
          :trend="oilTrendData"
          color="yellow"
          format="text"
          :loading="isLoading"
        />

        <!-- Gas Production -->
        <StatisticCard
          :title="$t('production.summary.gas_production')"
          :value="`${formatNumber(summaryData?.total_gas_mmscf || 0, 3)} ${$t('units.mmscf')}`"
          :subtitle="$t('production.summary.daily_production')"
          icon="mdi:gas-station"
          :trend="gasTrendData"
          color="blue"
          format="text"
          :loading="isLoading"
        />

        <!-- Water Cut -->
        <StatisticCard
          :title="$t('production.summary.water_cut')"
          :value="summaryData?.water_cut_pct || 0"
          :subtitle="$t('production.summary.percentage')"
          icon="mdi:water-percent"
          :trend="waterCutTrendData"
          color="gray"
          format="percent"
          :loading="isLoading"
        />

        <!-- Equipment Availability -->
        <StatisticCard
          :title="$t('production.summary.equipment_availability')"
          :value="summaryData?.equipment_availability_pct || 0"
          :subtitle="$t('production.summary.percentage')"
          icon="mdi:cog"
          :trend="equipmentTrendData"
          color="green"
          format="percent"
          :loading="isLoading"
        />
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Production Trend Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('production.summary.production_trend') }}
            </h3>
            <el-select v-model="chartPeriod" @change="updateChartData" class="w-32">
              <el-option label="7 Days" value="7d" />
              <el-option label="30 Days" value="30d" />
              <el-option label="90 Days" value="90d" />
            </el-select>
          </div>
          <v-chart 
            :option="productionChartOptions" 
            :loading="isChartLoading"
            class="h-80"
          />
        </div>

        <!-- Gas Balance Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('production.summary.gas_balance') }}
            </h3>
          </div>
          <v-chart 
            :option="gasBalanceChartOptions" 
            :loading="isChartLoading"
            class="h-80"
          />
        </div>
      </div>

      <!-- Well Performance Table -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('production.summary.well_performance') }}
            </h3>
            <el-button size="small" @click="viewAllWells">
              {{ $t('common.view_all') }}
            </el-button>
          </div>
        </div>
        
        <div class="p-6">
          <el-table 
            :data="wellData" 
            :loading="isWellDataLoading"
            class="w-full"
            stripe
          >
            <el-table-column 
              prop="well_name" 
              :label="$t('production.wells.well_name')"
              width="120"
            />
            <el-table-column 
              prop="oil_rate_bph" 
              :label="$t('production.wells.oil_rate')"
              align="right"
              width="130"
            >
              <template #default="{ row }">
                <span class="font-mono">
                  {{ formatNumber(row.oil_rate_bph) }}
                </span>
                <span class="text-xs text-gray-500 ml-1">BPH</span>
              </template>
            </el-table-column>
            
            <el-table-column 
              prop="gas_rate_mscfh" 
              :label="$t('production.wells.gas_rate')"
              align="right"
              width="130"
            >
              <template #default="{ row }">
                <span class="font-mono">
                  {{ formatNumber(row.gas_rate_mscfh, 3) }}
                </span>
                <span class="text-xs text-gray-500 ml-1">MSCFH</span>
              </template>
            </el-table-column>
            
            <el-table-column 
              prop="water_cut_percent" 
              :label="$t('production.wells.water_cut')"
              align="right"
              width="120"
            >
              <template #default="{ row }">
                <span class="font-mono" :class="getWaterCutClass(row.water_cut_percent)">
                  {{ formatNumber(row.water_cut_percent) }}%
                </span>
              </template>
            </el-table-column>
            
            <el-table-column 
              prop="wellhead_pressure_psi" 
              :label="$t('production.wells.wellhead_pressure')"
              align="right"
              width="150"
            >
              <template #default="{ row }">
                <span class="font-mono">
                  {{ formatNumber(row.wellhead_pressure_psi) }}
                </span>
                <span class="text-xs text-gray-500 ml-1">PSI</span>
              </template>
            </el-table-column>
            
            <el-table-column 
              prop="status" 
              :label="$t('common.status')"
              width="120"
            >
              <template #default="{ row }">
                <el-tag 
                  :type="getWellStatusType(row.status)"
                  effect="light"
                  size="small"
                >
                  {{ $t(`production.wells.status.${row.status.toLowerCase()}`) }}
                </el-tag>
              </template>
            </el-table-column>
            
            <el-table-column 
              :label="$t('common.actions')" 
              width="120"
              fixed="right"
            >
              <template #default="{ row }">
                <el-button
                  type="text"
                  size="small"
                  @click="viewWellDetails(row)"
                >
                  <Icon icon="mdi:eye" class="mr-1" />
                  {{ $t('common.view') }}
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </div>

      <!-- Equipment Status Grid -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ $t('production.summary.equipment_status') }}
            </h3>
            <el-button size="small" @click="viewAllEquipment">
              {{ $t('common.view_all') }}
            </el-button>
          </div>
        </div>
        
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <div 
              v-for="equipment in criticalEquipment" 
              :key="equipment.id"
              class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
            >
              <div class="flex items-center justify-between mb-2">
                <h4 class="font-medium text-gray-900 truncate">
                  {{ equipment.equipment_name }}
                </h4>
                <el-tag 
                  :type="getEquipmentStatusType(equipment.operational_status)"
                  effect="light"
                  size="small"
                >
                  {{ equipment.operational_status }}
                </el-tag>
              </div>
              <p class="text-sm text-gray-600 mb-2">{{ equipment.equipment_tag }}</p>
              <div class="text-xs text-gray-500">
                {{ $t('common.last_updated') }}: 
                {{ formatDate(equipment.reading_datetime) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Metrics -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personnel on Board -->
        <StatisticCard
          :title="$t('production.summary.personnel_on_board')"
          :value="summaryData?.total_pob || 0"
          :subtitle="$t('common.persons')"
          icon="mdi:account-group"
          color="purple"
          format="number"
          :loading="isLoading"
        />

        <!-- Safety Performance -->
        <StatisticCard
          :title="$t('production.summary.safety_incidents')"
          :value="summaryData?.safety_incidents || 0"
          :subtitle="$t('production.summary.incidents_today')"
          icon="mdi:shield-check"
          color="green"
          format="number"
          :loading="isLoading"
        />

        <!-- Production Efficiency -->
        <StatisticCard
          :title="$t('production.summary.efficiency')"
          :value="productionEfficiency"
          :subtitle="$t('production.summary.vs_target')"
          icon="mdi:speedometer"
          color="indigo"
          format="percent"
          :loading="isLoading"
        />
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

// Import reusable components
import PageHeader from '@/components/PageHeader.vue';
import StatisticCard from '@/components/StatisticCard.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';

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

const { formatDate, formatNumber } = useFormatter();
const { t } = useI18n();
const router = useRouter();

// Reactive state
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const selectedVessel = ref(null);
const chartPeriod = ref('7d');
const isExporting = ref(false);
const isChartLoading = ref(false);
const isWellDataLoading = ref(false);

// Data refs
const summaryData = ref(null);
const wellData = ref([]);
const criticalEquipment = ref([]);
const vessels = ref([]);
const trendData = ref([]);

// API calls
const fetchSummaryData = async () => {
  const response = await axios.get('/production/summary', {
    params: {
      date: selectedDate.value,
      vessel_id: selectedVessel.value,
    }
  });
  return response.data;
};

const fetchWellData = async () => {
  const response = await axios.get('/production/wells/performance', {
    params: {
      date: selectedDate.value,
      vessel_id: selectedVessel.value,
    }
  });
  return response.data;
};

const fetchEquipmentData = async () => {
  const response = await axios.get('/equipment/critical-status', {
    params: {
      vessel_id: selectedVessel.value,
    }
  });
  return response.data;
};

const fetchTrendData = async () => {
  const response = await axios.get('/production/trends', {
    params: {
      period: chartPeriod.value,
      vessel_id: selectedVessel.value,
    }
  });
  return response.data;
};

// Vue Query for data fetching
const { data: summaryResponse, isLoading, refetch } = useQuery({
  queryKey: ['productionSummary', selectedDate, selectedVessel],
  queryFn: fetchSummaryData,
  enabled: computed(() => !!selectedVessel.value),
});

// Computed properties for trend data
const oilTrendData = computed(() => {
  if (!trendData.value.oil_trend) return null;
  const trend = trendData.value.oil_trend;
  return {
    direction: trend > 0 ? 'up' : 'down',
    value: `${Math.abs(trend).toFixed(1)}%`,
    label: t('production.summary.vs_yesterday')
  };
});

const gasTrendData = computed(() => {
  if (!trendData.value.gas_trend) return null;
  const trend = trendData.value.gas_trend;
  return {
    direction: trend > 0 ? 'up' : 'down',
    value: `${Math.abs(trend).toFixed(1)}%`,
    label: t('production.summary.vs_yesterday')
  };
});

const waterCutTrendData = computed(() => {
  if (!trendData.value.water_cut_trend) return null;
  const trend = trendData.value.water_cut_trend;
  // For water cut, lower is better, so we reverse the direction
  return {
    direction: trend > 0 ? 'down' : 'up',
    value: `${Math.abs(trend).toFixed(1)}%`,
    label: t('production.summary.vs_yesterday')
  };
});

const equipmentTrendData = computed(() => {
  if (!trendData.value.equipment_trend) return null;
  const trend = trendData.value.equipment_trend;
  return {
    direction: trend > 0 ? 'up' : 'down',
    value: `${Math.abs(trend).toFixed(1)}%`,
    label: t('production.summary.vs_yesterday')
  };
});

const productionEfficiency = computed(() => {
  if (!summaryData.value) return 0;
  const target = 100; // This should come from vessel configuration
  const actual = (summaryData.value.total_oil_bbl || 0) + (summaryData.value.total_gas_mmscf || 0);
  return (actual / target) * 100;
});

// Chart options
const productionChartOptions = computed(() => ({
  title: {
    text: '',
  },
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'cross',
    },
  },
  legend: {
    data: ['Oil (BBL)', 'Gas (MMSCF)', 'Water Cut (%)'],
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
  yAxis: [
    {
      type: 'value',
      name: 'Production',
      position: 'left',
    },
    {
      type: 'value',
      name: 'Water Cut (%)',
      position: 'right',
      max: 100,
    },
  ],
  series: [
    {
      name: 'Oil (BBL)',
      type: 'line',
      data: trendData.value.oil_data || [],
      smooth: true,
      itemStyle: { color: '#f59e0b' },
    },
    {
      name: 'Gas (MMSCF)',
      type: 'line',
      data: trendData.value.gas_data || [],
      smooth: true,
      itemStyle: { color: '#3b82f6' },
    },
    {
      name: 'Water Cut (%)',
      type: 'line',
      yAxisIndex: 1,
      data: trendData.value.water_cut_data || [],
      smooth: true,
      itemStyle: { color: '#06b6d4' },
    },
  ],
}));

const gasBalanceChartOptions = computed(() => ({
  title: {
    text: '',
  },
  tooltip: {
    trigger: 'item',
    formatter: '{a} <br/>{b}: {c} MMSCF ({d}%)',
  },
  legend: {
    orient: 'vertical',
    left: 'left',
  },
  series: [
    {
      name: 'Gas Balance',
      type: 'pie',
      radius: '50%',
      data: [
        { 
          value: summaryData.value?.gas_export_mmscf || 0, 
          name: 'Export',
          itemStyle: { color: '#10b981' }
        },
        { 
          value: summaryData.value?.gas_fuel_mmscf || 0, 
          name: 'Fuel Gas',
          itemStyle: { color: '#f59e0b' }
        },
        { 
          value: summaryData.value?.gas_flare_mmscf || 0, 
          name: 'Flare',
          itemStyle: { color: '#ef4444' }
        },
      ],
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

const exportData = async () => {
  isExporting.value = true;
  try {
    const response = await axios.get('/production/summary/export', {
      params: {
        date: selectedDate.value,
        vessel_id: selectedVessel.value,
      },
      responseType: 'blob',
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `production-summary-${selectedDate.value}.xlsx`);
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

const loadAllData = async () => {
  if (!selectedVessel.value) return;
  
  try {
    const [summary, wells, equipment, trends] = await Promise.all([
      fetchSummaryData(),
      fetchWellData(),
      fetchEquipmentData(),
      fetchTrendData(),
    ]);
    
    summaryData.value = summary.data;
    wellData.value = wells.data;
    criticalEquipment.value = equipment.data;
    trendData.value = trends.data;
  } catch (error) {
    ElMessage.error(t('common.load_failed'));
  }
};

const updateChartData = () => {
  isChartLoading.value = true;
  fetchTrendData()
    .then(response => {
      trendData.value = response.data;
    })
    .finally(() => {
      isChartLoading.value = false;
    });
};

const viewAllWells = () => {
  router.push({ name: 'production.wells.index' });
};

const viewAllEquipment = () => {
  router.push({ name: 'equipment.status.index' });
};

const viewWellDetails = (well) => {
  router.push({ 
    name: 'production.wells.show', 
    params: { id: well.id }
  });
};

// Helper methods
const getWaterCutClass = (waterCut) => {
  if (waterCut < 20) return 'text-green-600';
  if (waterCut < 50) return 'text-yellow-600';
  return 'text-red-600';
};

const getWellStatusType = (status) => {
  const statusMap = {
    'Flowing': 'success',
    'Shut-in': 'warning',
    'Maintenance': 'info',
    'Problem': 'danger',
  };
  return statusMap[status] || 'info';
};

const getEquipmentStatusType = (status) => {
  const statusMap = {
    'Running': 'success',
    'Stopped': 'danger',
    'Maintenance': 'warning',
    'Standby': 'info',
  };
  return statusMap[status] || 'info';
};

// Load vessels on mount
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

// Watchers
watch(summaryResponse, (newData) => {
  if (newData) {
    summaryData.value = newData.data;
  }
});

// Lifecycle
onMounted(async () => {
  await loadVessels();
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

/* Equipment grid hover effects */
.equipment-card {
  @apply transition-all duration-200 hover:shadow-lg hover:border-blue-300;
}

/* Responsive table */
@media (max-width: 768px) {
  .el-table {
    font-size: 0.875rem;
  }
}
</style>