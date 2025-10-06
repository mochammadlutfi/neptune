<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('equipment.fuel_tanks.title')" :primary-action="{
            label: $t('equipment.fuel_tanks.create'),
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_tanks.fields.vessel_id') }}</label>
                            <el-select v-model="advancedFilters.vessel_id" :placeholder="$t('equipment.fuel_tanks.placeholder.vessel_id')"
                                clearable filterable class="w-full">
                                <el-option v-for="vessel in vessels" :key="vessel.id" :label="vessel.name" :value="vessel.id" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_tanks.fields.tank_type') }}</label>
                            <el-select v-model="advancedFilters.tank_type" :placeholder="$t('equipment.fuel_tanks.placeholder.tank_type')"
                                clearable class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.fuel_tanks.tank_types')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_tanks.fields.fuel_type') }}</label>
                            <el-select v-model="advancedFilters.fuel_type" :placeholder="$t('equipment.fuel_tanks.placeholder.fuel_type')"
                                clearable class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.fuel_tanks.fuel_types')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.fuel_tanks.fields.status') }}</label>
                            <el-select v-model="advancedFilters.status" :placeholder="$t('equipment.fuel_tanks.placeholder.status')"
                                clearable class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.fuel_tanks.statuses')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('common.date_range') }}</label>
                            <el-date-picker v-model="advancedFilters.date_range" type="daterange"
                                :range-separator="$t('common.to')" :start-placeholder="$t('common.start_date')"
                                :end-placeholder="$t('common.end_date')" format="YYYY-MM-DD" value-format="YYYY-MM-DD"
                                class="w-full" />
                        </div>
                    </div>
                </template>
            </TableControls>

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

                            <el-table-column prop="tank_code" :label="$t('equipment.fuel_tanks.fields.tank_code')" sortable
                                width="120" fixed="left">
                                <template #default="scope">
                                    <span class="font-mono font-medium">{{ scope.row.tank_code }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="tank_name" :label="$t('equipment.fuel_tanks.fields.tank_name')" sortable
                                min-width="200" show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="font-medium">{{ scope.row.tank_name }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="vessel_name" :label="$t('equipment.fuel_tanks.fields.vessel_name')" sortable
                                min-width="150" show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="font-medium">{{ scope.row.vessel_name || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="tank_type" :label="$t('equipment.fuel_tanks.fields.tank_type')" sortable
                                width="120" align="center">
                                <template #default="scope">
                                    <el-tag :type="getTankTypeTagType(scope.row.tank_type)">
                                        {{ $t(`equipment.fuel_tanks.tank_types.${scope.row.tank_type}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="fuel_type" :label="$t('equipment.fuel_tanks.fields.fuel_type')" sortable
                                width="120" align="center">
                                <template #default="scope">
                                    <el-tag :type="getFuelTypeTagType(scope.row.fuel_type)">
                                        {{ $t(`equipment.fuel_tanks.fuel_types.${scope.row.fuel_type}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="capacity" :label="$t('equipment.fuel_tanks.fields.capacity')" sortable
                                width="120" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.capacity) }} L</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="current_level" :label="$t('equipment.fuel_tanks.fields.current_level')" sortable
                                width="120" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.current_level) }} L</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="fill_percentage" :label="$t('equipment.fuel_tanks.fields.fill_percentage')" sortable
                                width="120" align="center">
                                <template #default="scope">
                                    <div class="flex items-center justify-center">
                                        <el-progress 
                                            :percentage="scope.row.fill_percentage" 
                                            :color="getFillPercentageColor(scope.row.fill_percentage)"
                                            :stroke-width="8"
                                            class="w-16"
                                        />
                                        <span class="ml-2 text-sm font-mono">{{ scope.row.fill_percentage }}%</span>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="status" :label="$t('equipment.fuel_tanks.fields.status')" sortable
                                width="100" align="center">
                                <template #default="scope">
                                    <el-tag :type="getStatusTagType(scope.row.status)">
                                        {{ $t(`equipment.fuel_tanks.statuses.${scope.row.status}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="last_updated" :label="$t('equipment.fuel_tanks.fields.last_updated')" sortable
                                width="180">
                                <template #default="scope">
                                    {{ formatDateTime(scope.row.last_updated) }}
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
    sort_by: 'tank_code',
    sort_order: 'asc'
})

const advancedFilters = reactive({
    vessel_id: null,
    tank_type: null,
    fuel_type: null,
    status: null,
    date_range: null
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

// Data fetching
const { data, isLoading, refetch } = useQuery({
    queryKey: ['fuelTanks', activeFilters],
    queryFn: async () => {
        const response = await axios.get('/api/equipment/fuel-tanks', {
            params: activeFilters.value
        })
        return response.data.result
    }
})

// Mock data for vessels
const vessels = ref([
    { id: 1, name: 'FPSO Vessel Alpha' },
    { id: 2, name: 'FPSO Vessel Beta' }
])

// Event handlers
const onCreate = () => {
    router.push({ name: 'equipment.fuel-tanks.create' })
}

const onView = (id) => {
    router.push({ name: 'equipment.fuel-tanks.show', params: { id } })
}

const onEdit = (id) => {
    router.push({ name: 'equipment.fuel-tanks.edit', params: { id } })
}

const onSearch = () => {
    params.page = 1
    refetch()
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
        const response = await axios.post('/api/equipment/fuel-tanks/export', {
            ids: selectedRows.value.map(row => row.id)
        }, {
            responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', 'fuel-tanks-export.xlsx')
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
        
        await axios.delete('/api/equipment/fuel-tanks/bulk', {
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

// Advanced filters
const applyAdvancedFilters = () => {
    params.page = 1
    refetch()
}

const clearAllFilters = () => {
    Object.keys(advancedFilters).forEach(key => {
        advancedFilters[key] = null
    })
    params.q = ''
    params.page = 1
    refetch()
}

const removeFilter = (filterKey) => {
    if (filterKey === 'q') {
        params.q = ''
    } else {
        advancedFilters[filterKey] = null
    }
    params.page = 1
    refetch()
}

// Watchers
watch(() => params.q, () => {
    params.page = 1
}, { debounce: 300 })

watch(advancedFilters, () => {
    params.page = 1
}, { deep: true })
</script>