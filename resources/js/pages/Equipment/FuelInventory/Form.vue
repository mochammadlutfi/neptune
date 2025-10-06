<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="isEdit ? $t('equipment.fuel_inventory.edit') : $t('equipment.fuel_inventory.create')"
            :back-button="{ label: $t('base.actions.back'), click: goBack }" />

        <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
            <el-form ref="formRef" :model="form" :rules="rules" label-position="top" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <Icon icon="mingcute:information-line" class="mr-2" />
                        {{ $t('equipment.fuel_inventory.sections.basic_information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.transaction_date')" prop="transaction_date" required>
                            <el-date-picker v-model="form.transaction_date" type="datetime" :placeholder="$t('equipment.fuel_inventory.placeholder.transaction_date')"
                                format="DD/MM/YYYY HH:mm" value-format="YYYY-MM-DD HH:mm:ss" class="w-full" />
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.transaction_type')" prop="transaction_type" required>
                            <el-select v-model="form.transaction_type" :placeholder="$t('equipment.fuel_inventory.placeholder.transaction_type')"
                                class="w-full" @change="onTransactionTypeChange">
                                <el-option v-for="(label, value) in $t('equipment.fuel_inventory.transaction_types')" :key="value" :label="label" :value="value">
                                    <div class="flex items-center">
                                        <Icon :icon="getTransactionTypeIcon(value)" class="mr-2" />
                                        {{ label }}
                                    </div>
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.shift')" prop="shift" required>
                            <el-select v-model="form.shift" :placeholder="$t('equipment.fuel_inventory.placeholder.shift')" class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.fuel_inventory.shifts')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </el-form-item>
                    </div>
                </div>

                <!-- Tank & Vessel Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <Icon icon="mingcute:storage-line" class="mr-2" />
                        {{ $t('equipment.fuel_inventory.sections.tank_vessel_information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.vessel_id')" prop="vessel_id" required>
                            <el-select v-model="form.vessel_id" :placeholder="$t('equipment.fuel_inventory.placeholder.vessel_id')"
                                filterable class="w-full" @change="onVesselChange">
                                <el-option v-for="vessel in vessels" :key="vessel.id" :label="vessel.name" :value="vessel.id" />
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.tank_id')" prop="tank_id" required>
                            <el-select v-model="form.tank_id" :placeholder="$t('equipment.fuel_inventory.placeholder.tank_id')"
                                filterable class="w-full" :disabled="!form.vessel_id" @change="onTankChange">
                                <el-option v-for="tank in filteredTanks" :key="tank.id" :value="tank.id">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ tank.tank_name }}</span>
                                        <span class="text-xs text-gray-500">{{ tank.tank_code }} - {{ tank.fuel_type }}</span>
                                    </div>
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <Icon icon="mingcute:file-line" class="mr-2" />
                        {{ $t('equipment.fuel_inventory.sections.transaction_details') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.fuel_type')" prop="fuel_type" required>
                            <el-select v-model="form.fuel_type" :placeholder="$t('equipment.fuel_inventory.placeholder.fuel_type')"
                                class="w-full" :disabled="!!selectedTank">
                                <el-option v-for="(label, value) in $t('equipment.fuel_inventory.fuel_types')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.volume')" prop="volume" required>
                            <el-input v-model.number="form.volume" type="number" :placeholder="$t('equipment.fuel_inventory.placeholder.volume')"
                                class="w-full" :min="0" step="0.01">
                                <template #append>L</template>
                            </el-input>
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.unit_price')" prop="unit_price">
                            <el-input v-model.number="form.unit_price" type="number" :placeholder="$t('equipment.fuel_inventory.placeholder.unit_price')"
                                class="w-full" :min="0" step="0.01">
                                <template #prepend>Rp</template>
                            </el-input>
                        </el-form-item>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.level_before')" prop="level_before" required>
                            <el-input v-model.number="form.level_before" type="number" :placeholder="$t('equipment.fuel_inventory.placeholder.level_before')"
                                class="w-full" :min="0" step="0.01" :disabled="isEdit">
                                <template #append>L</template>
                            </el-input>
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.level_after')" prop="level_after">
                            <el-input v-model.number="form.level_after" type="number" :placeholder="$t('equipment.fuel_inventory.placeholder.level_after')"
                                class="w-full" :min="0" step="0.01" readonly>
                                <template #append>L</template>
                            </el-input>
                        </el-form-item>
                    </div>
                </div>

                <!-- Transfer Information (only for transfer transactions) -->
                <div v-if="isTransferTransaction" class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <Icon icon="mingcute:transfer-line" class="mr-2" />
                        {{ $t('equipment.fuel_inventory.sections.transfer_information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.source_tank')" prop="source_tank_id" :required="isTransferTransaction">
                            <el-select v-model="form.source_tank_id" :placeholder="$t('equipment.fuel_inventory.placeholder.source_tank')"
                                filterable class="w-full">
                                <el-option v-for="tank in availableSourceTanks" :key="tank.id" :value="tank.id">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ tank.tank_name }}</span>
                                        <span class="text-xs text-gray-500">{{ tank.tank_code }} - Current: {{ formatNumber(tank.current_level) }}L</span>
                                    </div>
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.destination_tank')" prop="destination_tank_id" :required="isTransferTransaction">
                            <el-select v-model="form.destination_tank_id" :placeholder="$t('equipment.fuel_inventory.placeholder.destination_tank')"
                                filterable class="w-full">
                                <el-option v-for="tank in availableDestinationTanks" :key="tank.id" :value="tank.id">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ tank.tank_name }}</span>
                                        <span class="text-xs text-gray-500">{{ tank.tank_code }} - Current: {{ formatNumber(tank.current_level) }}L</span>
                                    </div>
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <Icon icon="mingcute:user-line" class="mr-2" />
                        {{ $t('equipment.fuel_inventory.sections.additional_information') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.operator')" prop="operator" required>
                            <el-input v-model="form.operator" :placeholder="$t('equipment.fuel_inventory.placeholder.operator')" class="w-full" />
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.supplier')" prop="supplier">
                            <el-input v-model="form.supplier" :placeholder="$t('equipment.fuel_inventory.placeholder.supplier')" class="w-full" />
                        </el-form-item>
                    </div>
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.reference_number')" prop="reference_number">
                            <el-input v-model="form.reference_number" :placeholder="$t('equipment.fuel_inventory.placeholder.reference_number')" class="w-full" />
                        </el-form-item>
                        <el-form-item :label="$t('equipment.fuel_inventory.fields.notes')" prop="notes">
                            <el-input v-model="form.notes" type="textarea" :rows="3" :placeholder="$t('equipment.fuel_inventory.placeholder.notes')" class="w-full" />
                        </el-form-item>
                    </div>
                </div>

                <!-- Summary Card -->
                <div v-if="form.volume && form.level_before !== null" class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <Icon icon="mingcute:calculator-line" class="mr-2" />
                        {{ $t('equipment.fuel_inventory.sections.transaction_summary') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-sm text-green-600 font-medium">{{ $t('equipment.fuel_inventory.fields.volume') }}</p>
                            <p class="text-xl font-bold text-green-800">{{ getVolumeDisplay() }} L</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-green-600 font-medium">{{ $t('equipment.fuel_inventory.fields.level_before') }}</p>
                            <p class="text-xl font-bold text-green-800">{{ formatNumber(form.level_before) }} L</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-green-600 font-medium">{{ $t('equipment.fuel_inventory.fields.level_after') }}</p>
                            <p class="text-xl font-bold text-green-800">{{ formatNumber(calculatedLevelAfter) }} L</p>
                        </div>
                        <div v-if="form.unit_price" class="text-center">
                            <p class="text-sm text-green-600 font-medium">{{ $t('equipment.fuel_inventory.fields.total_cost') }}</p>
                            <p class="text-xl font-bold text-green-800">Rp {{ formatNumber(totalCost) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <el-button @click="goBack">
                        {{ $t('base.actions.cancel') }}
                    </el-button>
                    <el-button type="primary" :loading="isSubmitting" @click="onSubmit">
                        <Icon icon="mingcute:save-line" class="mr-2" />
                        {{ isEdit ? $t('base.actions.update') : $t('base.actions.create') }}
                    </el-button>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
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
const formRef = ref()
const isSubmitting = ref(false)
const isEdit = computed(() => !!route.params.id)

// Form data
const form = reactive({
    transaction_date: new Date().toISOString().slice(0, 19).replace('T', ' '),
    transaction_type: '',
    vessel_id: null,
    tank_id: null,
    fuel_type: '',
    volume: null,
    unit_price: null,
    level_before: null,
    level_after: null,
    source_tank_id: null,
    destination_tank_id: null,
    operator: '',
    supplier: '',
    reference_number: '',
    notes: '',
    shift: 'day'
})

// Validation rules
const rules = {
    transaction_date: [
        { required: true, message: t('common.validation.required'), trigger: 'blur' }
    ],
    transaction_type: [
        { required: true, message: t('common.validation.required'), trigger: 'change' }
    ],
    vessel_id: [
        { required: true, message: t('common.validation.required'), trigger: 'change' }
    ],
    tank_id: [
        { required: true, message: t('common.validation.required'), trigger: 'change' }
    ],
    fuel_type: [
        { required: true, message: t('common.validation.required'), trigger: 'change' }
    ],
    volume: [
        { required: true, message: t('common.validation.required'), trigger: 'blur' },
        { type: 'number', min: 0.01, message: t('common.validation.min_value', { min: 0.01 }), trigger: 'blur' }
    ],
    level_before: [
        { required: true, message: t('common.validation.required'), trigger: 'blur' },
        { type: 'number', min: 0, message: t('common.validation.min_value', { min: 0 }), trigger: 'blur' }
    ],
    operator: [
        { required: true, message: t('common.validation.required'), trigger: 'blur' }
    ],
    shift: [
        { required: true, message: t('common.validation.required'), trigger: 'change' }
    ]
}

// Mock data
const vessels = ref([
    { id: 1, name: 'FPSO Vessel Alpha' },
    { id: 2, name: 'FPSO Vessel Beta' }
])

const fuelTanks = ref([
    { id: 1, vessel_id: 1, tank_name: 'Main Fuel Storage Tank', tank_code: 'FT-001', fuel_type: 'diesel', current_level: 15000, capacity: 20000 },
    { id: 2, vessel_id: 1, tank_name: 'Service Tank A', tank_code: 'FT-002', fuel_type: 'diesel', current_level: 8500, capacity: 10000 },
    { id: 3, vessel_id: 1, tank_name: 'Day Tank 1', tank_code: 'FT-003', fuel_type: 'diesel', current_level: 2800, capacity: 5000 },
    { id: 4, vessel_id: 2, tank_name: 'Main Storage Beta', tank_code: 'FT-004', fuel_type: 'marine_gas_oil', current_level: 12000, capacity: 18000 }
])

// Computed properties
const filteredTanks = computed(() => {
    if (!form.vessel_id) return []
    return fuelTanks.value.filter(tank => tank.vessel_id === form.vessel_id)
})

const selectedTank = computed(() => {
    return fuelTanks.value.find(tank => tank.id === form.tank_id)
})

const isTransferTransaction = computed(() => {
    return form.transaction_type === 'transfer_in' || form.transaction_type === 'transfer_out'
})

const availableSourceTanks = computed(() => {
    return fuelTanks.value.filter(tank => 
        tank.id !== form.tank_id && 
        tank.fuel_type === form.fuel_type &&
        tank.current_level > 0
    )
})

const availableDestinationTanks = computed(() => {
    return fuelTanks.value.filter(tank => 
        tank.id !== form.tank_id && 
        tank.fuel_type === form.fuel_type &&
        tank.current_level < tank.capacity
    )
})

const calculatedLevelAfter = computed(() => {
    if (form.level_before === null || form.volume === null) return 0
    
    const multiplier = (form.transaction_type === 'add' || form.transaction_type === 'transfer_in') ? 1 : -1
    return Math.max(0, form.level_before + (form.volume * multiplier))
})

const totalCost = computed(() => {
    if (!form.volume || !form.unit_price) return 0
    return form.volume * form.unit_price
})

// Helper methods
const formatNumber = (value) => {
    if (!value) return '0'
    return new Intl.NumberFormat('id-ID').format(value)
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

const getVolumeDisplay = () => {
    if (!form.volume) return '0'
    const prefix = (form.transaction_type === 'add' || form.transaction_type === 'transfer_in') ? '+' : '-'
    return `${prefix}${formatNumber(Math.abs(form.volume))}`
}

// Event handlers
const onTransactionTypeChange = () => {
    // Reset transfer-related fields when transaction type changes
    if (!isTransferTransaction.value) {
        form.source_tank_id = null
        form.destination_tank_id = null
    }
}

const onVesselChange = () => {
    form.tank_id = null
    form.fuel_type = ''
    form.level_before = null
}

const onTankChange = () => {
    if (selectedTank.value) {
        form.fuel_type = selectedTank.value.fuel_type
        form.level_before = selectedTank.value.current_level
    }
}

const goBack = () => {
    router.push({ name: 'equipment.fuel-inventory.index' })
}

const onSubmit = async () => {
    try {
        const valid = await formRef.value.validate()
        if (!valid) return

        isSubmitting.value = true

        // Calculate level_after
        form.level_after = calculatedLevelAfter.value

        const url = isEdit.value 
            ? `/api/equipment/fuel-inventory/${route.params.id}`
            : '/api/equipment/fuel-inventory'
        
        const method = isEdit.value ? 'put' : 'post'
        
        await axios[method](url, form)
        
        ElMessage.success(
            isEdit.value 
                ? t('common.success.updated') 
                : t('common.success.created')
        )
        
        goBack()
    } catch (error) {
        ElMessage.error(t('common.errors.operation_failed'))
    } finally {
        isSubmitting.value = false
    }
}

// Data fetching
const fetchData = async () => {
    if (!isEdit.value) return

    try {
        const response = await axios.get(`/api/equipment/fuel-inventory/${route.params.id}`)
        const data = response.data.result
        
        // Populate form with existing data
        Object.keys(form).forEach(key => {
            if (data[key] !== undefined) {
                form[key] = data[key]
            }
        })
    } catch (error) {
        ElMessage.error(t('common.errors.fetch_failed'))
        goBack()
    }
}

// Watchers
watch(() => form.volume, () => {
    // Auto-calculate level_after when volume changes
    form.level_after = calculatedLevelAfter.value
})

watch(() => form.level_before, () => {
    // Auto-calculate level_after when level_before changes
    form.level_after = calculatedLevelAfter.value
})

// Lifecycle
onMounted(() => {
    fetchData()
})
</script>