<template>
    <div class="content">
        <!-- Page Header -->
        <PageHeader :title="isEdit ? $t('equipment.maintenance.edit') : $t('equipment.maintenance.create')">
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
                        {{ $t('equipment.maintenance.sections.basic_information') }}
                    </h3>
                    <el-row :gutter="24">
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.activity_date')" prop="activity_date">
                                <el-date-picker 
                                    v-model="form.activity_date" 
                                    type="date"
                                    :placeholder="$t('equipment.maintenance.placeholder.activity_date')"
                                    format="DD-MM-YYYY" 
                                    value-format="YYYY-MM-DD" 
                                    style="width: 100%"
                                    @change="() => formRef?.validateField('activity_date')" />
                            </el-form-item>
                        </el-col>
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.work_type')" prop="work_type">
                                <el-select 
                                    v-model="form.work_type"
                                    :placeholder="$t('equipment.maintenance.placeholder.work_type')" 
                                    class="w-full">
                                    <el-option v-for="option in workTypeOptions" :key="option.value"
                                        :label="option.label" :value="option.value">
                                        <div class="flex items-center">
                                            <Icon :icon="option.icon" :class="option.color" class="mr-2" />
                                            <span>{{ option.label }}</span>
                                        </div>
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.status')" prop="status">
                                <el-select 
                                    v-model="form.status"
                                    :placeholder="$t('equipment.maintenance.placeholder.status')" 
                                    class="w-full"
                                    @change="onStatusChange">
                                    <el-option v-for="option in statusOptions" :key="option.value"
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

                <!-- Work Details -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon icon="mdi:wrench-outline" class="mr-2 text-green-600" />
                        {{ $t('equipment.maintenance.sections.work_details') }}
                    </h3>
                    <el-row :gutter="24">
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.equipment_id')" prop="equipment_id">
                                <SelectEquipment 
                                    v-model="form.equipment_id" 
                                    v-model:equipment="form.equipment"
                                    :placeholder="$t('equipment.maintenance.placeholder.equipment_id')" 
                                    class="w-full"
                                    :clearable="true" />
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $t('equipment.maintenance.fields.equipment_optional') }}
                                </div>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.work_order_no')" prop="work_order_no">
                                <el-input 
                                    v-model="form.work_order_no"
                                    :placeholder="$t('equipment.maintenance.placeholder.work_order_no')"
                                    :maxlength="50"
                                    @change="() => formRef?.validateField('work_order_no')">
                                    <template #prefix>
                                        <Icon icon="mdi:file-document-outline" class="text-blue-600" :width="16" :height="16" />
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    
                    <el-form-item :label="$t('equipment.maintenance.fields.description')" prop="description" class="mb-4">
                        <el-input 
                            v-model="form.description" 
                            type="textarea"
                            :placeholder="$t('equipment.maintenance.placeholder.description')" 
                            :rows="4" 
                            :maxlength="1000"
                            show-word-limit
                            @change="() => formRef?.validateField('description')" />
                    </el-form-item>
                </div>

                <!-- Resources & Completion -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon icon="mdi:account-hard-hat" class="mr-2 text-orange-600" />
                        {{ $t('equipment.maintenance.sections.resources_completion') }}
                    </h3>
                    <el-row :gutter="24">
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.work_hours')" prop="work_hours">
                                <el-input 
                                    v-model.number="form.work_hours"
                                    type="number"
                                    :placeholder="$t('equipment.maintenance.placeholder.work_hours')"
                                    :min="0" 
                                    :step="0.5"
                                    @change="() => formRef?.validateField('work_hours')">
                                    <template #prefix>
                                        <Icon icon="mdi:clock-outline" class="text-blue-600" :width="16" :height="16" />
                                    </template>
                                    <template #suffix>
                                        <span class="text-gray-500 text-sm">hours</span>
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.manpower_count')" prop="manpower_count">
                                <el-input 
                                    v-model.number="form.manpower_count"
                                    type="number"
                                    :placeholder="$t('equipment.maintenance.placeholder.manpower_count')"
                                    :min="1" 
                                    :step="1"
                                    @change="() => formRef?.validateField('manpower_count')">
                                    <template #prefix>
                                        <Icon icon="mdi:account-group" class="text-blue-600" :width="16" :height="16" />
                                    </template>
                                    <template #suffix>
                                        <span class="text-gray-500 text-sm">persons</span>
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-form-item :label="$t('equipment.maintenance.fields.spare_parts_used')" prop="spare_parts_used" class="mb-4">
                        <el-input 
                            v-model="form.spare_parts_used" 
                            type="textarea"
                            :placeholder="$t('equipment.maintenance.placeholder.spare_parts_used')" 
                            :rows="3" 
                            :maxlength="500"
                            show-word-limit
                            @change="() => formRef?.validateField('spare_parts_used')" />
                    </el-form-item>
                </div>

                <!-- Completion Details (show when status is 'Completed') -->
                <div v-if="showCompletionFields" class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <Icon icon="mdi:check-circle-outline" class="mr-2 text-green-600" />
                        {{ $t('equipment.maintenance.sections.completion_details') }}
                    </h3>
                    <el-row :gutter="24">
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.completion_date')" prop="completion_date">
                                <el-date-picker 
                                    v-model="form.completion_date" 
                                    type="date"
                                    :placeholder="$t('equipment.maintenance.placeholder.completion_date')"
                                    format="DD-MM-YYYY" 
                                    value-format="YYYY-MM-DD" 
                                    style="width: 100%" />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('equipment.maintenance.fields.completed_by')" prop="completed_by">
                                <el-input 
                                    v-model="form.completed_by"
                                    :placeholder="$t('equipment.maintenance.placeholder.completed_by')"
                                    :maxlength="100"
                                    @change="() => formRef?.validateField('completed_by')">
                                    <template #prefix>
                                        <Icon icon="mdi:account-check" class="text-blue-600" :width="16" :height="16" />
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
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
import { ref, computed, onMounted, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'

import PageHeader from '@/components/PageHeader.vue'
import SelectEquipment from '@/components/select/SelectEquipment.vue'
import { useUser } from '@/composables/auth/useUser'
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
 * State form untuk Maintenance Activities
 */
const form = reactive({
  // Basic fields
  activity_date: dayjs().format('YYYY-MM-DD'),
  work_type: null, // 'Preventive', 'Corrective', 'Breakdown', 'Inspection', 'Calibration'
  status: 'Planned', // 'Planned', 'In Progress', 'Completed', 'Deferred', 'Cancelled'
  
  // Work details
  equipment_id: null,
  equipment: null,
  work_order_no: '',
  description: '',
  
  // Resources
  work_hours: null,
  manpower_count: 1,
  spare_parts_used: '',
  recorded_by: user.value?.id || null,
  
  // Completion details (conditional)
  completion_date: null,
  completed_by: ''
})

// Work Type Options
const workTypeOptions = [
  {
    value: 'Preventive',
    label: t('equipment.maintenance.work_types.preventive'),
    icon: 'mdi:calendar-clock',
    color: 'text-blue-600'
  },
  {
    value: 'Corrective',
    label: t('equipment.maintenance.work_types.corrective'),
    icon: 'mdi:wrench',
    color: 'text-orange-600'
  },
  {
    value: 'Breakdown',
    label: t('equipment.maintenance.work_types.breakdown'),
    icon: 'mdi:alert-circle',
    color: 'text-red-600'
  },
  {
    value: 'Inspection',
    label: t('equipment.maintenance.work_types.inspection'),
    icon: 'mdi:magnify',
    color: 'text-green-600'
  },
  {
    value: 'Calibration',
    label: t('equipment.maintenance.work_types.calibration'),
    icon: 'mdi:tune',
    color: 'text-purple-600'
  }
]

// Status Options
const statusOptions = [
  {
    value: 'Planned',
    label: t('equipment.maintenance.status.planned'),
    icon: 'mdi:calendar-outline',
    color: 'text-blue-600'
  },
  {
    value: 'In Progress',
    label: t('equipment.maintenance.status.in_progress'),
    icon: 'mdi:progress-clock',
    color: 'text-orange-600'
  },
  {
    value: 'Completed',
    label: t('equipment.maintenance.status.completed'),
    icon: 'mdi:check-circle',
    color: 'text-green-600'
  },
  {
    value: 'Deferred',
    label: t('equipment.maintenance.status.deferred'),
    icon: 'mdi:clock-pause',
    color: 'text-yellow-600'
  },
  {
    value: 'Cancelled',
    label: t('equipment.maintenance.status.cancelled'),
    icon: 'mdi:cancel',
    color: 'text-red-600'
  }
]

// Show completion fields when status is 'Completed'
const showCompletionFields = computed(() => {
  return form.status === 'Completed'
})

/* -------------------- VALIDATION HELPERS -------------------- */
const requiredMsg = (attr) => t('common.validation.required', { attribute: attr })

// Date validator 
const validDate = {
  validator: (_rule, value, callback) => {
    if (!value) return callback(new Error(requiredMsg(t('equipment.maintenance.fields.activity_date'))))
    
    const selectedDate = dayjs(value)
    if (!selectedDate.isValid()) {
      return callback(new Error(t('common.validation.date', { attribute: t('equipment.maintenance.fields.activity_date') })))
    }

    callback()
  },
  trigger: ['change', 'blur']
}

// Completion date validator (should not be before activity date)
const validCompletionDate = {
  validator: (_rule, value, callback) => {
    if (!value && form.status === 'Completed') {
      return callback(new Error(requiredMsg(t('equipment.maintenance.fields.completion_date'))))
    }
    
    if (value) {
      const completionDate = dayjs(value)
      const activityDate = dayjs(form.activity_date)
      
      if (!completionDate.isValid()) {
        return callback(new Error(t('common.validation.date', { attribute: t('equipment.maintenance.fields.completion_date') })))
      }
      
      if (completionDate.isBefore(activityDate)) {
        return callback(new Error(t('equipment.maintenance.validation.completion_after_activity')))
      }
    }

    callback()
  },
  trigger: ['change', 'blur']
}

/* -------------------- FORM RULES -------------------- */
const formRules = computed(() => {
  const rules = {
    activity_date: [validDate],
    work_type: [
      { required: true, message: requiredMsg(t('equipment.maintenance.fields.work_type')), trigger: 'change' }
    ],
    status: [
      { required: true, message: requiredMsg(t('equipment.maintenance.fields.status')), trigger: 'change' }
    ],
    description: [
      { required: true, message: requiredMsg(t('equipment.maintenance.fields.description')), trigger: 'blur' },
      { max: 1000, message: t('common.validation.max', { attribute: t('equipment.maintenance.fields.description'), max: 1000 }), trigger: 'blur' }
    ],
    work_order_no: [
      { max: 50, message: t('common.validation.max', { attribute: t('equipment.maintenance.fields.work_order_no'), max: 50 }), trigger: 'blur' }
    ],
    work_hours: [
      { type: 'number', min: 0, message: t('common.validation.min.numeric', { attribute: t('equipment.maintenance.fields.work_hours'), min: 0 }), trigger: 'blur' }
    ],
    manpower_count: [
      { type: 'number', min: 1, message: t('common.validation.min.numeric', { attribute: t('equipment.maintenance.fields.manpower_count'), min: 1 }), trigger: 'blur' }
    ],
    spare_parts_used: [
      { max: 500, message: t('common.validation.max', { attribute: t('equipment.maintenance.fields.spare_parts_used'), max: 500 }), trigger: 'blur' }
    ],
    recorded_by: [
      { required: true, message: requiredMsg(t('equipment.maintenance.fields.recorded_by')), trigger: 'change' }
    ],
    completion_date: [validCompletionDate],
    completed_by: [
      { max: 100, message: t('common.validation.max', { attribute: t('equipment.maintenance.fields.completed_by'), max: 100 }), trigger: 'blur' }
    ]
  }

  // Add conditional validation for completion fields
  if (form.status === 'Completed') {
    rules.completed_by = [
      { required: true, message: requiredMsg(t('equipment.maintenance.fields.completed_by')), trigger: 'blur' },
      ...rules.completed_by
    ]
  }

  return rules
})

/* -------------------- EVENT HANDLERS -------------------- */
const onStatusChange = () => {
  // Auto-fill completion date when status changed to 'Completed'
  if (form.status === 'Completed' && !form.completion_date) {
    form.completion_date = dayjs().format('YYYY-MM-DD')
  }
  
  // Clear completion fields when status is not 'Completed'
  if (form.status !== 'Completed') {
    form.completion_date = null
    form.completed_by = ''
  }
  
  // Re-validate form
  formRef.value?.clearValidate(['completion_date', 'completed_by'])
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
      router.push({ name: 'equipment.maintenance.index' })
    })
  } else {
    router.push({ name: 'equipment.maintenance.index' })
  }
}

const hasUnsavedChanges = () => {
  return Object.values(form).some(value => {
    if (typeof value === 'object' && value !== null) {
      return Object.keys(value).length > 0
    }
    return value !== '' && value !== null && value !== 1 && value !== 'Planned'
  })
}

const fetchData = async () => {
  if (!isEdit.value) return
  
  try {
    loading.value = true
    const response = await axios.get(`/api/maintenance/activities/${route.params.id}`)
    
    if (response.data && response.data.success) {
      const data = response.data.result
      Object.assign(form, {
        activity_date: data.activity_date || dayjs().format('YYYY-MM-DD'),
        work_type: data.work_type || null,
        status: data.status || 'Planned',
        equipment_id: data.equipment_id || null,
        equipment: data.equipment || null,
        work_order_no: data.work_order_no || '',
        description: data.description || '',
        work_hours: data.work_hours || null,
        manpower_count: data.manpower_count || 1,
        spare_parts_used: data.spare_parts_used || '',
        recorded_by: data.recorded_by || user.value?.id,
        completion_date: data.completion_date || null,
        completed_by: data.completed_by || ''
      })
    }
  } catch (error) {
    console.error('Error fetching maintenance activity data:', error)
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

      // Clean payload - remove empty values
      const payload = {
        ...form,
        // Convert empty strings to null for optional fields
        work_order_no: form.work_order_no || null,
        equipment_id: form.equipment_id || null,
        work_hours: form.work_hours || null,
        spare_parts_used: form.spare_parts_used || null,
        completion_date: form.completion_date || null,
        completed_by: form.completed_by || null
      }

      // Remove equipment object (we only need equipment_id)
      delete payload.equipment

      const url = isEdit.value 
        ? `/api/maintenance/activities/${route.params.id}` 
        : '/api/maintenance/activities'
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
        router.push({ name: 'equipment.maintenance.index' })
      } else {
        throw new Error(response.data?.message || 'Operation failed')
      }
    } catch (error) {
      console.error('Error saving maintenance activity:', error)
      
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