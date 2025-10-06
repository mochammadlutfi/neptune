<template>
    <div class="content">
        <!-- Page Header -->
        <PageHeader :title="$t(title)">
            <template #actions>
                <el-tooltip :content="$t('common.form.form_help')" placement="bottom">
                    <el-button circle>
                        <Icon icon="mingcute:question-line" />
                    </el-button>
                </el-tooltip>
            </template>
        </PageHeader>

        <!-- Form Content -->
        <el-card body-class="!p-0" class="!rounded-lg !shadow-lg" v-loading="loading">
            <el-form :model="form" :rules="formRules" ref="formRef" @submit.prevent="onSubmit" label-position="top">
                
                <!-- Section 1: Basic Contract Information -->
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 2xl:grid-cols-3 gap-6">
                        <el-form-item :label="$t('master.equipment.fields.vessel_id')" prop="vessel_id">
                            <select-vessel v-model="form.vessel_id"/>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.code')" prop="code">
                            <el-input
                                v-model="form.code"
                                placeholder="e.g., EQ-001"
                                :disabled="isEdit"
                                maxlength="20"
                                show-word-limit
                            >
                                <template #prefix>
                                    <Icon icon="mdi:tag" :width="16" :height="16" />
                                </template>
                            </el-input>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.tag')" prop="tag">
                            <el-input
                                v-model="form.tag"
                                placeholder="e.g., GT-6640A"
                                maxlength="50"
                                show-word-limit
                            >
                                <template #prefix>
                                    <Icon icon="mdi:tag-outline" :width="16" :height="16" />
                                </template>
                            </el-input>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.name')" prop="name">
                            <el-input
                                v-model="form.name"
                                placeholder="e.g., Gas Turbine Generator A"
                                maxlength="200"
                                show-word-limit
                            >
                                <template #prefix>
                                    <Icon icon="mdi:wrench" :width="16" :height="16" />
                                </template>
                            </el-input>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.type')" prop="type">
                            <el-select
                                v-model="form.type"
                                :placeholder="$t('master.equipment.placeholders.type')"
                                style="width: 100%"
                            >
                                <el-option
                                    v-for="(label, value) in equipmentTypes"
                                    :key="value"
                                    :label="label"
                                    :value="value"
                                >
                                    <div class="flex items-center">
                                        <Icon :icon="getEquipmentTypeIcon(value)" class="mr-2" :width="16" :height="16" />
                                        {{ label }}
                                    </div>
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.category')" prop="category">
                            <el-select
                                v-model="form.category"
                                :placeholder="$t('master.equipment.placeholders.category')"
                                style="width: 100%"
                            >
                                <el-option
                                    v-for="(label, value) in categories"
                                    :key="value"
                                    :label="label"
                                    :value="value"
                                >
                                    <div class="flex items-center">
                                        {{ label }}
                                    </div>
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.is_critical')" prop="is_critical">
                            <el-switch
                                v-model="form.is_critical"
                                :active-text="$t('master.equipment.critical_yes')"
                                :inactive-text="$t('master.equipment.critical_no')"
                            />
                        </el-form-item>

                        <!-- <el-form-item :label="$t('master.equipment.fields.status')" prop="status">
                            <el-select
                                v-model="form.status"
                                placeholder="Select status"
                                style="width: 100%"
                            >
                                <el-option
                                    v-for="(label, value) in equipmentStatus"
                                    :key="value"
                                    :label="label"
                                    :value="value"
                                >
                                    <div class="flex items-center">
                                        <div 
                                            :class="getEquipmentStatusColor(value)"
                                            class="w-3 h-3 rounded-full mr-2"
                                        ></div>
                                        {{ label }}
                                    </div>
                                </el-option>
                            </el-select>
                        </el-form-item> -->

                        <el-form-item :label="$t('master.equipment.fields.manufacturer')" prop="manufacturer">
                            <el-input
                                v-model="form.manufacturer"
                                placeholder="e.g., Siemens, GE, Rolls-Royce"
                                maxlength="100"
                                show-word-limit
                            >
                                <template #prefix>
                                    <Icon icon="mdi:factory" :width="16" :height="16" />
                                </template>
                            </el-input>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.model')" prop="model">
                            <el-input
                                v-model="form.model"
                                placeholder="e.g., SGT-A65, MS5002E"
                                maxlength="100"
                                show-word-limit
                            >
                                <template #prefix>
                                    <Icon icon="mdi:cube-outline" :width="16" :height="16" />
                                </template>
                            </el-input>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.serial_number')" prop="serial_number">
                            <el-input
                                v-model="form.serial_number"
                                placeholder="e.g., SN123456789"
                                maxlength="50"
                                show-word-limit
                            >
                                <template #prefix>
                                    <Icon icon="mdi:barcode" :width="16" :height="16" />
                                </template>
                            </el-input>
                        </el-form-item>

                        <el-form-item :label="$t('master.equipment.fields.installation_date')" prop="installation_date">
                            <el-date-picker
                                v-model="form.installation_date"
                                type="date"
                                placeholder="Select installation date"
                                format="DD/MM/YYYY"
                                value-format="YYYY-MM-DD"
                                style="width: 100%"
                            >
                            </el-date-picker>
                        </el-form-item>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="p-6 border-t">
                    <div class="flex justify-end space-x-3">
                        <el-button @click="$router.go(-1)">
                            <Icon icon="mingcute:arrow-left-line" class="mr-2" />
                            {{ $t('common.actions.cancel') }}
                        </el-button>
                        <el-button native-type="submit" type="primary" :loading="loading">
                            <Icon icon="mingcute:check-fill" class="mr-2" />
                            {{ $t('common.actions.save') }}
                        </el-button>
                    </div>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="js">
import { Icon } from '@iconify/vue';
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

import PageHeader from '@/components/PageHeader.vue'
import SelectVessel from '@/components/select/SelectVessel.vue'
import { equipmentApi } from '@/api/master/equipment'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const title = ref(route.meta.title ?? 'master.equipment.create')
const formRef = ref(null)

// State
const loading = ref(false)
const vessels = ref([])

// Check if editing
const isEdit = computed(() => !!route.params.id)

// Form data
const form = ref({
    vessel_id: null,
    code: '',
    tag: '',
    name: '',
    type: '',
    category: '',
    sub_category: '',
    manufacturer: '',
    model: '',
    serial_number: '',
    installation_date: null,
    is_critical: false,
    status: 'stopped'
})

// Options - Use translations
const equipmentTypes = {
    'Gas Turbine' : 'Gas Turbine',
    'Compressor' : 'Compressor',
    'Pump' : 'Pump',
    'Separator' : 'Separator',
    'Heat Exchanger' : 'Heat Exchanger',
    'Valve' : 'Valve',
    'Instrument' : 'Instrument',
    'Electrical' : 'Electrical',
    'Structural' : 'Structural',
    'Piping' : 'Piping',
    'Other' : 'Other'
}

const categories = {
    'Production' : 'Production',
    'Utilities' : 'Utilities',
    'Safety' : 'Safety',
    'Marine' : 'Marine',
    'HVAC' : 'HVAC',
    'Instrumentation' : 'Instrumentation',
    'Electrical' : 'Electrical',
    'Telecommunications' : 'Telecommunications'
}

const getEquipmentTypeIcon = (type) => {
  const icons = {
    'Gas Turbine': 'mdi:engine',
    'Compressor': 'mdi:air-purifier',
    'Pump': 'mdi:pump',
    'Separator': 'mdi:filter-variant',
    'Heat Exchanger': 'mdi:radiator',
    'Valve': 'mdi:valve',
    'Instrument': 'mdi:gauge',
    'Electrical': 'mdi:flash',
    'Structural': 'mdi:pillar',
    'Piping': 'mdi:pipe',
    'Other': 'mdi:wrench'
  }
  return icons[type] || 'mdi:wrench'
}

const equipmentStatus = {
  'running': t('master.equipment.status.running'),
  'stopped': t('master.equipment.status.stopped'),
  'standby': t('master.equipment.status.standby'),
  'maintenance': t('master.equipment.status.maintenance'),
  'out_of_service': t('master.equipment.status.out_of_service')
}

const getEquipmentStatusColor = (status) => {
  const colors = {
    'running': 'bg-green-500',
    'stopped': 'bg-red-500',
    'standby': 'bg-orange-500',
    'maintenance': 'bg-blue-500',
    'out_of_service': 'bg-gray-500'
  }
  return colors[status] || 'bg-gray-500'
}

// Validation rules
const formRules = ref({
    vessel_id: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.equipment.fields.vessel_id') }),
        trigger: 'change'
    }],
    
    code: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.equipment.fields.code') }),
        trigger: 'blur'
    }, {
        min: 2,
        max: 20,
        message: t('common.validation.between', { attribute: t('master.equipment.fields.code'), min: 2, max: 20 }),
        trigger: 'blur'
    }],
    
    name: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.equipment.fields.name') }),
        trigger: 'blur'
    }, {
        min: 2,
        max: 100,
        message: t('common.validation.between', { attribute: t('master.equipment.fields.name'), min: 2, max: 100 }),
        trigger: 'blur'
    }],
    
    type: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.equipment.fields.type') }),
        trigger: 'change'
    }],
    
    tag: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.equipment.fields.tag') }),
        trigger: 'blur'
    }],
    
    manufacturer: [{
        max: 100,
        message: t('common.validation.max', { attribute: t('master.equipment.fields.manufacturer'), max: 100 }),
        trigger: 'blur'
    }],
    
    model: [{
        max: 100,
        message: t('common.validation.max', { attribute: t('master.equipment.fields.model'), max: 100 }),
        trigger: 'blur'
    }],
    
    serial_number: [{
        max: 50,
        message: t('common.validation.max', { attribute: t('master.equipment.fields.serial_number'), max: 50 }),
        trigger: 'blur'
    }]
})

// Load well data for editing
const loadWell = async () => {
    if (!isEdit.value) return
    
    try {
        loading.value = true
        const response = await equipmentApi.getById(route.params.id)
        const wellData = response.data.data
        
        // Map the data to form
        Object.keys(form.value).forEach(key => {
            if (wellData[key] !== undefined) {
                form.value[key] = wellData[key]
            }
        })
    } catch (error) {
        console.error('Failed to load well:', error)
        ElMessage.error(t('common.errors.server_error'))
        onBack()
    } finally {
        loading.value = false
    }
}

// Submit form
const onSubmit = async () => {
    if (!formRef.value) return
    
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                loading.value = true
                
                // Prepare form data
                const formData = { ...form.value }

                const url = route.params.id ? `/master/equipment/${route.params.id}/update` : '/master/equipment/store'
                const method = route.params.id ? 'put' : 'post'

                const response = await axios({
                    method,
                    url,
                    data: formData,
                })
                
                if (response.status === 200 || response.status === 201) {
                    ElMessage({
                        message: t('common.messages.saved'),
                        type: 'success',
                    })
                    router.replace({ path: `/master/equipment/${response.data.id || response.data.data?.id}` })
                }
            } catch (error) {
                console.log(error)
                loading.value = false
                
                // Handle validation errors from server
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors
                    let errorMessage = t('common.errors.validation_failed')
                    
                    if (errors) {
                        const firstError = Object.values(errors)[0]
                        if (firstError && firstError[0]) {
                            errorMessage = firstError[0]
                        }
                    }
                    
                    ElMessage({
                        message: errorMessage,
                        type: 'error',
                    })
                } else {
                    ElMessage({
                        message: t('common.errors.server_error'),
                        type: 'error',
                    })
                }
            }
        } else {
            ElMessage({
                message: t('common.errors.validation_failed'),
                type: 'error',
            })
        }
    })
}

// Go back
const onBack = () => {
    router.push({ name: 'master.wells.index' })
}

// Initialize
onMounted(() => {
    loadWell()
})
</script>
