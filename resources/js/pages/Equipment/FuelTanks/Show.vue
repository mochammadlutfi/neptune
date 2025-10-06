<template>
    <div class="content">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-64">
            <el-loading />
        </div>

        <!-- Content -->
        <div v-else-if="fuelTank">
            <!-- Modern Page Header -->
            <PageHeader :title="fuelTank.tank_name" :subtitle="fuelTank.tank_code" :show-back="true" @back="onBack"
                :primary-action="{
                    label: $t('base.actions.edit'),
                    icon: 'mingcute:edit-line',
                    click: onEdit
                }" />

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Main Information -->
                <div class="xl:col-span-2 space-y-6">
                    <!-- Tank Overview -->
                    <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center">
                                <Icon icon="mingcute:information-line" class="mr-2 text-lg" />
                                <span class="font-semibold">{{ $t('equipment.fuel_tanks.sections.overview') }}</span>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.vessel_name') }}
                                </label>
                                <p class="text-lg font-semibold">{{ fuelTank.vessel_name || '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.location') }}
                                </label>
                                <p class="text-lg">{{ fuelTank.location || '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.tank_type') }}
                                </label>
                                <el-tag :type="getTankTypeTagType(fuelTank.tank_type)" size="large">
                                    {{ $t(`equipment.fuel_tanks.tank_types.${fuelTank.tank_type}`) }}
                                </el-tag>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.fuel_type') }}
                                </label>
                                <el-tag :type="getFuelTypeTagType(fuelTank.fuel_type)" size="large">
                                    {{ $t(`equipment.fuel_tanks.fuel_types.${fuelTank.fuel_type}`) }}
                                </el-tag>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.status') }}
                                </label>
                                <el-tag :type="getStatusTagType(fuelTank.status)" size="large">
                                    {{ $t(`equipment.fuel_tanks.statuses.${fuelTank.status}`) }}
                                </el-tag>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.last_updated') }}
                                </label>
                                <p class="text-lg">{{ formatDateTime(fuelTank.last_updated) }}</p>
                            </div>
                        </div>
                    </el-card>

                    <!-- Current Status -->
                    <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center">
                                <Icon icon="mingcute:dashboard-line" class="mr-2 text-lg" />
                                <span class="font-semibold">{{ $t('equipment.fuel_tanks.sections.current_status') }}</span>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Level -->
                            <div class="text-center">
                                <div class="mb-4">
                                    <div class="text-3xl font-bold text-blue-600 mb-2">
                                        {{ formatNumber(fuelTank.current_level) }} L
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $t('equipment.fuel_tanks.fields.current_level') }}
                                    </div>
                                </div>
                                <el-progress 
                                    :percentage="fuelTank.fill_percentage" 
                                    :color="getFillPercentageColor(fuelTank.fill_percentage)"
                                    :stroke-width="12"
                                    :show-text="false"
                                />
                                <div class="text-lg font-semibold mt-2">{{ fuelTank.fill_percentage }}%</div>
                            </div>

                            <!-- Capacity -->
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600 mb-2">
                                    {{ formatNumber(fuelTank.capacity) }} L
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $t('equipment.fuel_tanks.fields.capacity') }}
                                </div>
                            </div>

                            <!-- Available Space -->
                            <div class="text-center">
                                <div class="text-3xl font-bold text-orange-600 mb-2">
                                    {{ formatNumber(fuelTank.capacity - fuelTank.current_level) }} L
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $t('equipment.fuel_tanks.fields.available_space') }}
                                </div>
                            </div>
                        </div>
                    </el-card>

                    <!-- Level History Chart -->
                    <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <Icon icon="mingcute:chart-line" class="mr-2 text-lg" />
                                    <span class="font-semibold">{{ $t('equipment.fuel_tanks.sections.level_history') }}</span>
                                </div>
                                <el-select v-model="chartPeriod" @change="updateChart" class="w-32">
                                    <el-option label="24h" value="24h" />
                                    <el-option label="7d" value="7d" />
                                    <el-option label="30d" value="30d" />
                                </el-select>
                            </div>
                        </template>

                        <div ref="chartContainer" class="h-80"></div>
                    </el-card>

                    <!-- Specifications -->
                    <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center">
                                <Icon icon="mingcute:settings-3-line" class="mr-2 text-lg" />
                                <span class="font-semibold">{{ $t('equipment.fuel_tanks.sections.specifications') }}</span>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.dimensions') }}
                                </label>
                                <p class="text-lg">
                                    {{ fuelTank.length }}m × {{ fuelTank.width }}m × {{ fuelTank.height }}m
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.material') }}
                                </label>
                                <p class="text-lg">{{ $t(`equipment.fuel_tanks.materials.${fuelTank.material}`) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.sensor_type') }}
                                </label>
                                <p class="text-lg">{{ $t(`equipment.fuel_tanks.sensor_types.${fuelTank.sensor_type}`) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.min_level') }}
                                </label>
                                <p class="text-lg">{{ formatNumber(fuelTank.min_level) }} L</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.max_level') }}
                                </label>
                                <p class="text-lg">{{ formatNumber(fuelTank.max_level) }} L</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.manufacturer') }}
                                </label>
                                <p class="text-lg">{{ fuelTank.manufacturer || '-' }}</p>
                            </div>
                        </div>
                    </el-card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <el-card body-class="!p-4" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center">
                                <Icon icon="mingcute:flash-line" class="mr-2 text-lg" />
                                <span class="font-semibold">{{ $t('equipment.fuel_tanks.sections.quick_actions') }}</span>
                            </div>
                        </template>

                        <div class="space-y-3">
                            <el-button type="primary" class="w-full" @click="refreshData">
                                <Icon icon="mingcute:refresh-line" class="mr-2" />
                                {{ $t('base.actions.refresh') }}
                            </el-button>
                            <el-button type="success" class="w-full" @click="onAddFuel">
                                <Icon icon="mingcute:add-line" class="mr-2" />
                                {{ $t('equipment.fuel_tanks.actions.add_fuel') }}
                            </el-button>
                            <el-button type="warning" class="w-full" @click="onRemoveFuel">
                                <Icon icon="mingcute:subtract-line" class="mr-2" />
                                {{ $t('equipment.fuel_tanks.actions.remove_fuel') }}
                            </el-button>
                        </div>
                    </el-card>

                    <!-- Alarm Levels -->
                    <el-card body-class="!p-4" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center">
                                <Icon icon="mingcute:alarm-line" class="mr-2 text-lg" />
                                <span class="font-semibold">{{ $t('equipment.fuel_tanks.sections.alarm_levels') }}</span>
                            </div>
                        </template>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div class="flex items-center">
                                    <Icon icon="mingcute:alert-triangle-line" class="text-red-500 mr-2" />
                                    <span class="text-sm font-medium">{{ $t('equipment.fuel_tanks.fields.alarm_low_level') }}</span>
                                </div>
                                <span class="font-mono font-bold text-red-600">{{ formatNumber(fuelTank.alarm_low_level) }} L</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <div class="flex items-center">
                                    <Icon icon="mingcute:alert-triangle-line" class="text-orange-500 mr-2" />
                                    <span class="text-sm font-medium">{{ $t('equipment.fuel_tanks.fields.alarm_high_level') }}</span>
                                </div>
                                <span class="font-mono font-bold text-orange-600">{{ formatNumber(fuelTank.alarm_high_level) }} L</span>
                            </div>
                        </div>
                    </el-card>

                    <!-- Maintenance Info -->
                    <el-card body-class="!p-4" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center">
                                <Icon icon="mingcute:tool-line" class="mr-2 text-lg" />
                                <span class="font-semibold">{{ $t('equipment.fuel_tanks.sections.maintenance') }}</span>
                            </div>
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.installation_date') }}
                                </label>
                                <p class="text-sm font-semibold">{{ formatDate(fuelTank.installation_date) }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.last_inspection') }}
                                </label>
                                <p class="text-sm font-semibold">{{ formatDate(fuelTank.last_inspection) }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    {{ $t('equipment.fuel_tanks.fields.next_inspection') }}
                                </label>
                                <p class="text-sm font-semibold" :class="getInspectionStatusClass(fuelTank.next_inspection)">
                                    {{ formatDate(fuelTank.next_inspection) }}
                                </p>
                            </div>
                        </div>
                    </el-card>

                    <!-- Additional Info -->
                    <el-card v-if="fuelTank.notes" body-class="!p-4" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center">
                                <Icon icon="mingcute:file-text-line" class="mr-2 text-lg" />
                                <span class="font-semibold">{{ $t('equipment.fuel_tanks.sections.notes') }}</span>
                            </div>
                        </template>

                        <p class="text-sm text-gray-700 leading-relaxed">{{ fuelTank.notes }}</p>
                    </el-card>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="!fuelTank" class="text-center py-12">
            <Icon icon="mingcute:file-search-line" class="text-6xl text-gray-400 mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $t('common.errors.not_found') }}</h3>
            <p class="text-gray-500 mb-6">{{ $t('equipment.fuel_tanks.errors.not_found') }}</p>
            <el-button type="primary" @click="onBack">
                {{ $t('base.actions.back') }}
            </el-button>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'
import * as echarts from 'echarts'

// Components
import PageHeader from '@/components/PageHeader.vue'

// Composables
const { t } = useI18n()
const router = useRouter()
const route = useRoute()

// State
const loading = ref(false)
const chartContainer = ref()
const chartInstance = ref()
const chartPeriod = ref('24h')

// Data
const fuelTank = reactive({
    id: null,
    tank_code: '',
    tank_name: '',
    vessel_name: '',
    location: '',
    tank_type: '',
    fuel_type: '',
    capacity: 0,
    current_level: 0,
    fill_percentage: 0,
    min_level: 0,
    max_level: 0,
    alarm_low_level: 0,
    alarm_high_level: 0,
    length: 0,
    width: 0,
    height: 0,
    material: '',
    sensor_type: '',
    status: '',
    installation_date: null,
    last_inspection: null,
    next_inspection: null,
    manufacturer: '',
    model: '',
    serial_number: '',
    notes: '',
    last_updated: null
})

// Helper methods
const formatNumber = (value) => {
    if (!value) return '0'
    return new Intl.NumberFormat('id-ID').format(value)
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

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const getTankTypeTagType = (type) => {
    const types = {
        storage: 'primary',
        service: 'success',
        day: 'warning'
    }
    return types[type] || 'info'
}

const getFuelTypeTagType = (type) => {
    const types = {
        diesel: 'primary',
        gasoline: 'warning',
        heavy_fuel_oil: 'danger',
        marine_gas_oil: 'success'
    }
    return types[type] || 'info'
}

const getStatusTagType = (status) => {
    const statuses = {
        active: 'success',
        inactive: 'info',
        maintenance: 'warning',
        out_of_service: 'danger'
    }
    return statuses[status] || 'info'
}

const getFillPercentageColor = (percentage) => {
    if (percentage >= 80) return '#67c23a'
    if (percentage >= 50) return '#e6a23c'
    if (percentage >= 20) return '#f56c6c'
    return '#909399'
}

const getInspectionStatusClass = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    const now = new Date()
    const diffDays = Math.ceil((date - now) / (1000 * 60 * 60 * 24))
    
    if (diffDays < 0) return 'text-red-600' // Overdue
    if (diffDays <= 30) return 'text-orange-600' // Due soon
    return 'text-green-600' // OK
}

// Data fetching
const fetchData = async () => {
    try {
        loading.value = true
        
        // Mock data - replace with actual API call
        const mockData = {
            id: route.params.id,
            tank_code: 'FT-001',
            tank_name: 'Main Fuel Storage Tank',
            vessel_name: 'FPSO Vessel Alpha',
            location: 'Engine Room - Port Side',
            tank_type: 'storage',
            fuel_type: 'diesel',
            capacity: 50000,
            current_level: 35000,
            fill_percentage: 70,
            min_level: 5000,
            max_level: 48000,
            alarm_low_level: 8000,
            alarm_high_level: 45000,
            length: 12.5,
            width: 8.0,
            height: 6.0,
            material: 'steel',
            sensor_type: 'ultrasonic',
            status: 'active',
            installation_date: '2020-03-15',
            last_inspection: '2024-01-15',
            next_inspection: '2024-07-15',
            manufacturer: 'Marine Tank Systems',
            model: 'MTS-5000',
            serial_number: 'MTS-5000-2020-001',
            notes: 'Primary fuel storage tank for main engines. Regular monitoring required.',
            last_updated: '2024-01-20T10:30:00Z'
        }
        
        Object.assign(fuelTank, mockData)
        
        await nextTick()
        initChart()
    } catch (error) {
        ElMessage.error(t('common.errors.fetch_failed'))
    } finally {
        loading.value = false
    }
}

// Chart methods
const initChart = () => {
    if (!chartContainer.value) return
    
    chartInstance.value = echarts.init(chartContainer.value)
    updateChart()
    
    window.addEventListener('resize', handleResize)
}

const updateChart = () => {
    if (!chartInstance.value) return
    
    // Mock chart data - replace with actual API call
    const mockData = generateMockChartData()
    
    const option = {
        tooltip: {
            trigger: 'axis',
            formatter: (params) => {
                const point = params[0]
                return `${point.name}<br/>${point.seriesName}: ${formatNumber(point.value)} L`
            }
        },
        xAxis: {
            type: 'category',
            data: mockData.map(item => item.time)
        },
        yAxis: {
            type: 'value',
            name: 'Level (L)',
            axisLabel: {
                formatter: (value) => formatNumber(value)
            }
        },
        series: [{
            name: t('equipment.fuel_tanks.fields.fuel_level'),
            type: 'line',
            data: mockData.map(item => item.level),
            smooth: true,
            lineStyle: {
                color: '#409EFF'
            },
            areaStyle: {
                color: {
                    type: 'linear',
                    x: 0,
                    y: 0,
                    x2: 0,
                    y2: 1,
                    colorStops: [{
                        offset: 0,
                        color: 'rgba(64, 158, 255, 0.3)'
                    }, {
                        offset: 1,
                        color: 'rgba(64, 158, 255, 0.1)'
                    }]
                }
            }
        }],
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        }
    }
    
    chartInstance.value.setOption(option)
}

const generateMockChartData = () => {
    const data = []
    const now = new Date()
    const periods = {
        '24h': { hours: 24, interval: 1 },
        '7d': { hours: 168, interval: 6 },
        '30d': { hours: 720, interval: 24 }
    }
    
    const period = periods[chartPeriod.value]
    const baseLevel = fuelTank.current_level
    
    for (let i = period.hours; i >= 0; i -= period.interval) {
        const time = new Date(now.getTime() - i * 60 * 60 * 1000)
        const variation = (Math.random() - 0.5) * 2000 // ±1000L variation
        const level = Math.max(0, Math.min(fuelTank.capacity, baseLevel + variation))
        
        data.push({
            time: time.toLocaleString('id-ID', {
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }),
            level: Math.round(level)
        })
    }
    
    return data
}

const handleResize = () => {
    if (chartInstance.value) {
        chartInstance.value.resize()
    }
}

// Actions
const refreshData = async () => {
    await fetchData()
    ElMessage.success(t('common.success.data_refreshed'))
}

const onEdit = () => {
    router.push({ name: 'equipment.fuel-tanks.edit', params: { id: route.params.id } })
}

const onBack = () => {
    router.push({ name: 'equipment.fuel-tanks.index' })
}

const onAddFuel = () => {
    // Navigate to fuel inventory add form
    router.push({ 
        name: 'equipment.fuel-inventory.create',
        query: { tank_id: route.params.id, type: 'add' }
    })
}

const onRemoveFuel = () => {
    // Navigate to fuel inventory remove form
    router.push({ 
        name: 'equipment.fuel-inventory.create',
        query: { tank_id: route.params.id, type: 'remove' }
    })
}

// Lifecycle
onMounted(() => {
    fetchData()
})

onUnmounted(() => {
    if (chartInstance.value) {
        chartInstance.value.dispose()
    }
    window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
.chart-container {
    width: 100%;
    height: 320px;
}

@media (max-width: 768px) {
    .chart-container {
        height: 250px;
    }
}
</style>