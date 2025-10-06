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
      <el-form
        ref="formRef"
        :model="form"
        :rules="formRules"
        label-position="top"
        @submit.prevent="onSubmit"
      >
        <div class="p-6">
          <!-- Basic Information -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="heroicons:information-circle" class="mr-2 text-blue-600" />
              {{ $t('production.wells.sections.basic_info') }}
            </h3>
            <el-row :gutter="24">
              <!-- Well -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.well_id')" prop="well_id">
                  <select-well
                    v-model="form.well_id"
                    :vessel-id="form.vessel_id"
                    :placeholder="$t('production.wells.placeholder.well_id')"
                    @change="() => formRef?.validateField('well_id')"
                  />
                </el-form-item>
              </el-col>

              <!-- Production Date -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.production_date')" prop="production_date">
                  <el-date-picker
                    v-model="form.production_date"
                    type="date"
                    :placeholder="$t('production.wells.placeholder.production_date')"
                    format="DD-MM-YYYY"
                    value-format="YYYY-MM-DD"
                    style="width: 100%"
                    :disabled-date="disabledDate"
                    @change="() => formRef?.validateField('production_date')"
                  />
                </el-form-item>
              </el-col>

              <!-- Shift -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.shift')" prop="shift">
                  <el-select
                    v-model="form.shift"
                    :placeholder="$t('production.wells.placeholder.shift')"
                    style="width: 100%"
                    @change="() => formRef?.validateField('shift')"
                  >
                    <el-option
                      v-for="shift in shiftOptions"
                      :key="shift.value"
                      :label="shift.label"
                      :value="shift.value"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Production Rates -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="heroicons:chart-bar" class="mr-2 text-green-600" />
              {{ $t('production.wells.sections.production_rates') }}
            </h3>
            <el-row :gutter="24">
              <!-- Oil Rate -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.oil_rate_bph')" prop="oil_rate_bph">
                  <el-input
                    v-model.number="form.oil_rate_bph"
                    type="number"
                    :placeholder="$t('production.wells.placeholder.oil_rate_bph')"
                    :min="0"
                    :step="0.01"
                    @input="calculateMetrics"
                    @change="() => formRef?.validateField('oil_rate_bph')"
                  >
                    <template #prefix>
                      <Icon icon="heroicons:beaker" class="text-green-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">BPH</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Gas Rate -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.gas_rate_mscfh')" prop="gas_rate_mscfh">
                  <el-input
                    v-model.number="form.gas_rate_mscfh"
                    type="number"
                    :placeholder="$t('production.wells.placeholder.gas_rate_mscfh')"
                    :min="0"
                    :step="0.001"
                    @input="calculateMetrics"
                    @change="() => formRef?.validateField('gas_rate_mscfh')"
                  >
                    <template #prefix>
                      <Icon icon="heroicons:cloud" class="text-blue-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MSCFH</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Water Rate -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.water_rate_bph')" prop="water_rate_bph">
                  <el-input
                    v-model.number="form.water_rate_bph"
                    type="number"
                    :placeholder="$t('production.wells.placeholder.water_rate_bph')"
                    :min="0"
                    :step="0.01"
                    @input="calculateMetrics"
                    @change="() => formRef?.validateField('water_rate_bph')"
                  >
                    <template #prefix>
                      <Icon icon="heroicons:beaker" class="text-cyan-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">BPH</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <!-- Calculated Metrics (Read-only) -->
            <el-row :gutter="24" v-if="showCalculatedMetrics">
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.total_liquid_rate')">
                  <el-input
                    :value="calculatedMetrics.totalLiquidRate"
                    readonly
                    placeholder="Auto-calculated"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">BPH</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.water_cut_percent')">
                  <el-input
                    :value="calculatedMetrics.waterCut"
                    readonly
                    placeholder="Auto-calculated"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">%</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.gor')">
                  <el-input
                    :value="calculatedMetrics.gor"
                    readonly
                    placeholder="Auto-calculated"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">SCF/STB</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Well Parameters -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="mdi:gauge" class="mr-2 text-red-600" />
              {{ $t('production.wells.sections.well_parameters') }}
            </h3>
            <el-row :gutter="24">
              <!-- Wellhead Pressure -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.wellhead_pressure_psi')" prop="wellhead_pressure_psi">
                  <el-input
                    v-model.number="form.wellhead_pressure_psi"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter pressure"
                  >
                    <template #prefix>
                      <Icon icon="mdi:gauge" class="text-red-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PSI</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Wellhead Temperature -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.wellhead_temp_f')" prop="wellhead_temp_f">
                  <el-input
                    v-model.number="form.wellhead_temp_f"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter temperature"
                  >
                    <template #prefix>
                      <Icon icon="mdi:thermometer" class="text-orange-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">°F</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Separator Pressure -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.separator_pressure_psi')" prop="separator_pressure_psi">
                  <el-input
                    v-model.number="form.separator_pressure_psi"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter separator pressure"
                  >
                    <template #prefix>
                      <Icon icon="mdi:gauge" class="text-purple-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PSI</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Choke Size -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.choke_size')" prop="choke_size">
                  <el-input
                    v-model="form.choke_size"
                    :placeholder="$t('production.wells.placeholder.choke_size')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">inch</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Quality Parameters -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="heroicons:beaker" class="mr-2 text-purple-600" />
              {{ $t('production.wells.sections.quality_parameters') }}
            </h3>
            <el-row :gutter="24">
              <!-- API Gravity -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.api_gravity')" prop="api_gravity">
                  <el-input
                    v-model.number="form.api_gravity"
                    type="number"
                    :min="0"
                    :max="100"
                    :step="0.1"
                    :placeholder="$t('production.wells.placeholder.api_gravity')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">°API</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- BS&W (Basic Sediment & Water) -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.wells.fields.bs_w_percent')" prop="bs_w_percent">
                  <el-input
                    v-model.number="form.bs_w_percent"
                    type="number"
                    :min="0"
                    :max="100"
                    :step="0.1"
                    :placeholder="$t('production.wells.placeholder.bs_w_percent')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">%</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Additional Information -->
          <div class="mb-2">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="heroicons:document-text" class="mr-2 text-gray-600" />
              {{ $t('production.wells.sections.additional_info') }}
            </h3>
            <!-- Remarks -->
            <el-col :span="24" class="mb-2">
              <el-form-item :label="$t('production.wells.fields.remarks')" prop="remarks">
                <el-input
                  v-model="form.remarks"
                  type="textarea"
                  :rows="3"
                  :placeholder="$t('production.wells.placeholder.remarks')"
                  maxlength="500"
                  show-word-limit
                />
              </el-form-item>
            </el-col>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="p-6 border-t bg-gray-50">
          <div class="flex justify-between items-center">
            <!-- Left side - Additional actions -->
            <div class="flex space-x-3">
              <el-button v-if="isEdit" @click="onDuplicate" type="info" plain>
                <Icon icon="mingcute:copy-line" class="mr-2" />
                {{ $t('common.actions.duplicate') }}
              </el-button>
            </div>
            
            <!-- Right side - Main actions -->
            <div class="flex space-x-3">
              <el-button @click="onBack">
                <Icon icon="mingcute:arrow-left-line" class="mr-2" />
                {{ $t('common.actions.cancel') }}
              </el-button>
              <el-button native-type="submit" type="primary" :loading="loading">
                <Icon icon="mingcute:check-fill" class="mr-2" />
                {{ $t('common.actions.save') }}
              </el-button>
            </div>
          </div>
        </div>
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="js">
import axios from 'axios'
import { Icon } from '@iconify/vue'
import { ref, computed, onMounted, reactive, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

import PageHeader from '@/components/PageHeader.vue'
import SelectWell from '@/components/select/SelectWell.vue'
import { useUser } from '@/composables/auth/useUser'
import dayjs from 'dayjs';

const user = useUser()
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const title = ref(route.meta.title ?? 'production.wells.create')
const formRef = ref(null)
const loading = ref(false)

// edit mode
const isEdit = computed(() => !!route.params.id)

// Shift options
const shiftOptions = [
  { value: 'Day', label: t('production.wells.fields.day_shift') },
  { value: 'Night', label: t('production.wells.fields.night_shift') }
]

/**
 * State form - Updated to match database schema
 */
const form = reactive({
  vessel_id: user.value?.vessel_id || null, // Auto-filled from user
  well_id: null,
  production_date: dayjs().format('YYYY-MM-DD'),
  shift: 'Day',

  // Production rates
  oil_rate_bph: null,
  gas_rate_mscfh: null,
  water_rate_bph: null,

  // Well parameters  
  wellhead_pressure_psi: null,
  wellhead_temp_f: null,
  separator_pressure_psi: null,
  choke_size: null,

  // Quality
  api_gravity: null,
  bs_w_percent: null,

  remarks: ''
})

// Calculated metrics (derived values)
const calculatedMetrics = reactive({
  totalLiquidRate: 0,
  waterCut: 0,
  gor: 0
})

// Show calculated metrics only when we have production data
const showCalculatedMetrics = computed(() => {
  return form.oil_rate_bph > 0 || form.water_rate_bph > 0 || form.gas_rate_mscfh > 0
})

/* -------------------- VALIDATION HELPERS -------------------- */
const now = () => new Date()

// Disable future dates
const disabledDate = (date) => {
  return date.getTime() > now().getTime()
}

const requiredMsg = (attr) => t('common.validation.required', { attribute: attr })

// Generic numeric range validator with better handling
const numberRange = (min, max, attr, opts = {}) => ({
  validator: (_rule, value, callback) => {
    const allowNull = !!opts.allowNull
    if (allowNull && (value === null || value === undefined || value === '')) return callback()
    if (!allowNull && (value === null || value === undefined || value === '')) {
      return callback(new Error(requiredMsg(attr)))
    }
    if (value !== null && value !== undefined && value !== '') {
      const numValue = Number(value)
      if (Number.isNaN(numValue)) {
        return callback(new Error(t('common.validation.numeric', { attribute: attr })))
      }
      if (min !== undefined && numValue < min) {
        return callback(new Error(t('common.validation.min.numeric', { attribute: attr, min })))
      }
      if (max !== undefined && numValue > max) {
        return callback(new Error(t('common.validation.max.numeric', { attribute: attr, max })))
      }
    }
    callback()
  },
  trigger: opts.trigger || ['change', 'blur']
})

// Date validator (must not be future)
const notFutureDate = {
  validator: (_rule, value, callback) => {
    if (!value) return callback(new Error(requiredMsg(t('production.wells.fields.production_date'))))
    
    const productionDate = dayjs(value)
    if (!productionDate.isValid()) {
      return callback(new Error(t('common.validation.date', { attribute: t('production.wells.fields.production_date') })))
    }

    if (productionDate.isAfter(dayjs(), 'day')) {
      return callback(new Error(t('common.validation.before_or_equal', { 
        attribute: t('production.wells.fields.production_date'), 
        date: t('common.words.today') 
      })))
    }

    callback()
  },
  trigger: ['change', 'blur']
}

/* -------------------- VALIDATION RULES -------------------- */
const formRules = ref({
  well_id: [
    { required: true, message: requiredMsg(t('production.wells.fields.well_id')), trigger: 'change' }
  ],
  production_date: [ notFutureDate ],
  shift: [
    { required: true, message: requiredMsg(t('production.wells.fields.shift')), trigger: 'change' }
  ],
  oil_rate_bph: [
    numberRange(0, undefined, t('production.wells.fields.oil_rate_bph'), { allowNull: true })
  ],
  gas_rate_mscfh: [
    numberRange(0, undefined, t('production.wells.fields.gas_rate_mscfh'), { allowNull: true })
  ],
  water_rate_bph: [
    numberRange(0, undefined, t('production.wells.fields.water_rate_bph'), { allowNull: true })
  ],
  wellhead_pressure_psi: [
    numberRange(0, undefined, t('production.wells.fields.wellhead_pressure_psi'), { allowNull: true })
  ],
  wellhead_temp_f: [
    numberRange(0, undefined, t('production.wells.fields.wellhead_temp_f'), { allowNull: true })
  ],
  separator_pressure_psi: [
    numberRange(0, undefined, t('production.wells.fields.separator_pressure_psi'), { allowNull: true })
  ],
  api_gravity: [
    numberRange(0, 100, t('production.wells.fields.api_gravity'), { allowNull: true })
  ],
  bs_w_percent: [
    numberRange(0, 100, t('production.wells.fields.bs_w_percent'), { allowNull: true })
  ],
  remarks: [
    { min: 0, max: 500, message: t('common.validation.max.string', { attribute: t('common.fields.remarks'), max: 500 }), trigger: 'blur' }
  ]
})

/* -------------------- CALCULATIONS -------------------- */
const calculateMetrics = () => {
  const oil = Number(form.oil_rate_bph) || 0
  const water = Number(form.water_rate_bph) || 0
  const gas = Number(form.gas_rate_mscfh) || 0

  // Total liquid rate (oil + water)
  calculatedMetrics.totalLiquidRate = (oil + water).toFixed(2)

  // Water cut percentage
  if (oil + water > 0) {
    calculatedMetrics.waterCut = ((water / (oil + water)) * 100).toFixed(2)
  } else {
    calculatedMetrics.waterCut = '0.00'
  }

  // Gas-Oil Ratio (GOR) - gas rate / oil rate * 1000 to convert MSCF to SCF
  if (oil > 0) {
    calculatedMetrics.gor = ((gas * 1000) / oil).toFixed(0)
  } else {
    calculatedMetrics.gor = '0'
  }
}

// Watch for changes in production rates to auto-calculate
watch([() => form.oil_rate_bph, () => form.water_rate_bph, () => form.gas_rate_mscfh], () => {
  calculateMetrics()
}, { deep: true })

/* -------------------- HELPERS -------------------- */
const sanitizeNumeric = (v) => (v === '' || v === null || v === undefined ? null : Number(v))

/* -------------------- SUBMIT -------------------- */
const onSubmit = async () => {
  if (!formRef.value) return
  formRef.value.validate(async (valid) => {
    if (!valid) {
      return ElMessage({ message: t('common.errors.validation_failed'), type: 'error' })
    }

    // Check if at least one production rate is provided
    const hasProductionData = form.oil_rate_bph > 0 || form.gas_rate_mscfh > 0 || form.water_rate_bph > 0
    if (!hasProductionData) {
      return ElMessage({ 
        message: t('production.wells.validation.required_production_data'), 
        type: 'error' 
      })
    }

    try {
      loading.value = true

      const payload = {
        vessel_id: form.vessel_id,
        well_id: form.well_id,
        production_date: form.production_date,
        shift: form.shift,

        oil_rate_bph: sanitizeNumeric(form.oil_rate_bph),
        gas_rate_mscfh: sanitizeNumeric(form.gas_rate_mscfh),
        water_rate_bph: sanitizeNumeric(form.water_rate_bph),

        wellhead_pressure_psi: sanitizeNumeric(form.wellhead_pressure_psi),
        wellhead_temp_f: sanitizeNumeric(form.wellhead_temp_f),
        separator_pressure_psi: sanitizeNumeric(form.separator_pressure_psi),
        choke_size: form.choke_size || null,

        api_gravity: sanitizeNumeric(form.api_gravity),
        bs_w_percent: sanitizeNumeric(form.bs_w_percent),

        remarks: form.remarks || null
      }

      // endpoint sesuai dengan struktur database baru
      const url = isEdit.value
        ? `/production/wells/${route.params.id}/update`
        : '/production/wells/store'
      const method = isEdit.value ? 'put' : 'post'

      const { status, data } = await axios({ method, url, data: payload })

      if (status === 200 || status === 201) {
        ElMessage({ message: t('common.messages.saved'), type: 'success' })
        const id = data?.id || data?.data?.id
        if (id) {
          router.replace({ path: `/production/wells/${id}` })
        } else {
          router.push({ name: 'production.wells.index' })
        }
      }
    } catch (error) {
      console.error('Submit error:', error)
      if (error?.response?.status === 422) {
        const errors = error.response.data.errors
        let msg = t('common.errors.validation_failed')
        if (errors) {
          const first = Object.values(errors)[0]
          if (first && first[0]) msg = first[0]
        }
        ElMessage({ message: msg, type: 'error' })
      } else if (error?.response?.status === 409) {
        ElMessage({ 
          message: t('production.wells.validation.duplicate_entry'), 
          type: 'error' 
        })
      } else {
        ElMessage({ message: t('common.errors.server_error'), type: 'error' })
      }
    } finally {
      loading.value = false
    }
  })
}

/* -------------------- LOAD DATA (edit mode) -------------------- */
const loadData = async () => {
  if (!isEdit.value) return
  try {
    loading.value = true
    const { data } = await axios.get(`/production/well-production/${route.params.id}`)
    const d = data?.data || data
    if (d) {
      Object.assign(form, {
        vessel_id: d.vessel_id ?? user.value?.vessel_id,
        well_id: d.well_id ?? null,
        production_date: d.production_date ?? dayjs().format('YYYY-MM-DD'),
        shift: d.shift ?? 'Day',

        oil_rate_bph: d.oil_rate_bph ?? null,
        gas_rate_mscfh: d.gas_rate_mscfh ?? null,
        water_rate_bph: d.water_rate_bph ?? null,

        wellhead_pressure_psi: d.wellhead_pressure_psi ?? null,
        wellhead_temp_f: d.wellhead_temp_f ?? null,
        separator_pressure_psi: d.separator_pressure_psi ?? null,
        choke_size: d.choke_size ?? null,

        api_gravity: d.api_gravity ?? null,
        bs_w_percent: d.bs_w_percent ?? null,

        remarks: d.remarks ?? ''
      })
      
      // Calculate derived metrics
      calculateMetrics()
    }
  } catch (e) {
    console.error('Load error:', e)
    ElMessage.error(t('common.errors.server_error'))
    onBack()
  } finally {
    loading.value = false
  }
}

/* -------------------- ACTIONS -------------------- */
const onBack = () => {
  router.push({ name: 'production.well-production.index' })
}

const onDuplicate = () => {
  const duplicateData = { ...form }
  duplicateData.production_date = dayjs().format('YYYY-MM-DD')
  duplicateData.remarks = `Copy of: ${form.remarks || 'Previous entry'}`
  
  router.push({ 
    name: 'production.well-production.create',
    query: { duplicate: JSON.stringify(duplicateData) }
  })
}

/* -------------------- LIFECYCLE -------------------- */
onMounted(() => {
  // Handle duplicate data from query
  if (route.query.duplicate) {
    try {
      const duplicateData = JSON.parse(route.query.duplicate)
      Object.assign(form, duplicateData)
      calculateMetrics()
    } catch (e) {
      console.error('Error parsing duplicate data:', e)
    }
  }
  
  loadData()
})
</script>

<style scoped>
/* Custom styling for calculated fields */
.el-input.is-disabled .el-input__inner {
  background-color: #f5f7fa;
  border-color: #e4e7ed;
  color: #606266;
}

/* Section headers */
h3 {
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .el-col {
    margin-bottom: 16px !important;
  }
}
</style>