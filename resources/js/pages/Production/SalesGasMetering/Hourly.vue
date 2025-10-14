<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('production.sales_gas_metering.hourly')" :primary-action="{
            label: $t('production.sales_gas_metering.create'),
            icon: 'mingcute:add-line',
            click: onCreate
        }" :subtitle="formatDate(params.date) + ' | ' + vesselName" />

        <!-- Data Completeness Section -->
        <el-card class="mb-6 !rounded-lg !shadow-md">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <Icon icon="mingcute:chart-bar-line" class="inline mr-2" />
                        {{ $t('production.sales_gas_metering.data_completeness') }}
                    </h3>
                    <el-tag :type="completenessTagType" size="large">
                        {{ completenessPercentage }}% {{ $t('common.actions.complete') }}
                    </el-tag>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-2">
                    <el-progress 
                        :percentage="completenessPercentage" 
                        :color="progressColor"
                        :stroke-width="24"
                        :show-text="false"
                    />
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>{{ completedHours }} {{ $t('common.pagination.of') }} 24 {{ $t('production.sales_gas_metering.hours_recorded') }}</span>
                        <span>{{ missingHours.length }} {{ $t('production.sales_gas_metering.hours_missing') }}</span>
                    </div>
                </div>

                <!-- Missing Hours Alert -->
                <el-alert
                    v-if="missingHours.length > 0"
                    type="warning"
                    :closable="false"
                    class="!rounded-lg"
                >
                    <template #title>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-semibold mb-2">
                                    ⚠️ {{ missingHours.length }} {{ $t('production.sales_gas_metering.hours_missing') }}
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <el-tag 
                                        v-for="hour in missingHours" 
                                        :key="hour"
                                        type="warning"
                                        size="small"
                                        effect="plain"
                                    >
                                        {{ hour }}
                                    </el-tag>
                                </div>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <el-button size="small" @click="fillMissingHours">
                                    <Icon icon="mingcute:inbox-line" class="mr-1" />
                                    {{ $t('production.sales_gas_metering.quick_fill') }}
                                </el-button>
                            </div>
                        </div>
                    </template>
                </el-alert>
            </div>
        </el-card>

        <!-- Daily Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Average Pressure Card -->
            <el-card class="!rounded-lg !shadow-md">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $t('production.sales_gas_metering.avg_pressure') }}</span>
                        <Icon icon="mingcute:chart-line" class="text-blue-500" />
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ formatNumber(dailySummary?.avg_pressure_psig) }}
                        <span class="text-sm font-normal text-gray-500">psig</span>
                    </div>
                    <div class="h-16">
                        <MiniChart 
                            :data="pressureChartData" 
                            color="#3b82f6"
                            type="line"
                        />
                    </div>
                    <div class="text-xs text-gray-500">
                        Range: {{ formatNumber(pressureRange.min) }} - {{ formatNumber(pressureRange.max) }} psig
                    </div>
                </div>
            </el-card>

            <!-- Average Temperature Card -->
            <el-card class="!rounded-lg !shadow-md">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $t('production.sales_gas_metering.avg_temperature') }}</span>
                        <Icon icon="mingcute:fire-line" class="text-orange-500" />
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ formatNumber(dailySummary?.avg_temperature_f) }}
                        <span class="text-sm font-normal text-gray-500">°F</span>
                    </div>
                    <div class="h-16">
                        <MiniChart 
                            :data="temperatureChartData" 
                            color="#f59e0b"
                            type="line"
                        />
                    </div>
                    <div class="text-xs text-gray-500">
                        Range: {{ formatNumber(temperatureRange.min) }} - {{ formatNumber(temperatureRange.max) }} °F
                    </div>
                </div>
            </el-card>

            <!-- Average Flow Rate Card -->
            <el-card class="!rounded-lg !shadow-md">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $t('production.sales_gas_metering.avg_flow_rate') }}</span>
                        <Icon icon="mingcute:waves-line" class="text-green-500" />
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ formatNumber(dailySummary?.avg_total_flow_rate) }}
                        <span class="text-sm font-normal text-gray-500">MMSCFD</span>
                    </div>
                    <div class="h-16">
                        <MiniChart 
                            :data="flowRateChartData" 
                            color="#10b981"
                            type="line"
                        />
                    </div>
                    <div class="text-xs text-gray-500">
                        Range: {{ formatNumber(flowRateRange.min) }} - {{ formatNumber(flowRateRange.max) }} MMSCFD
                    </div>
                </div>
            </el-card>
        </div>

        <!-- Main Table -->
        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $t('production.sales_gas_metering.hourly_readings_table') }}
                    </h3>
                    <div class="flex gap-2">
                        <el-input
                            v-model="searchQuery"
                            :placeholder="$t('common.search.search_placeholder')"
                            clearable
                            class="w-64"
                        >
                            <template #prefix>
                                <Icon icon="mingcute:search-line" />
                            </template>
                        </el-input>
                        <el-button @click="refetch">
                            <Icon icon="mingcute:refresh-line" />
                        </el-button>
                    </div>
                </div>
            </div>

            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <SkeletonTable />
                </template>
                <template #default>
                    <div class="overflow-x-auto">
                        <el-table 
                            :data="filteredData" 
                            class="base-table"
                            stripe
                            :row-class-name="getRowClassName"
                            @sort-change="sortChange"
                        >
                            <!-- Status Indicator -->
                            <el-table-column width="60" align="center">
                                <template #default="scope">
                                    <el-tooltip :content="scope.row.id ? 'Complete' : 'Missing'" placement="top">
                                        <Icon 
                                            :icon="scope.row.id ? 'mingcute:check-circle-fill' : 'mingcute:close-circle-fill'" 
                                            :class="scope.row.id ? 'text-green-500' : 'text-red-400'"
                                            class="text-xl"
                                        />
                                    </el-tooltip>
                                </template>
                            </el-table-column>

                            <!-- Hour -->
                            <el-table-column 
                                prop="time" 
                                :label="$t('production.sales_gas_metering.fields.time')" 
                                width="100"
                                fixed="left"
                            >
                                <template #default="scope">
                                    <span class="font-semibold">{{ formatTime(scope.row.time) }}</span>
                                </template>
                            </el-table-column>

                            <!-- Pressure -->
                            <el-table-column 
                                prop="pressure_psig" 
                                :label="$t('production.sales_gas_metering.fields.pressure_psig')" 
                                sortable
                                align="right"
                                width="140"
                            >
                                <template #header>
                                    <div class="text-right">
                                        <div class="font-semibold">Pressure</div>
                                        <div class="text-xs text-gray-500">psig</div>
                                    </div>
                                </template>
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.pressure_psig">
                                        {{ formatNumber(scope.row.pressure_psig, 2) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <!-- Temperature -->
                            <el-table-column 
                                prop="temperature_f" 
                                :label="$t('production.sales_gas_metering.fields.temperature_f')" 
                                sortable
                                align="right"
                                width="140"
                            >
                                <template #header>
                                    <div class="text-right">
                                        <div class="font-semibold">Temperature</div>
                                        <div class="text-xs text-gray-500">°F</div>
                                    </div>
                                </template>
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.temperature_f">
                                        {{ formatNumber(scope.row.temperature_f, 2) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <!-- H2O Content -->
                            <el-table-column 
                                prop="h2o_lb_mmscf" 
                                :label="$t('production.sales_gas_metering.fields.h2o_lb_mmscf')" 
                                sortable
                                align="right"
                                width="140"
                            >
                                <template #header>
                                    <div class="text-right">
                                        <div class="font-semibold">H₂O</div>
                                        <div class="text-xs text-gray-500">lb/MMSCF</div>
                                    </div>
                                </template>
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.h2o_lb_mmscf">
                                        {{ formatNumber(scope.row.h2o_lb_mmscf, 2) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <!-- CO2 -->
                            <el-table-column 
                                prop="co2_mol_pct" 
                                :label="$t('production.sales_gas_metering.fields.co2_mol_pct')" 
                                sortable
                                align="right"
                                width="120"
                            >
                                <template #header>
                                    <div class="text-right">
                                        <div class="font-semibold">CO₂</div>
                                        <div class="text-xs text-gray-500">mol %</div>
                                    </div>
                                </template>
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.co2_mol_pct">
                                        {{ formatNumber(scope.row.co2_mol_pct, 2) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <!-- GHV -->
                            <el-table-column 
                                prop="ghv" 
                                :label="$t('production.sales_gas_metering.fields.ghv')" 
                                sortable
                                align="right"
                                width="120"
                            >
                                <template #header>
                                    <div class="text-right">
                                        <div class="font-semibold">GHV</div>
                                        <div class="text-xs text-gray-500">BTU/SCF</div>
                                    </div>
                                </template>
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.ghv">
                                        {{ formatNumber(scope.row.ghv, 2) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <!-- Specific Gravity -->
                            <el-table-column 
                                prop="specific_gravity" 
                                :label="$t('production.sales_gas_metering.fields.specific_gravity')" 
                                sortable
                                align="right"
                                width="140"
                            >
                                <template #header>
                                    <div class="text-right">
                                        <div class="font-semibold">Spec. Gravity</div>
                                        <div class="text-xs text-gray-500">-</div>
                                    </div>
                                </template>
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.specific_gravity">
                                        {{ formatNumber(scope.row.specific_gravity, 4) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <!-- Flow Rate -->
                            <el-table-column 
                                prop="total_flow_rate" 
                                :label="$t('production.sales_gas_metering.fields.total_flow_rate')" 
                                sortable
                                align="right"
                                width="140"
                            >
                                <template #header>
                                    <div class="text-right">
                                        <div class="font-semibold">Flow Rate</div>
                                        <div class="text-xs text-gray-500">MMSCFD</div>
                                    </div>
                                </template>
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.total_flow_rate">
                                        {{ formatNumber(scope.row.total_flow_rate, 4) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <!-- Actions -->
                            <el-table-column 
                                :label="$t('common.actions.action', 2)" 
                                align="center" 
                                width="120"
                                fixed="right"
                            >
                                <template #default="scope">
                                    <div class="flex gap-1 justify-center">
                                        <el-button 
                                            v-if="scope.row.id"
                                            size="small" 
                                            circle 
                                            @click="onEdit(scope.row)"
                                        >
                                            <Icon icon="mingcute:edit-line" />
                                        </el-button>
                                        <el-button 
                                            v-else
                                            size="small" 
                                            type="primary"
                                            circle 
                                            @click="onAddHour(scope.row.time)"
                                        >
                                            <Icon icon="mingcute:add-line" />
                                        </el-button>
                                        <el-button 
                                            v-if="scope.row.id"
                                            size="small" 
                                            type="danger"
                                            circle 
                                            @click="onDelete(scope.row)"
                                        >
                                            <Icon icon="mingcute:delete-2-line" />
                                        </el-button>
                                    </div>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </template>
            </el-skeleton>
        </el-card>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import { useQuery } from '@tanstack/vue-query';
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/common/useFormatter';
import { useRouter, useRoute } from 'vue-router';
import PageHeader from '@/components/PageHeader.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';
import MiniChart from '@/components/MiniChart.vue'; // You'll need to cr
import { useUser } from '@/composables/auth/useUser'

const { formatTime, formatDate } = useFormatter();
const { userVesselId } = useUser();
const { t } = useI18n();
const router = useRouter();
const route = useRoute();

// Props from route params
const params = ref({
    vessel_id: userVesselId,
    date: route.params.date || route.query.date,
});

// State
const searchQuery = ref('');
const vesselName = ref('MOPU PW-8'); // Fetch from vessel data

// Fetch hourly data with daily summary
const fetchHourlyData = async ({ queryKey }) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/production/sales-gas-metering/hourly-detail", {
        params: queryParams,
    });
    return response.data;
};

const { data: hourlyData, isLoading, refetch } = useQuery({
    queryKey: ['gasMeteringHourly', params.value],
    queryFn: fetchHourlyData,
    keepPreviousData: true,
});

// Computed properties
const dailySummary = computed(() => hourlyData.value?.daily_summary || {});
const hourlyReadings = computed(() => hourlyData.value?.hourly_data || []);
const completedHours = computed(() => hourlyData.value?.completed_hours || 0);
const missingHours = computed(() => hourlyData.value?.missing_hours || []);

const completenessPercentage = computed(() => {
    return Math.round((completedHours.value / 24) * 100);
});

const completenessTagType = computed(() => {
    const pct = completenessPercentage.value;
    if (pct === 100) return 'success';
    if (pct >= 75) return 'warning';
    return 'danger';
});

const progressColor = computed(() => {
    const pct = completenessPercentage.value;
    if (pct === 100) return '#10b981';
    if (pct >= 75) return '#f59e0b';
    return '#ef4444';
});

const canApprove = computed(() => {
    return completenessPercentage.value === 100;
});

// Chart data
const pressureChartData = computed(() => {
    return hourlyReadings.value
        .filter(r => r.pressure_psig)
        .map(r => r.pressure_psig);
});

const temperatureChartData = computed(() => {
    return hourlyReadings.value
        .filter(r => r.temperature_f)
        .map(r => r.temperature_f);
});

const flowRateChartData = computed(() => {
    return hourlyReadings.value
        .filter(r => r.total_flow_rate)
        .map(r => r.total_flow_rate);
});

// Range calculations
const pressureRange = computed(() => {
    const values = pressureChartData.value;
    if (values.length === 0) return { min: 0, max: 0 };
    return {
        min: Math.min(...values),
        max: Math.max(...values)
    };
});

const temperatureRange = computed(() => {
    const values = temperatureChartData.value;
    if (values.length === 0) return { min: 0, max: 0 };
    return {
        min: Math.min(...values),
        max: Math.max(...values)
    };
});

const flowRateRange = computed(() => {
    const values = flowRateChartData.value;
    if (values.length === 0) return { min: 0, max: 0 };
    return {
        min: Math.min(...values),
        max: Math.max(...values)
    };
});

// Filtered data for search
const filteredData = computed(() => {
    if (!searchQuery.value) return hourlyReadings.value;
    
    const query = searchQuery.value.toLowerCase();
    return hourlyReadings.value.filter(row => {
        return Object.values(row).some(val => 
            String(val).toLowerCase().includes(query)
        );
    });
});

// Helper methods
const formatNumber = (value, decimals = 2) => {
    if (value === null || value === undefined) return '-';
    return Number(value).toFixed(decimals);
};

const formatHour = (hour) => {
    return `${String(hour).padStart(2, '0')}:00`;
};

const isHourComplete = (hour) => {
    const hourStr = formatHour(hour);
    return hourlyReadings.value.some(r => r.time === hourStr && r.id);
};

const getHourStatusClass = (hour) => {
    const complete = isHourComplete(hour);
    return complete 
        ? 'bg-green-500 text-white' 
        : 'bg-red-100 text-red-600 border border-red-300';
};

const getRowClassName = ({ row }) => {
    if (!row.id) return 'row-missing';
    return 'row-complete';
};

const scrollToHour = (hour) => {
    // Implement smooth scroll to specific hour in table
    ElMessage.info(`Scroll to hour ${hour}:00`);
};

// Actions
const onCreate = () => {
    router.push({ 
        name: 'production.sales_gas_metering.create',
        query: {
            vessel_id: params.value.vessel_id,
            date: params.value.date
        }
    });
};

const onAddHour = (time) => {
    router.push({ 
        name: 'production.sales_gas_metering.create',
        query: {
            vessel_id: params.value.vessel_id,
            date: params.value.date,
            time: time
        }
    });
};

const onEdit = (row) => {
    router.push({ 
        name: 'production.sales_gas_metering.edit', 
        params: { id: row.id } 
    });
};

const onDelete = async (row) => {
    try {
        await ElMessageBox.confirm(
            t('common.confirmations.delete.message'),
            t('common.confirmations.delete.title'),
            {
                confirmButtonText: t('common.actions.delete'),
                cancelButtonText: t('common.actions.cancel'),
                type: 'warning',
            }
        );

        await axios.delete(`/production/sales-gas-metering/${row.id}`);
        ElMessage.success(t('common.messages.deleted'));
        refetch();
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error(t('common.error.delete_failed'));
        }
    }
};

const fillMissingHours = () => {
    ElMessageBox.confirm(
        'Fill missing hours with previous hour values?',
        'Quick Fill',
        {
            confirmButtonText: 'Fill',
            cancelButtonText: 'Cancel',
            type: 'info',
        }
    ).then(() => {
        ElMessage.info('Feature coming soon');
    }).catch(() => {});
};

const onSaveAndApprove = async () => {
    if (!canApprove.value) {
        ElMessage.warning('Cannot approve: Data is incomplete');
        return;
    }

    try {
        await ElMessageBox.confirm(
            'Approve this daily summary? Data will be locked.',
            'Confirm Approval',
            {
                confirmButtonText: 'Approve',
                cancelButtonText: 'Cancel',
                type: 'warning',
            }
        );

        await axios.post(`/production/sales-gas-metering/daily/${dailySummary.value.id}/approve`);
        ElMessage.success('Daily summary approved successfully');
        refetch();
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error('Failed to approve daily summary');
        }
    }
};

const sortChange = (sortObj) => {
    // Implement sorting if needed
};

// Lifecycle
onMounted(() => {
    refetch();
});
</script>

<style scoped>
.row-complete {
    background-color: #f0fdf4;
}

.row-missing {
    background-color: #fef2f2;
}

.row-complete:hover {
    background-color: #dcfce7 !important;
}

.row-missing:hover {
    background-color: #fee2e2 !important;
}
</style>