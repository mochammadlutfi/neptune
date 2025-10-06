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
          <!-- Section 1: General Information -->
          <div class="mb-8">
            <div class="flex items-center mb-4">
              <Icon icon="mdi:information-outline" class="text-blue-600 mr-2" :width="20" :height="20" />
              <h3 class="text-lg font-semibold text-gray-800">{{ $t('production.sales_gas.sections.general_info') }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-6">
              <!-- Gas Buyer -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.buyer_id')" prop="buyer_id">
                  <SelectGasBuyer v-model="form.buyer_id"/>
                </el-form-item>
              </div>

              <!-- Sales Date -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.sales_date')" prop="sales_date">
                  <el-date-picker
                    v-model="form.sales_date"
                    type="date"
                    :placeholder="$t('production.sales_gas.placeholder.sales_date')"
                    format="DD-MM-YYYY"
                    value-format="YYYY-MM-DD"
                    style="width: 100%"
                    :disabled-date="disabledDate"
                    @change="() => formRef?.validateField('sales_date')"
                  />
                </el-form-item>
              </div>
            </div>
          </div>

          <!-- Section 2: Export Conditions -->
          <div class="mb-8">
            <div class="flex items-center mb-4">
              <Icon icon="mdi:export" class="text-green-600 mr-2" :width="20" :height="20" />
              <h3 class="text-lg font-semibold text-gray-800">{{ $t('production.sales_gas.sections.export_conditions') }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-6">
              <!-- Export Pressure -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.export_pressure_psi')" prop="export_pressure_psi">
                  <el-input
                    v-model.number="form.export_pressure_psi"
                    type="number"
                    :placeholder="$t('production.sales_gas.placeholder.export_pressure_psi')"
                    :min="0"
                    :step="0.01"
                    @change="() => formRef?.validateField('export_pressure_psi')"
                  >
                    <template #prefix>
                      <Icon icon="mdi:gauge" class="text-red-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PSI</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>

              <!-- Export Temperature -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.export_temp_f')" prop="export_temp_f">
                  <el-input
                    v-model.number="form.export_temp_f"
                    type="number"
                    :placeholder="$t('production.sales_gas.placeholder.export_temp_f')"
                    :step="0.01"
                    @change="() => formRef?.validateField('export_temp_f')"
                  >
                    <template #prefix>
                      <Icon icon="mdi:thermometer" class="text-orange-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">°F</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>
            </div>
          </div>

          <!-- Section 3: Volume & Delivery -->
          <div class="mb-8">
            <div class="flex items-center mb-4">
              <Icon icon="mdi:chart-line" class="text-purple-600 mr-2" :width="20" :height="20" />
              <h3 class="text-lg font-semibold text-gray-800">{{ $t('production.sales_gas.sections.volume_delivery') }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
              <!-- Nomination -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.nomination_mmscf')" prop="nomination_mmscf">
                  <el-input
                    v-model.number="form.nomination_mmscf"
                    type="number"
                    :min="0"
                    :step="0.001"
                    :placeholder="$t('production.sales_gas.placeholder.nomination_mmscf')"
                    @input="calculateVariance"
                  >
                    <template #prefix>
                      <Icon icon="mdi:target" class="text-green-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCF</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>

              <!-- Actual Delivery -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.actual_delivery_mmscf')" prop="actual_delivery_mmscf">
                  <el-input
                    v-model.number="form.actual_delivery_mmscf"
                    type="number"
                    :min="0"
                    :step="0.001"
                    :placeholder="$t('production.sales_gas.placeholder.actual_delivery_mmscf')"
                    @input="calculateVariance"
                  >
                    <template #prefix>
                      <Icon icon="mdi:truck-delivery" class="text-orange-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCF</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>

              <!-- Variance (Auto-calculated) -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.variance_percent')" prop="variance_percent">
                  <el-input
                    v-model.number="form.variance_percent"
                    type="number"
                    :step="0.01"
                    :placeholder="$t('production.sales_gas.placeholder.variance_percent')"
                    readonly
                  >
                    <template #prefix>
                      <Icon icon="mdi:percent" 
                            :class="getVarianceColor()" 
                            :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">%</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>
            </div>
          </div>

          <!-- Section 4: Gas Quality Parameters -->
          <div class="mb-8">
            <div class="flex items-center mb-4">
              <Icon icon="mdi:flask-outline" class="text-indigo-600 mr-2" :width="20" :height="20" />
              <h3 class="text-lg font-semibold text-gray-800">{{ $t('production.sales_gas.sections.quality_parameters') }}</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
              <!-- Heating Value -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.heating_value_btu')" prop="heating_value_btu">
                  <el-input
                    v-model.number="form.heating_value_btu"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.sales_gas.placeholder.heating_value_btu')"
                  >
                    <template #prefix>
                      <Icon icon="mdi:fire" class="text-red-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">BTU</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>

              <!-- Specific Gravity -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.specific_gravity')" prop="specific_gravity">
                  <el-input
                    v-model.number="form.specific_gravity"
                    type="number"
                    :min="0"
                    :step="0.0001"
                    :placeholder="$t('production.sales_gas.placeholder.specific_gravity')"
                  >
                    <template #prefix>
                      <Icon icon="mdi:scale-balance" class="text-purple-600" :width="16" :height="16" />
                    </template>
                  </el-input>
                </el-form-item>
              </div>

              <!-- H2S Content -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.h2s_content_ppm')" prop="h2s_content_ppm">
                  <el-input
                    v-model.number="form.h2s_content_ppm"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.sales_gas.placeholder.h2s_content_ppm')"
                  >
                    <template #prefix>
                      <Icon icon="mdi:chemical-weapon" class="text-yellow-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PPM</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>

              <!-- CO2 Content -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.co2_content_pct')" prop="co2_content_pct">
                  <el-input
                    v-model.number="form.co2_content_pct"
                    type="number"
                    :min="0"
                    :max="100"
                    :step="0.01"
                    :placeholder="$t('production.sales_gas.placeholder.co2_content_pct')"
                  >
                    <template #prefix>
                      <Icon icon="mdi:molecule-co2" class="text-gray-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">%</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>

              <!-- Moisture Content -->
              <div class="mb-4">
                <el-form-item :label="$t('production.sales_gas.fields.moisture_content_ppm')" prop="moisture_content_ppm">
                  <el-input
                    v-model.number="form.moisture_content_ppm"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.sales_gas.placeholder.moisture_content_ppm')"
                  >
                    <template #prefix>
                      <Icon icon="mdi:water-percent" class="text-blue-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PPM</span>
                    </template>
                  </el-input>
                </el-form-item>
              </div>
            </div>
          </div>

          <!-- Section 5: Additional Information -->
          <div class="mb-8">
            <div class="flex items-center mb-4">
              <Icon icon="mdi:note-text-outline" class="text-gray-600 mr-2" :width="20" :height="20" />
              <h3 class="text-lg font-semibold text-gray-800">{{ $t('production.sales_gas.sections.additional_info') }}</h3>
            </div>
            <div class="grid grid-cols-1 gap-6">
              <!-- Remarks -->
              <div class="mb-2">
                <el-form-item :label="$t('production.sales_gas.fields.remarks')" prop="remarks">
                  <el-input
                    v-model="form.remarks"
                    type="textarea"
                    :rows="3"
                    :placeholder="$t('production.sales_gas.placeholder.remarks')"
                    maxlength="1000"
                    show-word-limit
                  />
                </el-form-item>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="p-6 border-t bg-gray-50">
          <div class="flex justify-end space-x-3">
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
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="js">
import axios from 'axios'
import { Icon } from '@iconify/vue'
import { ref, computed, onMounted, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

import PageHeader from '@/components/PageHeader.vue'
import SelectGasBuyer from '@/components/select/SelectGasBuyer.vue'
import { useUser } from '@/composables/auth/useUser'
import dayjs from 'dayjs';

// Register components
const components = {
  PageHeader,
  SelectGasBuyer
}

const user = useUser()
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const title = ref(route.meta.title ?? 'production.sales_gas.create')
const formRef = ref(null)
const loading = ref(false)

// edit mode? (opsional jika nanti ada update reading)
const isEdit = computed(() => !!route.params.id)

/**
 * State form untuk Gas Sales
 * Sesuai dengan struktur tabel gas_sales (clean database structure)
 */
const form = reactive({
  buyer_id: null, // bigint NOT NULL - Gas buyer reference
  sales_date: null, // date NOT NULL
  export_pressure_psi: null, // decimal(8,2) NULL
  export_temp_f: null, // decimal(6,2) NULL
  actual_delivery_mmscf: null, // decimal(12,3) NULL
  nomination_mmscf: null, // decimal(12,3) NULL
  variance_percent: null, // decimal(5,2) NULL - auto calculated
  heating_value_btu: null, // decimal(8,2) NULL
  specific_gravity: null, // decimal(6,4) NULL
  h2s_content_ppm: null, // decimal(6,2) NULL
  co2_content_pct: null, // decimal(5,2) NULL
  moisture_content_ppm: null, // decimal(8,2) NULL
  recorded_by: user.value?.id || null, // bigint - auto set user ID
  remarks: '' // text NULL
})

/* -------------------- VALIDATION HELPERS -------------------- */
const now = () => new Date()

// Disable tanggal masa depan di date-picker (toleransi 1 hari)
const disabledDate = (date) => {
  return date.getTime() > now().getTime() + 24 * 60 * 60 * 1000
}

const requiredMsg = (attr) => t('common.validation.required', { attribute: attr })

// Generic numeric range validator dengan decimal precision
const numberRange = (min, max, attr, opts = {}) => ({
  validator: (_rule, value, callback) => {
    const allowNull = !!opts.allowNull
    if (allowNull && (value === null || value === undefined || value === '')) return callback()
    if (value === null || value === undefined || value === '') {
      return callback(new Error(requiredMsg(attr)))
    }
    if (typeof value !== 'number' || Number.isNaN(value)) {
      return callback(new Error(t('common.validation.numeric', { attribute: attr })))
    }
    if (min !== undefined && value < min) {
      return callback(new Error(t('common.validation.min.numeric', { attribute: attr, min })))
    }
    if (max !== undefined && value > max) {
      return callback(new Error(t('common.validation.max.numeric', { attribute: attr, max })))
    }
    // Validasi decimal precision sesuai database
    if (opts.precision) {
      const decimalPlaces = (value.toString().split('.')[1] || '').length
      if (decimalPlaces > opts.precision) {
        return callback(new Error(t('common.validation.decimal_places', { attribute: attr, max: opts.precision })))
      }
    }
    callback()
  },
  trigger: opts.trigger || ['change', 'blur']
})

/* -------------------- RULES -------------------- */
const formRules = ref({
  buyer_id: [
    { required: true, message: requiredMsg(t('production.sales_gas.fields.buyer_id')), trigger: 'change' }
  ],
  sales_date: [
    { required: true, message: requiredMsg(t('production.sales_gas.fields.sales_date')), trigger: 'change' }
  ],
  actual_delivery_mmscf: [
    { required: true, message: requiredMsg(t('production.sales_gas.fields.actual_delivery_mmscf')), trigger: 'blur' },
    numberRange(0, 999999999.999, t('production.sales_gas.fields.actual_delivery_mmscf'), { precision: 3 })
  ],

  // Optional fields with proper validation ranges
  export_pressure_psi: [
    numberRange(0, 999999.99, t('production.sales_gas.fields.export_pressure_psi'), { allowNull: true, precision: 2 })
  ],
  export_temp_f: [
    numberRange(-459.67, 9999.99, t('production.sales_gas.fields.export_temp_f'), { allowNull: true, precision: 2 })
  ],
  nomination_mmscf: [
    numberRange(0, 999999999.999, t('production.sales_gas.fields.nomination_mmscf'), { allowNull: true, precision: 3 })
  ],
  heating_value_btu: [
    numberRange(0, 999999.99, t('production.sales_gas.fields.heating_value_btu'), { allowNull: true, precision: 2 })
  ],
  specific_gravity: [
    numberRange(0, 99.9999, t('production.sales_gas.fields.specific_gravity'), { allowNull: true, precision: 4 })
  ],
  h2s_content_ppm: [
    numberRange(0, 9999.99, t('production.sales_gas.fields.h2s_content_ppm'), { allowNull: true, precision: 2 })
  ],
  co2_content_pct: [
    numberRange(0, 999.99, t('production.sales_gas.fields.co2_content_pct'), { allowNull: true, precision: 2 })
  ],
  moisture_content_ppm: [
    numberRange(0, 99999999.99, t('production.sales_gas.fields.moisture_content_ppm'), { allowNull: true, precision: 2 })
  ],
  variance_percent: [
    numberRange(-999.99, 999.99, t('production.sales_gas.fields.variance_percent'), { allowNull: true, precision: 2 })
  ],
  remarks: [
    { max: 1000, message: t('common.validation.max', { attribute: t('production.sales_gas.fields.remarks'), max: 1000 }), trigger: 'blur' }
  ]
})

/* -------------------- HELPERS -------------------- */
const sanitizeNumeric = (v) => (v === '' || v === null || v === undefined ? null : v)

// Hitung variance berdasarkan nomination dan actual delivery
const calculateVariance = () => {
  if (form.nomination_mmscf && form.actual_delivery_mmscf) {
    const variance = ((form.actual_delivery_mmscf - form.nomination_mmscf) / form.nomination_mmscf) * 100
    form.variance_percent = Math.round(variance * 100) / 100 // round to 2 decimal places
  } else {
    form.variance_percent = null
  }
}

// Get variance color based on percentage
const getVarianceColor = () => {
  if (form.variance_percent === null || form.variance_percent === undefined) return 'text-gray-500'
  if (Math.abs(form.variance_percent) <= 5) return 'text-green-600'
  if (Math.abs(form.variance_percent) <= 10) return 'text-yellow-600'
  return 'text-red-600'
}

/* -------------------- SUBMIT -------------------- */
const onSubmit = async () => {
  if (!formRef.value) return
  formRef.value.validate(async (valid) => {
    if (!valid) {
      return ElMessage({ message: t('common.errors.validation_failed'), type: 'error' })
    }
    try {
      loading.value = true

      const payload = {
        buyer_id: form.buyer_id,
        sales_date: form.sales_date,
        export_pressure_psi: sanitizeNumeric(form.export_pressure_psi),
        export_temp_f: sanitizeNumeric(form.export_temp_f),
        actual_delivery_mmscf: sanitizeNumeric(form.actual_delivery_mmscf),
        nomination_mmscf: sanitizeNumeric(form.nomination_mmscf),
        heating_value_btu: sanitizeNumeric(form.heating_value_btu),
        specific_gravity: sanitizeNumeric(form.specific_gravity),
        h2s_content_ppm: sanitizeNumeric(form.h2s_content_ppm),
        co2_content_pct: sanitizeNumeric(form.co2_content_pct),
        moisture_content_ppm: sanitizeNumeric(form.moisture_content_ppm),
        recorded_by: form.recorded_by || user.value?.id,
        remarks: form.remarks ?? ''
      }

      // endpoint sesuai dengan rute backend
      const url = isEdit.value
        ? `/production/sales-gas/${route.params.id}`
        : '/production/sales-gas'
      const method = isEdit.value ? 'put' : 'post'

      const { status, data } = await axios({ method, url, data: payload })

      if (status === 200 || status === 201) {
        ElMessage({ message: t('common.messages.saved'), type: 'success' })
        const id = data?.id || data?.data?.id
        if (id) {
          router.replace({ path: `/production/sales-gas/${id}` })
        } else {
          router.push({ name: 'production.sales_gas.index' })
        }
      }
    } catch (error) {
      if (error?.response?.status === 422) {
        const errors = error.response.data.errors
        let msg = t('common.errors.validation_failed')
        if (errors) {
          const first = Object.values(errors)[0]
          if (first && first[0]) msg = first[0]
        }
        ElMessage({ message: msg, type: 'error' })
      } else {
        ElMessage({ message: t('common.errors.server_error'), type: 'error' })
      }
    } finally {
      loading.value = false
    }
  })
}

/* -------------------- LOAD (edit mode) -------------------- */
const loadData = async () => {
  if (!isEdit.value) return
  try {
    loading.value = true
    const { data } = await axios.get(`/production/sales-gas/${route.params.id}`)
    const d = data?.data || data
    if (d) {
      form.buyer_id = d.buyer_id ?? null
      form.sales_date = d.sales_date
      form.export_pressure_psi = d.export_pressure_psi ?? null
      form.export_temp_f = d.export_temp_f ?? null
      form.actual_delivery_mmscf = d.actual_delivery_mmscf ?? null
      form.nomination_mmscf = d.nomination_mmscf ?? null
      form.variance_percent = d.variance_percent ?? null
      form.heating_value_btu = d.heating_value_btu ?? null
      form.specific_gravity = d.specific_gravity ?? null
      form.h2s_content_ppm = d.h2s_content_ppm ?? null
      form.co2_content_pct = d.co2_content_pct ?? null
      form.moisture_content_ppm = d.moisture_content_ppm ?? null
      form.recorded_by = d.recorded_by ?? user.value?.id
      form.remarks = d.remarks ?? ''
    }
  } catch (e) {
    console.error(e)
    ElMessage.error(t('common.errors.server_error'))
    onBack()
  } finally {
    loading.value = false
  }
}

const onBack = () => {
  router.push({ name: 'production.sales_gas.index' })
}

onMounted(() => {
  loadData()
})
</script>