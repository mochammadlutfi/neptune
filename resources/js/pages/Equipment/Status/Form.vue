<template>
    <div class="content">
        <!-- Page Header -->
        <PageHeader :title="isEdit ? $t('equipment.status.edit') : $t('equipment.status.create')">
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
            <el-form ref="formRef" :model="form" :rules="formRules" label-position="top" @submit.prevent="onSubmit">
                
                <!-- Basic Information -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon icon="mdi:information-outline" class="mr-2 text-blue-600" />
                        {{ $t('equipment.status.sections.basic_information') }}
                    </h3>
                    <el-row :gutter="24">
                        <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.status.fields.equipment_id')" prop="equipment_id">
                                <SelectEquipment 
                                    v-model="form.equipment_id" 
                                    v-model:equipment="form.equipment"
                                    :placeholder="$t('equipment.status.placeholder.equipment_id')" 
                                    class="w-full"
                                    @data="(equipmentData) => form.equipment = equipmentData"
                                    @change="onEquipmentChange" />
                            </el-form-item>
                        </el-col>
                        <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.status.fields.reading_datetime')" prop="reading_datetime">
                                <el-date-picker 
                                    v-model="form.reading_datetime" 
                                    type="datetime"
                                    :placeholder="$t('equipment.status.placeholder.reading_datetime')"
                                    format="DD-MM-YYYY HH:mm" 
                                    value-format="YYYY-MM-DD HH:mm:ss" 
                                    style="width: 100%"
                                    :disabled-date="disabledDate"
                                    @change="() => formRef?.validateField('reading_datetime')" />
                            </el-form-item>
                        </el-col>
                        <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.status.fields.reading_type')" prop="reading_type">
                                <el-select 
                                    v-model="form.reading_type"
                                    :placeholder="$t('equipment.status.placeholder.reading_type')" 
                                    class="w-full"
                                    @change="onReadingTypeChange">
                                    <el-option value="real_time" :label="$t('equipment.status.reading_types.real_time')" />
                                    <el-option value="daily_assessment" :label="$t('equipment.status.reading_types.daily_assessment')" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.status.fields.operational_status')" prop="operational_status">
                                <el-select 
                                    v-model="form.operational_status"
                                    :placeholder="$t('equipment.status.placeholder.operational_status')" 
                                    class="w-full">
                                    <el-option v-for="option in operationalStatusOptions" :key="option.value"
                                        :label="option.label" :value="option.value">
                                        <div class="flex items-center">
                                            <Icon :icon="option.icon" :class="option.color" class="mr-2" />
                                            <span>{{ option.label }}</span>
                                        </div>
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>

                <!-- Common Parameters -->
                <div class="p-6 border-b border-gray-200" v-if="showCommonParameters">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon icon="mdi:cog-outline" class="mr-2 text-green-600" />
                        {{ $t('equipment.status.sections.common_parameters') }}
                    </h3>
                    <el-row :gutter="24">
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.status.fields.running_hours')" prop="running_hours">
                                <el-input
                                    v-model.number="form.running_hours"
                                    type="number"
                                    :placeholder="$t('equipment.status.placeholder.running_hours')"
                                    :min="0" 
                                    :step="0.1"
                                    @change="() => formRef?.validateField('running_hours')">
                                    <template #prefix>
                                        <Icon icon="mdi:clock-outline" class="text-blue-600" :width="16" :height="16" />
                                    </template>
                                    <template #suffix>
                                        <span class="text-gray-500 text-sm">hrs</span>
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.status.fields.shift_operator')" prop="shift_operator">
                                <el-input 
                                    v-model="form.shift_operator"
                                    :placeholder="$t('equipment.status.placeholder.shift_operator')"
                                    :maxlength="100"
                                    @change="() => formRef?.validateField('shift_operator')">
                                    <template #prefix>
                                        <Icon icon="mdi:account-outline" class="text-green-600" :width="16" :height="16" />
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.status.fields.recorded_by')" prop="recorded_by">
                                <el-input
                                    v-model="form.recorded_by"
                                    :placeholder="$t('equipment.status.placeholder.recorded_by')"
                                    @change="() => formRef?.validateField('recorded_by')">
                                    <template #prefix>
                                        <Icon icon="mdi:account-outline" class="text-blue-600" :width="16" :height="16" />
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>

                <!-- Dynamic Parameters based on Equipment Type -->
                <div v-if="currentParameterGroups.length > 0" class="dynamic-parameters">
                    <div v-for="group in currentParameterGroups" :key="group.group" class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <Icon :icon="getGroupIcon(group.group)" class="mr-2 text-blue-600" />
                            {{ $t(`equipment.status.parameter_groups.${group.group.toLowerCase().replace(/\s+/g, '_')}`) }}
                        </h3>
                        <el-row :gutter="24">
                            <el-col v-for="field in group.fields" :key="field.key" 
                                :md="getFieldColSize(field)" :sm="12" :xs="24" class="mb-4">
                                <el-form-item 
                                    :label="getFieldLabel(field)" 
                                    :prop="`parameters.${field.key}`">
                                    
                                    <!-- Number Input -->
                                    <template v-if="field.type === 'number'">
                                        <el-input
                                            v-model.number="form.parameters[field.key]"
                                            type="number"
                                            :placeholder="getFieldPlaceholder(field)"
                                            :min="field.min" 
                                            :max="field.max"
                                            :step="field.step || 0.1"
                                            @change="() => formRef?.validateField(`parameters.${field.key}`)">
                                            <template #prefix>
                                                <Icon :icon="getFieldIcon(field)" class="text-blue-600" :width="16" :height="16" />
                                            </template>
                                            <template #suffix v-if="field.unit">
                                                <span class="text-gray-500 text-sm">{{ field.unit }}</span>
                                            </template>
                                        </el-input>
                                    </template>

                                    <!-- Select Input -->
                                    <template v-else-if="field.type === 'select'">
                                        <el-select 
                                            v-model="form.parameters[field.key]"
                                            :placeholder="getFieldPlaceholder(field)"
                                            class="w-full">
                                            <el-option v-for="option in field.options" :key="option" 
                                                :label="option" :value="option" />
                                        </el-select>
                                    </template>

                                    <!-- Boolean Input -->
                                    <template v-else-if="field.type === 'boolean'">
                                        <el-switch 
                                            v-model="form.parameters[field.key]"
                                            :active-text="$t('common.words.yes')"
                                            :inactive-text="$t('common.words.no')" />
                                    </template>

                                    <!-- Textarea Input -->
                                    <template v-else-if="field.type === 'textarea'">
                                        <el-input 
                                            v-model="form.parameters[field.key]"
                                            type="textarea"
                                            :placeholder="getFieldPlaceholder(field)"
                                            :rows="field.rows || 3"
                                            :maxlength="field.maxlength || 500"
                                            show-word-limit />
                                    </template>

                                    <!-- Text Input (default) -->
                                    <template v-else>
                                        <el-input 
                                            v-model="form.parameters[field.key]"
                                            :placeholder="getFieldPlaceholder(field)"
                                            :maxlength="field.maxlength || 100" />
                                    </template>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon icon="mdi:note-text" class="mr-2 text-orange-600" />
                        {{ $t('equipment.status.sections.additional_information') }}
                    </h3>
                    <el-form-item :label="$t('equipment.status.fields.remarks')" prop="remarks">
                        <el-input 
                            v-model="form.remarks" 
                            type="textarea"
                            :placeholder="$t('equipment.status.placeholder.remarks')" 
                            :rows="4" 
                            :maxlength="1000"
                            show-word-limit />
                    </el-form-item>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <el-button @click="onBack" :disabled="loading">
                        <Icon icon="mdi:arrow-left" class="mr-2" />
                        {{ $t('common.actions.cancel') }}
                    </el-button>

                    <el-button type="primary" native-type="submit" :loading="loading">
                        <Icon icon="mdi:content-save" class="mr-2" />
                        {{ isEdit ? $t('common.actions.update') : $t('common.actions.save') }}
                    </el-button>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup>
import axios from 'axios'
import { Icon } from '@iconify/vue'
import { ref, computed, onMounted, reactive, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'

import PageHeader from '@/components/PageHeader.vue'
import SelectEquipment from '@/components/select/SelectEquipment.vue'
import { useUser } from '@/composables/auth/useUser'
import { equipmentParametersMapping, getEquipmentParameters } from './equipmentParametersMapping'
import dayjs from 'dayjs'

const user = useUser()
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const formRef = ref(null)
const loading = ref(false)

// Edit mode?
const isEdit = computed(() => !!route.params.id)

/**
 * State form untuk Equipment Status (Updated Structure)
 */
const form = reactive({
  // Basic fields
  equipment_id: null,
  equipment: null,
  reading_datetime: dayjs().format('YYYY-MM-DD HH:mm:ss'),
  reading_type: 'real_time', // 'real_time' or 'daily_assessment'
  operational_status: null, // 'operational', 'maintenance', 'breakdown', 'standby'
  shift: 'day', // 'day' or 'night'
  
  // Common parameters
  running_hours: null,
  recorded_by: user.value?.name || '',
  
  // Dynamic parameters (JSON field)
  parameters: {},
  
  // Additional info
  remarks: ''
})

// Shift Options
const shiftOptions = [
  { value: 'day', label: t('equipment.status.shift.day') },
  { value: 'night', label: t('equipment.status.shift.night') }
]

// Operational Status Options
const operationalStatusOptions = [
  {
    value: 'operational',
    label: t('equipment.status.operational_status.operational'),
    icon: 'mdi:play-circle',
    color: 'text-green-600'
  },
  {
    value: 'standby',
    label: t('equipment.status.operational_status.standby'),
    icon: 'mdi:pause-circle',
    color: 'text-blue-600'
  },
  {
    value: 'breakdown',
    label: t('equipment.status.operational_status.breakdown'),
    icon: 'mdi:alert-circle',
    color: 'text-red-600'
  },
  {
    value: 'maintenance',
    label: t('equipment.status.operational_status.maintenance'),
    icon: 'mdi:wrench',
    color: 'text-orange-600'
  }
]

// Computed properties for dynamic form
const equipmentType = computed(() => {
  return form.equipment?.type || 'Other'
})

const currentParameterGroups = computed(() => {
  if (!form.equipment_id || !form.reading_type) return []
  return getEquipmentParameters(equipmentType.value, form.reading_type)
})

const showCommonParameters = computed(() => {
  return !!form.equipment_id
})

/* -------------------- VALIDATION HELPERS -------------------- */
const now = () => new Date()

// Disable future dates in date-picker (5 minutes tolerance)
const disabledDate = (date) => {
  return date.getTime() > now().getTime() + 5 * 60 * 1000
}

const requiredMsg = (attr) => t('common.validation.required', { attribute: attr })

// DateTime validator (no future dates)
const notFutureDate = {
  validator: (_rule, value, callback) => {
    if (!value) return callback(new Error(requiredMsg(t('equipment.status.fields.reading_datetime'))))
    
    const recordedAt = dayjs(value)
    if (!recordedAt.isValid()) {
      return callback(new Error(t('common.validation.date', { attribute: t('equipment.status.fields.reading_datetime') })))
    }

    const fiveMinutesFromNow = dayjs().add(5, 'minutes')
    if (recordedAt.isAfter(fiveMinutesFromNow)) {
      return callback(new Error(t('common.validation.before_or_equal', { 
        attribute: t('equipment.status.fields.reading_datetime'), 
        date: t('common.words.now') 
      })))
    }

    callback()
  },
  trigger: ['change', 'blur']
}

/* -------------------- FORM RULES -------------------- */
const formRules = computed(() => {
  const rules = {
    equipment_id: [
      { required: true, message: requiredMsg(t('equipment.status.fields.equipment_id')), trigger: 'change' }
    ],
    reading_datetime: [notFutureDate],
    reading_type: [
      { required: true, message: requiredMsg(t('equipment.status.fields.reading_type')), trigger: 'change' }
    ],
    operational_status: [
      { required: true, message: requiredMsg(t('equipment.status.fields.operational_status')), trigger: 'change' }
    ],
    recorded_by: [
      { required: true, message: requiredMsg(t('equipment.status.fields.recorded_by')), trigger: 'change' }
    ],
    remarks: [
      { max: 1000, message: t('common.validation.max', { attribute: t('equipment.status.fields.remarks'), max: 1000 }), trigger: 'blur' }
    ]
  }

  // Add dynamic validation rules for parameters
  currentParameterGroups.value.forEach(group => {
    group.fields.forEach(field => {
      if (field.required) {
        rules[`parameters.${field.key}`] = [
          { required: true, message: requiredMsg(field.label), trigger: ['change', 'blur'] }
        ]
      }
    })
  })

  return rules
})

/* -------------------- HELPER FUNCTIONS -------------------- */
const getGroupIcon = (groupName) => {
  const iconMap = {
    'Process Parameters': 'mdi:gauge',
    'Performance Parameters': 'mdi:speedometer',
    'Condition Monitoring': 'mdi:monitor-eye',
    'Power Generation': 'mdi:lightning-bolt',
    'Turbine Parameters': 'mdi:turbine',
    'Operating Parameters': 'mdi:cog',
    'Hydraulic System': 'mdi:hydraulic-oil-temperature',
    'Safety Systems': 'mdi:shield-check',
    'Availability Assessment': 'mdi:check-circle',
    'Issues & Actions': 'mdi:alert-circle',
    'Inspection Results': 'mdi:clipboard-check',
    'General Parameters': 'mdi:view-list',
    'General Assessment': 'mdi:clipboard-text'
  }
  return iconMap[groupName] || 'mdi:cog'
}

const getFieldColSize = (field) => {
  if (field.type === 'textarea') return 12
  if (field.type === 'boolean') return 6
  return 8
}

const getFieldLabel = (field) => {
  return `${field.label}${field.unit ? ` (${field.unit})` : ''}`
}

const getFieldPlaceholder = (field) => {
  return t('equipment.status.placeholder.enter_value', { field: field.label })
}

const getPrecision = (field) => {
  if (field.step) {
    const stepStr = field.step.toString()
    const decimalIndex = stepStr.indexOf('.')
    return decimalIndex === -1 ? 0 : stepStr.length - decimalIndex - 1
  }
  return 2
}

const getFieldIcon = (field) => {
  const iconMap = {
    // Pressure related
    'pressure': 'mdi:gauge',
    'psi': 'mdi:gauge',
    'bar': 'mdi:gauge',
    // Temperature related
    'temperature': 'mdi:thermometer',
    'temp': 'mdi:thermometer',
    'celsius': 'mdi:thermometer',
    'fahrenheit': 'mdi:thermometer',
    // Flow related
    'flow': 'mdi:pipe',
    'rate': 'mdi:speedometer',
    'volume': 'mdi:cube-outline',
    // Power related
    'power': 'mdi:lightning-bolt',
    'kw': 'mdi:lightning-bolt',
    'mw': 'mdi:lightning-bolt',
    'voltage': 'mdi:flash',
    'current': 'mdi:current-ac',
    // Speed related
    'speed': 'mdi:speedometer',
    'rpm': 'mdi:rotate-right',
    // Level related
    'level': 'mdi:waves',
    'height': 'mdi:ruler',
    // Vibration related
    'vibration': 'mdi:vibrate',
    // Efficiency related
    'efficiency': 'mdi:chart-line',
    'percentage': 'mdi:percent',
    // Fuel related
    'fuel': 'mdi:fuel',
    'consumption': 'mdi:gas-station'
  }
  
  const fieldKey = field.key?.toLowerCase() || ''
  const fieldLabel = field.label?.toLowerCase() || ''
  const fieldUnit = field.unit?.toLowerCase() || ''
  
  // Check for matches in key, label, or unit
  for (const [keyword, icon] of Object.entries(iconMap)) {
    if (fieldKey.includes(keyword) || fieldLabel.includes(keyword) || fieldUnit.includes(keyword)) {
      return icon
    }
  }
  
  // Default icon based on field type
  if (field.type === 'number') return 'mdi:numeric'
  if (field.type === 'select') return 'mdi:format-list-bulleted'
  if (field.type === 'boolean') return 'mdi:toggle-switch'
  if (field.type === 'textarea') return 'mdi:text'
  
  return 'mdi:form-textbox'
}

/* -------------------- EVENT HANDLERS -------------------- */
const onEquipmentChange = (value) => {
  console.log(value);
  // Reset parameters when equipment changes
  form.parameters = {}
  
  // Re-validate form
  formRef.value?.clearValidate()
}

const onReadingTypeChange = () => {
  // Reset parameters when reading type changes
  form.parameters = {}
  
  // Re-validate form
  formRef.value?.clearValidate()
}

const onBack = () => {
  if (hasUnsavedChanges()) {
    ElMessageBox.confirm(
      t('common.confirmations.confirm_leave'),
      t('common.confirmations.are_you_sure'),
      {
        confirmButtonText: t('common.actions.confirm'),
        cancelButtonText: t('common.actions.cancel'),
        type: 'warning'
      }
    ).then(() => {
      router.push({ name: 'equipment.status.index' })
    })
  } else {
    router.push({ name: 'equipment.status.index' })
  }
}

const hasUnsavedChanges = () => {
  return Object.values(form).some(value => {
    if (typeof value === 'object' && value !== null) {
      return Object.keys(value).length > 0
    }
    return value !== '' && value !== null
  })
}

const fetchData = async () => {
  if (!isEdit.value) return
  
  try {
    loading.value = true
    const response = await axios.get(`/api/equipment/status/${route.params.id}`)
    
    if (response.data && response.data.success) {
      const data = response.data.result
      Object.assign(form, {
        equipment_id: data.equipment_id || null,
        equipment: data.equipment || null,
        reading_datetime: data.reading_time || dayjs().format('YYYY-MM-DD HH:mm:ss'),
        reading_type: data.reading_type || 'real_time',
        operational_status: data.operational_status || null,
        shift: data.shift || 'day',
        running_hours: data.running_hours || null,
        recorded_by: data.recorded_by || user.value?.name,
        parameters: data.parameters || {},
        remarks: data.remarks || ''
      })
    }
  } catch (error) {
    console.error('Error fetching equipment status data:', error)
    ElMessage.error(t('common.errors.operation_failed'))
    onBack()
  } finally {
    loading.value = false
  }
}

/* -------------------- SUBMIT -------------------- */
const onSubmit = async () => {
  if (!formRef.value) return
  
  formRef.value.validate(async (valid) => {
    if (!valid) {
      return ElMessage.warning(t('common.validation.form_invalid'))
    }

    try {
      loading.value = true

      // Clean parameters - remove empty values
      const cleanParameters = {}
      Object.keys(form.parameters).forEach(key => {
        if (form.parameters[key] !== null && form.parameters[key] !== undefined && form.parameters[key] !== '') {
          cleanParameters[key] = form.parameters[key]
        }
      })

      const payload = {
        equipment_id: form.equipment_id,
        reading_time: form.reading_datetime,
        reading_type: form.reading_type,
        operational_status: form.operational_status,
        shift: form.shift,
        running_hours: form.running_hours,
        recorded_by: form.recorded_by,
        parameters: cleanParameters,
        remarks: form.remarks
      }

      const url = isEdit.value 
        ? `/api/equipment/status/${route.params.id}/edit` 
        : '/api/equipment/status/store'
      const method = isEdit.value ? 'put' : 'post'

      const response = await axios({
        method,
        url,
        data: payload
      })

      if (response.data && response.data.success) {
        ElMessage.success(
          isEdit.value 
            ? t('common.success.item_updated') 
            : t('common.success.item_created')
        )
        router.push({ name: 'equipment.status.index' })
      } else {
        throw new Error(response.data?.message || 'Operation failed')
      }
    } catch (error) {
      console.error('Error saving equipment status:', error)
      
      if (error.response?.status === 422 && error.response?.data?.errors) {
        const errors = error.response.data.errors
        Object.keys(errors).forEach(field => {
          ElMessage.error(`${field}: ${errors[field][0]}`)
        })
      } else if (error.response?.data?.message) {
        ElMessage.error(error.response.data.message)
      } else {
        ElMessage.error(t('common.errors.operation_failed'))
      }
    } finally {
      loading.value = false
    }
  })
}

// Lifecycle
onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.dynamic-parameters .el-form-item {
  margin-bottom: 16px;
}

.dynamic-parameters .el-input-number {
  width: 100%;
}

.content {
  min-height: calc(100vh - 120px);
}
</style>