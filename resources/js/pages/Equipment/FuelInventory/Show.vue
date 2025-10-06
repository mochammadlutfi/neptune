<template>
    <div class="content">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
            <el-loading />
        </div>

        <!-- Content -->
        <div v-else-if="transaction" class="space-y-6">
            <!-- Modern Page Header -->
            <PageHeader :title="$t('equipment.fuel_inventory.transaction_details')"
                :back-button="{ label: $t('base.actions.back'), click: goBack }" />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Transaction Overview -->
                    <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <Icon icon="mingcute:file-line" class="mr-2" />
                                    {{ $t('equipment.fuel_inventory.sections.transaction_overview') }}
                                </h3>
                                <el-tag :type="getTransactionTypeTagType(transaction.transaction_type)" size="large">
                                    <Icon :icon="getTransactionTypeIcon(transaction.transaction_type)" class="mr-1" />
                                    {{ $t(`equipment.fuel_inventory.transaction_types.${transaction.transaction_type}`) }}
                                </el-tag>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.transaction_date') }}
                                    </label>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ formatDateTime(transaction.transaction_date) }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.reference_number') }}
                                    </label>
                                    <p class="text-lg font-semibold text-gray-900 font-mono">
                                        {{ transaction.reference_number || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.operator') }}
                                    </label>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ transaction.operator }}
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.shift') }}
                                    </label>
                                    <el-tag :type="getShiftTagType(transaction.shift)">
                                        {{ $t(`equipment.fuel_inventory.shifts.${transaction.shift}`) }}
                                    </el-tag>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.supplier') }}
                                    </label>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ transaction.supplier || '-' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('common.created_at') }}
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        {{ formatDateTime(transaction.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </el-card>

                    <!-- Tank & Volume Information -->
                    <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <Icon icon="mingcute:storage-line" class="mr-2" />
                                {{ $t('equipment.fuel_inventory.sections.tank_volume_information') }}
                            </h3>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.vessel_name') }}
                                    </label>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ transaction.vessel_name }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.tank_name') }}
                                    </label>
                                    <div class="flex flex-col">
                                        <p class="text-lg font-semibold text-gray-900">{{ transaction.tank_name }}</p>
                                        <p class="text-sm text-gray-500 font-mono">{{ transaction.tank_code }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.fuel_type') }}
                                    </label>
                                    <el-tag :type="getFuelTypeTagType(transaction.fuel_type)">
                                        {{ $t(`equipment.fuel_inventory.fuel_types.${transaction.fuel_type}`) }}
                                    </el-tag>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <label class="block text-sm font-medium text-blue-600 mb-1">
                                        {{ $t('equipment.fuel_inventory.fields.volume') }}
                                    </label>
                                    <p class="text-2xl font-bold" :class="getVolumeClass(transaction.transaction_type)">
                                        {{ getVolumeDisplay(transaction.volume, transaction.transaction_type) }} L
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            {{ $t('equipment.fuel_inventory.fields.level_before') }}
                                        </label>
                                        <p class="text-lg font-bold text-gray-900 font-mono">
                                            {{ formatNumber(transaction.level_before) }} L
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <label class="block text-sm font-medium text-gray-600 mb-1">
                                            {{ $t('equipment.fuel_inventory.fields.level_after') }}
                                        </label>
                                        <p class="text-lg font-bold text-gray-900 font-mono">
                                            {{ formatNumber(transaction.level_after) }} L
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </el-card>

                    <!-- Transfer Information (if applicable) -->
                    <el-card v-if="isTransferTransaction" body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <Icon icon="mingcute:transfer-line" class="mr-2" />
                                {{ $t('equipment.fuel_inventory.sections.transfer_information') }}
                            </h3>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div v-if="transaction.source_tank" class="bg-red-50 p-4 rounded-lg">
                                <label class="block text-sm font-medium text-red-600 mb-2">
                                    {{ $t('equipment.fuel_inventory.fields.source_tank') }}
                                </label>
                                <div class="flex flex-col">
                                    <p class="text-lg font-semibold text-red-800">{{ transaction.source_tank.tank_name }}</p>
                                    <p class="text-sm text-red-600 font-mono">{{ transaction.source_tank.tank_code }}</p>
                                </div>
                            </div>
                            <div v-if="transaction.destination_tank" class="bg-green-50 p-4 rounded-lg">
                                <label class="block text-sm font-medium text-green-600 mb-2">
                                    {{ $t('equipment.fuel_inventory.fields.destination_tank') }}
                                </label>
                                <div class="flex flex-col">
                                    <p class="text-lg font-semibold text-green-800">{{ transaction.destination_tank.tank_name }}</p>
                                    <p class="text-sm text-green-600 font-mono">{{ transaction.destination_tank.tank_code }}</p>
                                </div>
                            </div>
                        </div>
                    </el-card>

                    <!-- Cost Information -->
                    <el-card v-if="transaction.unit_price" body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <Icon icon="mingcute:calculator-line" class="mr-2" />
                                {{ $t('equipment.fuel_inventory.sections.cost_information') }}
                            </h3>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <label class="block text-sm font-medium text-blue-600 mb-1">
                                    {{ $t('equipment.fuel_inventory.fields.unit_price') }}
                                </label>
                                <p class="text-xl font-bold text-blue-800">
                                    Rp {{ formatNumber(transaction.unit_price) }}
                                </p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <label class="block text-sm font-medium text-green-600 mb-1">
                                    {{ $t('equipment.fuel_inventory.fields.volume') }}
                                </label>
                                <p class="text-xl font-bold text-green-800">
                                    {{ formatNumber(Math.abs(transaction.volume)) }} L
                                </p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg text-center">
                                <label class="block text-sm font-medium text-purple-600 mb-1">
                                    {{ $t('equipment.fuel_inventory.fields.total_cost') }}
                                </label>
                                <p class="text-xl font-bold text-purple-800">
                                    Rp {{ formatNumber(totalCost) }}
                                </p>
                            </div>
                        </div>
                    </el-card>

                    <!-- Notes -->
                    <el-card v-if="transaction.notes" body-class="!p-6" class="!rounded-lg !shadow-md">
                        <template #header>
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <Icon icon="mingcute:note-line" class="mr-2" />
                                {{ $t('equipment.fuel_inventory.fields.notes') }}
                            </h3>
                        </template>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ transaction.notes }}</p>
                    </el-card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <el-card body-class="!p-4" class="!rounded-lg !shadow-md">
                        <template #header>
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <Icon icon="mingcute:settings-line" class="mr-2" />
                                {{ $t('base.actions.title') }}
                            </h3>
                        </template>
                        <div class="space-y-3">
                            <el-button type="primary" class="w-full" @click="onEdit">
                                <Icon icon="mingcute:edit-line" class="mr-2" />
                                {{ $t('base.actions.edit') }}
                            </el-button>
                            <el-button type="success" class="w-full" @click="onPrint">
                                <Icon icon="mingcute:printer-line" class="mr-2" />
                                {{ $t('base.actions.print') }}
                            </el-button>
                            <el-button type="info" class="w-full" @click="onExport">
                                <Icon icon="mingcute:download-line" class="mr-2" />
                                {{ $t('base.actions.export') }}
                            </el-button>
                            <el-button type="danger" class="w-full" @click="onDelete">
                                <Icon icon="mingcute:delete-line" class="mr-2" />
                                {{ $t('base.actions.delete') }}
                            </el-button>
                        </div>
                    </el-card>

                    <!-- Tank Status -->
                    <el-card body-class="!p-4" class="!rounded-lg !shadow-md">
                        <template #header>
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <Icon icon="mingcute:storage-line" class="mr-2" />
                                {{ $t('equipment.fuel_inventory.sections.current_tank_status') }}
                            </h3>
                        </template>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $t('equipment.fuel_inventory.fields.current_level') }}</span>
                                <span class="font-bold text-lg">{{ formatNumber(tankStatus.current_level) }} L</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $t('equipment.fuel_inventory.fields.capacity') }}</span>
                                <span class="font-bold text-lg">{{ formatNumber(tankStatus.capacity) }} L</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $t('equipment.fuel_inventory.fields.utilization') }}</span>
                                <span class="font-bold text-lg" :class="getUtilizationClass(tankStatus.utilization)">
                                    {{ tankStatus.utilization }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="h-3 rounded-full transition-all duration-300" 
                                     :class="getUtilizationBarClass(tankStatus.utilization)"
                                     :style="{ width: `${tankStatus.utilization}%` }">
                                </div>
                            </div>
                        </div>
                    </el-card>

                    <!-- Related Transactions -->
                    <el-card body-class="!p-4" class="!rounded-lg !shadow-md">
                        <template #header>
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <Icon icon="mingcute:history-line" class="mr-2" />
                                {{ $t('equipment.fuel_inventory.sections.recent_transactions') }}
                            </h3>
                        </template>
                        <div class="space-y-3">
                            <div v-for="relatedTransaction in relatedTransactions" :key="relatedTransaction.id"
                                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer"
                                 @click="onViewRelated(relatedTransaction.id)">
                                <div class="flex items-center space-x-3">
                                    <Icon :icon="getTransactionTypeIcon(relatedTransaction.transaction_type)" 
                                          :class="getTransactionTypeIconClass(relatedTransaction.transaction_type)" />
                                    <div>
                                        <p class="text-sm font-medium">{{ formatDate(relatedTransaction.transaction_date) }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ getVolumeDisplay(relatedTransaction.volume, relatedTransaction.transaction_type) }} L
                                        </p>
                                    </div>
                                </div>
                                <Icon icon="mingcute:arrow-right-line" class="text-gray-400" />
                            </div>
                        </div>
                    </el-card>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="!transaction" class="flex flex-col items-center justify-center min-h-[400px] text-gray-500">
            <Icon icon="mingcute:file-search-line" class="text-6xl mb-4" />
            <p class="text-lg">{{ $t('common.errors.not_found') }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'

// Components
import PageHeader from '@/components/PageHeader.vue'

// Composables
const { t } = useI18n()
const router = useRouter()
const route = useRoute()

// State
const loading = ref(true)
const transaction = ref(null)

// Mock data for tank status and related transactions
const tankStatus = reactive({
    current_level: 12500,
    capacity: 20000,
    utilization: 62.5
})

const relatedTransactions = ref([
    {
        id: 2,
        transaction_date: '2024-01-15 14:30:00',
        transaction_type: 'add',
        volume: 5000
    },
    {
        id: 1,
        transaction_date: '2024-01-14 09:15:00',
        transaction_type: 'remove',
        volume: 2500
    }
])

// Computed properties
const isTransferTransaction = computed(() => {
    return transaction.value?.transaction_type === 'transfer_in' || 
           transaction.value?.transaction_type === 'transfer_out'
})

const totalCost = computed(() => {
    if (!transaction.value?.unit_price || !transaction.value?.volume) return 0
    return Math.abs(transaction.value.volume) * transaction.value.unit_price
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

const getTransactionTypeIconClass = (type) => {
    const classes = {
        add: 'text-green-600',
        remove: 'text-red-600',
        transfer_in: 'text-blue-600',
        transfer_out: 'text-orange-600'
    }
    return classes[type] || 'text-gray-600'
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

const getUtilizationClass = (utilization) => {
    if (utilization >= 90) return 'text-red-600'
    if (utilization >= 75) return 'text-orange-600'
    if (utilization >= 50) return 'text-green-600'
    return 'text-blue-600'
}

const getUtilizationBarClass = (utilization) => {
    if (utilization >= 90) return 'bg-red-500'
    if (utilization >= 75) return 'bg-orange-500'
    if (utilization >= 50) return 'bg-green-500'
    return 'bg-blue-500'
}

// Event handlers
const goBack = () => {
    router.push({ name: 'equipment.fuel-inventory.index' })
}

const onEdit = () => {
    router.push({ name: 'equipment.fuel-inventory.edit', params: { id: route.params.id } })
}

const onPrint = () => {
    window.print()
}

const onExport = async () => {
    try {
        const response = await axios.get(`/api/equipment/fuel-inventory/${route.params.id}/export`, {
            responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `fuel-transaction-${route.params.id}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
        
        ElMessage.success(t('common.success.export_completed'))
    } catch (error) {
        ElMessage.error(t('common.errors.export_failed'))
    }
}

const onDelete = async () => {
    try {
        await ElMessageBox.confirm(
            t('common.confirmation.delete_message'),
            t('common.confirmation.delete_title'),
            {
                confirmButtonText: t('base.actions.delete'),
                cancelButtonText: t('base.actions.cancel'),
                type: 'warning'
            }
        )
        
        await axios.delete(`/api/equipment/fuel-inventory/${route.params.id}`)
        
        ElMessage.success(t('common.success.deleted'))
        goBack()
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage.error(t('common.errors.operation_failed'))
        }
    }
}

const onViewRelated = (id) => {
    router.push({ name: 'equipment.fuel-inventory.show', params: { id } })
}

// Data fetching
const fetchData = async () => {
    try {
        loading.value = true
        
        // Mock API call - replace with actual API
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        // Mock transaction data
        transaction.value = {
            id: parseInt(route.params.id),
            transaction_date: '2024-01-16 10:30:00',
            transaction_type: 'add',
            vessel_name: 'FPSO Vessel Alpha',
            tank_name: 'Main Fuel Storage Tank',
            tank_code: 'FT-001',
            fuel_type: 'diesel',
            volume: 7500,
            unit_price: 15000,
            level_before: 8000,
            level_after: 15500,
            shift: 'day',
            operator: 'John Doe',
            supplier: 'PT Pertamina',
            reference_number: 'TXN-2024-001',
            notes: 'Regular fuel supply delivery. Quality checked and approved.',
            created_at: '2024-01-16 10:35:00',
            source_tank: null,
            destination_tank: null
        }
        
        // Update tank status based on transaction
        tankStatus.current_level = transaction.value.level_after
        tankStatus.utilization = (tankStatus.current_level / tankStatus.capacity) * 100
        
    } catch (error) {
        ElMessage.error(t('common.errors.fetch_failed'))
        transaction.value = null
    } finally {
        loading.value = false
    }
}

// Lifecycle
onMounted(() => {
    fetchData()
})
</script>

<style scoped>
@media print {
    .el-card__header,
    .el-button,
    .sidebar {
        display: none !important;
    }
}
</style>