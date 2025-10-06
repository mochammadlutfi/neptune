<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="isEdit ? $t('equipment.fuel_tanks.edit') : $t('equipment.fuel_tanks.create')" 
            :show-back="true" @back="onBack" />

        <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
            <el-form ref="formRef" :model="form" :rules="rules" label-position="top" @submit.prevent="onSubmit">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <Icon icon="mingcute:information-line" class="mr-2" />
                            {{ $t('equipment.fuel_tanks.sections.basic_info') }}
                        </h3>
                    </div>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.tank_code')" prop="tank_code">
                        <el-input v-model="form.tank_code" :placeholder="$t('equipment.fuel_tanks.placeholder.tank_code')"
                            maxlength="20" show-word-limit />
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.tank_name')" prop="tank_name">
                        <el-input v-model="form.tank_name" :placeholder="$t('equipment.fuel_tanks.placeholder.tank_name')"
                            maxlength="100" show-word-limit />
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.vessel_id')" prop="vessel_id">
                        <el-select v-model="form.vessel_id" :placeholder="$t('equipment.fuel_tanks.placeholder.vessel_id')"
                            filterable class="w-full">
                            <el-option v-for="vessel in vessels" :key="vessel.id" :label="vessel.name" :value="vessel.id" />
                        </el-select>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.location')" prop="location">
                        <el-input v-model="form.location" :placeholder="$t('equipment.fuel_tanks.placeholder.location')"
                            maxlength="100" show-word-limit />
                    </el-form-item>

                    <!-- Tank Specifications -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <Icon icon="mingcute:settings-3-line" class="mr-2" />
                            {{ $t('equipment.fuel_tanks.sections.specifications') }}
                        </h3>
                    </div>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.tank_type')" prop="tank_type">
                        <el-select v-model="form.tank_type" :placeholder="$t('equipment.fuel_tanks.placeholder.tank_type')"
                            class="w-full">
                            <el-option v-for="(label, value) in $t('equipment.fuel_tanks.tank_types')" :key="value" 
                                :label="label" :value="value" />
                        </el-select>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.fuel_type')" prop="fuel_type">
                        <el-select v-model="form.fuel_type" :placeholder="$t('equipment.fuel_tanks.placeholder.fuel_type')"
                            class="w-full">
                            <el-option v-for="(label, value) in $t('equipment.fuel_tanks.fuel_types')" :key="value" 
                                :label="label" :value="value" />
                        </el-select>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.capacity')" prop="capacity">
                        <el-input-number v-model="form.capacity" :placeholder="$t('equipment.fuel_tanks.placeholder.capacity')"
                            :min="0" :max="999999" :precision="2" class="w-full">
                            <template #append>L</template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.min_level')" prop="min_level">
                        <el-input-number v-model="form.min_level" :placeholder="$t('equipment.fuel_tanks.placeholder.min_level')"
                            :min="0" :max="form.capacity || 999999" :precision="2" class="w-full">
                            <template #append>L</template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.max_level')" prop="max_level">
                        <el-input-number v-model="form.max_level" :placeholder="$t('equipment.fuel_tanks.placeholder.max_level')"
                            :min="form.min_level || 0" :max="form.capacity || 999999" :precision="2" class="w-full">
                            <template #append>L</template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.current_level')" prop="current_level">
                        <el-input-number v-model="form.current_level" :placeholder="$t('equipment.fuel_tanks.placeholder.current_level')"
                            :min="0" :max="form.capacity || 999999" :precision="2" class="w-full">
                            <template #append>L</template>
                        </el-input-number>
                    </el-form-item>

                    <!-- Physical Dimensions -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <Icon icon="mingcute:ruler-line" class="mr-2" />
                            {{ $t('equipment.fuel_tanks.sections.dimensions') }}
                        </h3>
                    </div>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.length')" prop="length">
                        <el-input-number v-model="form.length" :placeholder="$t('equipment.fuel_tanks.placeholder.length')"
                            :min="0" :precision="2" class="w-full">
                            <template #append>m</template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.width')" prop="width">
                        <el-input-number v-model="form.width" :placeholder="$t('equipment.fuel_tanks.placeholder.width')"
                            :min="0" :precision="2" class="w-full">
                            <template #append>m</template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.height')" prop="height">
                        <el-input-number v-model="form.height" :placeholder="$t('equipment.fuel_tanks.placeholder.height')"
                            :min="0" :precision="2" class="w-full">
                            <template #append>m</template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.material')" prop="material">
                        <el-select v-model="form.material" :placeholder="$t('equipment.fuel_tanks.placeholder.material')"
                            class="w-full">
                            <el-option v-for="(label, value) in $t('equipment.fuel_tanks.materials')" :key="value" 
                                :label="label" :value="value" />
                        </el-select>
                    </el-form-item>

                    <!-- Monitoring & Safety -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <Icon icon="mingcute:shield-check-line" class="mr-2" />
                            {{ $t('equipment.fuel_tanks.sections.monitoring_safety') }}
                        </h3>
                    </div>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.sensor_type')" prop="sensor_type">
                        <el-select v-model="form.sensor_type" :placeholder="$t('equipment.fuel_tanks.placeholder.sensor_type')"
                            class="w-full">
                            <el-option v-for="(label, value) in $t('equipment.fuel_tanks.sensor_types')" :key="value" 
                                :label="label" :value="value" />
                        </el-select>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.alarm_low_level')" prop="alarm_low_level">
                        <el-input-number v-model="form.alarm_low_level" :placeholder="$t('equipment.fuel_tanks.placeholder.alarm_low_level')"
                            :min="0" :max="form.capacity || 999999" :precision="2" class="w-full">
                            <template #append>L</template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.alarm_high_level')" prop="alarm_high_level">
                        <el-input-number v-model="form.alarm_high_level" :placeholder="$t('equipment.fuel_tanks.placeholder.alarm_high_level')"
                            :min="form.alarm_low_level || 0" :max="form.capacity || 999999" :precision="2" class="w-full">
                            <template #append>L</template>
                        </el-input-number>
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.status')" prop="status">
                        <el-select v-model="form.status" :placeholder="$t('equipment.fuel_tanks.placeholder.status')"
                            class="w-full">
                            <el-option v-for="(label, value) in $t('equipment.fuel_tanks.statuses')" :key="value" 
                                :label="label" :value="value" />
                        </el-select>
                    </el-form-item>

                    <!-- Additional Information -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <Icon icon="mingcute:file-text-line" class="mr-2" />
                            {{ $t('equipment.fuel_tanks.sections.additional_info') }}
                        </h3>
                    </div>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.installation_date')" prop="installation_date">
                        <el-date-picker v-model="form.installation_date" type="date" 
                            :placeholder="$t('equipment.fuel_tanks.placeholder.installation_date')"
                            format="DD/MM/YYYY" value-format="YYYY-MM-DD" class="w-full" />
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.last_inspection')" prop="last_inspection">
                        <el-date-picker v-model="form.last_inspection" type="date" 
                            :placeholder="$t('equipment.fuel_tanks.placeholder.last_inspection')"
                            format="DD/MM/YYYY" value-format="YYYY-MM-DD" class="w-full" />
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.next_inspection')" prop="next_inspection">
                        <el-date-picker v-model="form.next_inspection" type="date" 
                            :placeholder="$t('equipment.fuel_tanks.placeholder.next_inspection')"
                            format="DD/MM/YYYY" value-format="YYYY-MM-DD" class="w-full" />
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.manufacturer')" prop="manufacturer">
                        <el-input v-model="form.manufacturer" :placeholder="$t('equipment.fuel_tanks.placeholder.manufacturer')"
                            maxlength="100" show-word-limit />
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.model')" prop="model">
                        <el-input v-model="form.model" :placeholder="$t('equipment.fuel_tanks.placeholder.model')"
                            maxlength="100" show-word-limit />
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.serial_number')" prop="serial_number">
                        <el-input v-model="form.serial_number" :placeholder="$t('equipment.fuel_tanks.placeholder.serial_number')"
                            maxlength="100" show-word-limit />
                    </el-form-item>

                    <el-form-item :label="$t('equipment.fuel_tanks.fields.notes')" prop="notes" class="lg:col-span-2">
                        <el-input v-model="form.notes" type="textarea" :rows="4" 
                            :placeholder="$t('equipment.fuel_tanks.placeholder.notes')"
                            maxlength="500" show-word-limit />
                    </el-form-item>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <el-button @click="onBack">
                        {{ $t('base.actions.cancel') }}
                    </el-button>
                    <el-button type="primary" :loading="loading" @click="onSubmit">
                        <Icon icon="mingcute:save-line" class="mr-2" />
                        {{ isEdit ? $t('base.actions.update') : $t('base.actions.create') }}
                    </el-button>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
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
const loading = ref(false)

// Computed
const isEdit = computed(() => !!route.params.id)

// Form data
const form = reactive({
    tank_code: '',
    tank_name: '',
    vessel_id: null,
    location: '',
    tank_type: '',
    fuel_type: '',
    capacity: null,
    min_level: null,
    max_level: null,
    current_level: null,
    length: null,
    width: null,
    height: null,
    material: '',
    sensor_type: '',
    alarm_low_level: null,
    alarm_high_level: null,
    status: 'active',
    installation_date: null,
    last_inspection: null,
    next_inspection: null,
    manufacturer: '',
    model: '',
    serial_number: '',
    notes: ''
})

// Validation rules
const rules = {
    tank_code: [
        { required: true, message: t('validation.required', { field: t('equipment.fuel_tanks.fields.tank_code') }), trigger: 'blur' }
    ],
    tank_name: [
        { required: true, message: t('validation.required', { field: t('equipment.fuel_tanks.fields.tank_name') }), trigger: 'blur' }
    ],
    vessel_id: [
        { required: true, message: t('validation.required', { field: t('equipment.fuel_tanks.fields.vessel_id') }), trigger: 'change' }
    ],
    tank_type: [
        { required: true, message: t('validation.required', { field: t('equipment.fuel_tanks.fields.tank_type') }), trigger: 'change' }
    ],
    fuel_type: [
        { required: true, message: t('validation.required', { field: t('equipment.fuel_tanks.fields.fuel_type') }), trigger: 'change' }
    ],
    capacity: [
        { required: true, message: t('validation.required', { field: t('equipment.fuel_tanks.fields.capacity') }), trigger: 'blur' },
        { type: 'number', min: 1, message: t('validation.min_value', { field: t('equipment.fuel_tanks.fields.capacity'), min: 1 }), trigger: 'blur' }
    ],
    status: [
        { required: true, message: t('validation.required', { field: t('equipment.fuel_tanks.fields.status') }), trigger: 'change' }
    ]
}

// Mock data for vessels
const vessels = ref([
    { id: 1, name: 'FPSO Vessel Alpha' },
    { id: 2, name: 'FPSO Vessel Beta' }
])

// Methods
const fetchData = async () => {
    if (!isEdit.value) return
    
    try {
        loading.value = true
        const response = await axios.get(`/api/equipment/fuel-tanks/${route.params.id}`)
        const data = response.data.result
        
        // Populate form with existing data
        Object.keys(form).forEach(key => {
            if (data[key] !== undefined) {
                form[key] = data[key]
            }
        })
    } catch (error) {
        ElMessage.error(t('common.errors.fetch_failed'))
        onBack()
    } finally {
        loading.value = false
    }
}

const onSubmit = async () => {
    if (!formRef.value) return
    
    try {
        const valid = await formRef.value.validate()
        if (!valid) return
        
        loading.value = true
        
        const url = isEdit.value 
            ? `/api/equipment/fuel-tanks/${route.params.id}`
            : '/api/equipment/fuel-tanks'
        
        const method = isEdit.value ? 'put' : 'post'
        
        await axios[method](url, form)
        
        ElMessage.success(
            isEdit.value 
                ? t('common.success.updated')
                : t('common.success.created')
        )
        
        onBack()
    } catch (error) {
        if (error.response?.status === 422) {
            const errors = error.response.data.errors
            Object.keys(errors).forEach(field => {
                formRef.value.setFields({
                    [field]: {
                        message: errors[field][0],
                        field: field
                    }
                })
            })
        } else {
            ElMessage.error(t('common.errors.operation_failed'))
        }
    } finally {
        loading.value = false
    }
}

const onBack = () => {
    router.push({ name: 'equipment.fuel-tanks.index' })
}

// Lifecycle
onMounted(() => {
    fetchData()
})
</script>