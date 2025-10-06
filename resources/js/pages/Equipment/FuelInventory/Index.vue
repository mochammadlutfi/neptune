<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('equipment.fuel_inventory.title')" :primary-action="{
            label: $t('equipment.fuel_inventory.create'),
            icon: 'mingcute:add-line',
            click: onCreate
        }" />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <TableControls v-model:search="params.q" :search-placeholder="$t('common.search.search_placeholder')"
                :loading="isLoading" :selected-rows="selectedRows" @refresh="refetch" @bulk-export="bulkExport"
                @bulk-delete="bulkDelete">
                <template #filters>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_inventory.fields.vessel_id') }}</label>
                            <el-select v-model="advancedFilters.vessel_id" :placeholder="$t('equipment.fuel_inventory.placeholder.vessel_id')"
                                clearable filterable class="w-full">
                                <el-option v-for="vessel in vessels" :key="vessel.id" :label="vessel.name" :value="vessel.id" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_inventory.fields.tank_id') }}</label>
                            <el-select v-model="advancedFilters.tank_id" :placeholder="$t('equipment.fuel_inventory.placeholder.tank_id')"
                                clearable filterable class="w-full">
                                <el-option v-for="tank in fuelTanks" :key="tank.id" :label="tank.tank_name" :value="tank.id" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_inventory.fields.transaction_type') }}</label>
                            <el-select v-model="advancedFilters.transaction_type" :placeholder="$t('equipment.fuel_inventory.placeholder.transaction_type')"
                                clearable class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.fuel_inventory.transaction_types')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_inventory.fields.fuel_type') }}</label>
                            <el-select v-model="advancedFilters.fuel_type" :placeholder="$t('equipment.fuel_inventory.placeholder.fuel_type')"
                                clearable class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.fuel_inventory.fuel_types')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('common.date_range') }}</label>
                            <el-date-picker v-model="advancedFilters.date_range" type="daterange"
                                :range-separator="$t('common.to')" :start-placeholder="$t('common.start_date')"
                                :end-placeholder="$t('common.end_date')" format="YYYY-MM-DD" value-format="YYYY-MM-DD"
                                class="w-full" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_inventory.fields.shift') }}</label>
                            <el-select v-model="advancedFilters.shift" :placeholder="$t('equipment.fuel_inventory.placeholder.shift')"
                                clearable class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.fuel_inventory.shifts')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_inventory.fields.operator') }}</label>
                            <el-input v-model="advancedFilters.operator" :placeholder="$t('equipment.fuel_inventory.placeholder.operator')"
                                clearable class="w-full" />
                        </div>
                    </div>
                </template>
            </TableControls>

            <!-- Summary Cards -->
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <Icon icon="mingcute:add-circle-line" class="text-2xl text-blue-600 mr-3" />
                            <div>
                                <p class="text-sm text-blue-600 font-medium">{{ $t('equipment.fuel_inventory.summary.total_added') }}</p>
                                <p class="text-xl font-bold text-blue-800">{{ formatNumber(summary.total_added) }} L</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <Icon icon="mingcute:subtract-circle-line" class="text-2xl text-red-600 mr-3" />
                            <div>
                                <p class="text-sm text-red-600 font-medium">{{ $t('equipment.fuel_inventory.summary.total_removed') }}</p>
                                <p class="text-xl font-bold text-red-800">{{ formatNumber(summary.total_removed) }} L</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <Icon icon="mingcute:trending-up-line" class="text-2xl text-green-600 mr-3" />
                            <div>
                                <p class="text-sm text-green-600 font-medium">{{ $t('equipment.fuel_inventory.summary.net_change') }}</p>
                                <p class="text-xl font-bold text-green-800">{{ formatNumber(summary.net_change) }} L</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <Icon icon="mingcute:file-line" class="text-2xl text-purple-600 mr-3" />
                            <div>
                                <p class="text-sm text-purple-600 font-medium">{{ $t('equipment.fuel_inventory.summary.total_transactions') }}</p>
                                <p class="text-xl font-bold text-purple-800">{{ formatNumber(summary.total_transactions) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <SkeletonTable />
                </template>
                <template #default>
                    <div class="overflow-x-auto">
                        <el-table class="base-table" :data="data?.data || []" @sort-change="sortChange"
                            @selection-change="handleSelectionChange" row-key="id" stripe>
                            <el-table-column type="selection" width="50" align="center" />

                            <el-table-column prop="transaction_date" :label="$t('equipment.fuel_inventory.fields.transaction_date')" sortable
                                width="180" fixed="left">
                                <template #default="scope">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ formatDate(scope.row.transaction_date) }}</span>
                                        <span class="text-xs text-gray-500">{{ formatTime(scope.row.transaction_date) }}</span>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="transaction_type" :label="$t('equipment.fuel_inventory.fields.transaction_type')" sortable
                                width="120" align="center">
                                <template #default="scope">
                                    <el-tag :type="getTransactionTypeTagType(scope.row.transaction_type)">
                                        <Icon :icon="getTransactionTypeIcon(scope.row.transaction_type)" class="mr-1" />
                                        {{ $t(`equipment.fuel_inventory.transaction_types.${scope.row.transaction_type}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="vessel_name" :label="$t('equipment.fuel_inventory.fields.vessel_name')" sortable
                                min-width="150" show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="font-medium">{{ scope.row.vessel_name || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="tank_name" :label="$t('equipment.fuel_inventory.fields.tank_name')" sortable
                                min-width="180" show-overflow-tooltip>
                                <template #default="scope">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ scope.row.tank_name }}</span>
                                        <span class="text-xs text-gray-500 font-mono">{{ scope.row.tank_code }}</span>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="fuel_type" :label="$t('equipment.fuel_inventory.fields.fuel_type')" sortable
                                width="120" align="center">
                                <template #default="scope">
                                    <el-tag :type="getFuelTypeTagType(scope.row.fuel_type)">
                                        {{ $t(`equipment.fuel_inventory.fuel_types.${scope.row.fuel_type}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="volume" :label="$t('equipment.fuel_inventory.fields.volume')" sortable
                                width="120" align="right">
                                <template #default="scope">
                                    <span class="font-mono font-bold" :class="getVolumeClass(scope.row.transaction_type)">
                                        {{ getVolumeDisplay(scope.row.volume, scope.row.transaction_type) }} L
                                    </span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="level_before" :label="$t('equipment.fuel_inventory.fields.level_before')" sortable
                                width="120" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.level_before) }} L</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="level_after" :label="$t('equipment.fuel_inventory.fields.level_after')" sortable
                                width="120" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.level_after) }} L</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="shift" :label="$t('equipment.fuel_inventory.fields.shift')" sortable
                                width="80" align="center">
                                <template #default="scope">
                                    <el-tag size="small" :type="getShiftTagType(scope.row.shift)">
                                        {{ $t(`equipment.fuel_inventory.shifts.${scope.row.shift}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="operator" :label="$t('equipment.fuel_inventory.fields.operator')" sortable
                                width="150" show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="font-medium">{{ scope.row.operator || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column :label="$t('base.actions.title')" width="120" align="center" fixed="right">
                                <template #default="scope">
                                    <div class="flex items-center justify-center space-x-2">
                                        <el-tooltip :content="$t('base.actions.view')" placement="top">
                                            <el-button type="primary" size="small" circle @click="onView(scope.row.id)">
                                                <Icon icon="mingcute:eye-line" />
                                            </el-button>
                                        </el-tooltip>
                                        <el-tooltip :content="$t('base.actions.edit')" placement="top">
                                            <el-button type="warning" size="small" circle @click="onEdit(scope.row.id)">
                                                <Icon icon="mingcute:edit-line" />
                                            </el-button>
                                        </el-tooltip>
                                    </div>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>
                </template>
            </el-skeleton>

            <!-- Pagination -->
            <el-pagination v-if="data?.data?.length" v-model:current-page="params.page" v-model:page-size="params.per_page"
                :total="data?.total || 0" :page-sizes="[10, 25, 50, 100]"
                layout="total, sizes, prev, pager, next, jumper" @size-change="onSizeChange"
                @current-change="onCurrentChange" class="justify-center" />
        </el-card>
    </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useQuery } from '@tanstack/vue-query'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

// Components
import PageHeader from '@/components/PageHeader.vue'
import TableControls from '@/components/TableControls.vue'
import SkeletonTable from '@/components/SkeletonTable.vue'

// Composables
const { t } = useI18n()
const router = useRouter()

// State
const selectedRows = ref([])

// Filter state
const params = reactive({
    page: 1,
    per_page: 25,
    q: '',
    sort_by: 'transaction_date',
    sort_order: 'desc'
})

const advancedFilters = reactive({
    vessel_id: null,
    tank_id: null,
    transaction_type: null,
    fuel_type: null,
    shift: null,
    operator: '',
    date_range: null
})

// Summary data
const summary = reactive({
    total_added: 0,
    total_removed: 0,
    net_change: 0,
    total_transactions: 0
})

// Computed
const activeFilters = computed(() => {
    const filters = { ...params }
    
    // Add advanced filters
    Object.keys(advancedFilters).forEach(key => {
        if (advancedFilters[key] !== null && advancedFilters[key] !== '') {
            if (key === 'date_range' && Array.isArray(advancedFilters[key])) {
                filters.date_from = advancedFilters[key][0]
                filters.date_to = advancedFilters[key][1]
            } else {
                filters[key] = advancedFilters[key]
            }
        }
    })
    
    return filters
})

// Helper methods
const formatNumber = (value) => {
    if (!value) return '0'
    return new Intl.NumberFormat('id-ID').format(value)
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

const formatTime = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getTransactionTypeTagType = (type) => {
    const types = {
        add: 'success',
        remove: 'danger',
        transfer_in: 'primary',
        transfer_out: 'warning'
    }
    return types[type] || 'info'
}

const getTransactionTypeIcon = (type) => {
    const icons = {
        add: 'mingcute:add-circle-line',
        remove: 'mingcute:subtract-circle-line',
        transfer_in: 'mingcute:arrow-down-circle-line',
        transfer_out: 'mingcute:arrow-up-circle-line'
    }
    return icons[type] || 'mingcute:file-line'
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

const getShiftTagType = (shift) => {
    const shifts = {
        day: 'warning',
        night: 'primary'
    }
    return shifts[shift] || 'info'
}

const getVolumeClass = (transactionType) => {
    return transactionType === 'add' || transactionType === 'transfer_in' 
        ? 'text-green-600' 
        : 'text-red-600'
}

const getVolumeDisplay = (volume, transactionType) => {
    const prefix = (transactionType === 'add' || transactionType === 'transfer_in') ? '+' : '-'
    return `${prefix}${formatNumber(Math.abs(volume))}`
}

// Data fetching
const { data, isLoading, refetch } = useQuery({
    queryKey: ['fuelInventory', activeFilters],
    queryFn: async () => {
        const response = await axios.get('/api/equipment/fuel-inventory', {
            params: activeFilters.value
        })
        
        // Update summary
        Object.assign(summary, response.data.result.summary || {
            total_added: 125000,
            total_removed: 89000,
            net_change: 36000,
            total_transactions: 156
        })
        
        return response.data.result
    }
})

// Mock data for dropdowns
const vessels = ref([
    { id: 1, name: 'FPSO Vessel Alpha' },
    { id: 2, name: 'FPSO Vessel Beta' }
])

const fuelTanks = ref([
    { id: 1, tank_name: 'Main Fuel Storage Tank', tank_code: 'FT-001' },
    { id: 2, tank_name: 'Service Tank A', tank_code: 'FT-002' },
    { id: 3, tank_name: 'Day Tank 1', tank_code: 'FT-003' }
])

// Event handlers
const onCreate = () => {
    router.push({ name: 'equipment.fuel-inventory.create' })
}

const onView = (id) => {
    router.push({ name: 'equipment.fuel-inventory.show', params: { id } })
}

const onEdit = (id) => {
    router.push({ name: 'equipment.fuel-inventory.edit', params: { id } })
}

const sortChange = ({ prop, order }) => {
    params.sort_by = prop
    params.sort_order = order === 'ascending' ? 'asc' : 'desc'
    params.page = 1
}

const onSizeChange = (size) => {
    params.per_page = size
    params.page = 1
}

const onCurrentChange = (page) => {
    params.page = page
}

const handleSelectionChange = (selection) => {
    selectedRows.value = selection
}

// Bulk operations
const bulkExport = async () => {
    try {
        const response = await axios.post('/api/equipment/fuel-inventory/export', {
            ids: selectedRows.value.map(row => row.id)
        }, {
            responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'fuel-inventory-export.xlsx')
        document.body.appendChild(link)
        link.click()
        link.remove()
        
        ElMessage.success(t('common.success.export_completed'))
    } catch (error) {
        ElMessage.error(t('common.errors.export_failed'))
    }
}

const bulkDelete = async () => {
    if (selectedRows.value.length === 0) {
        ElMessage.warning(t('common.validation.no_items_selected'))
        return
    }
    
    try {
        await ElMessageBox.confirm(
            t('common.confirmation.bulk_delete_message', { count: selectedRows.value.length }),
            t('common.confirmation.bulk_delete_title'),
            {
                confirmButtonText: t('base.actions.delete'),
                cancelButtonText: t('base.actions.cancel'),
                type: 'warning'
            }
        )
        
        await axios.delete('/api/equipment/fuel-inventory/bulk', {
            data: { ids: selectedRows.value.map(row => row.id) }
        })
        
        ElMessage.success(t('common.success.items_deleted'))
        selectedRows.value = []
        refetch()
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error(t('common.errors.operation_failed'))
        }
    }
}

// Watchers
watch(() => params.q, () => {
    params.page = 1
}, { debounce: 300 })

watch(advancedFilters, () => {
    params.page = 1
}, { deep: true })
</script>