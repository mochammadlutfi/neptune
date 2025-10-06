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
              {{ $t('production.fpu.sections.basic_info') }}
            </h3>
            <el-row :gutter="24">
              <!-- Reading Date -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.reading_date')" prop="reading_date">
                  <el-date-picker
                    v-model="form.reading_date"
                    type="date"
                    :placeholder="$t('production.fpu.placeholder.reading_date')"
                    format="DD-MM-YYYY"
                    value-format="YYYY-MM-DD"
                    style="width: 100%"
                    :disabled-date="disabledDate"
                    @change="() => formRef?.validateField('reading_date')"
                  />
                </el-form-item>
              </el-col>

              <!-- Reading Hour -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.reading_hour')" prop="reading_hour">
                  <el-select
                    v-model="form.reading_hour"
                    :placeholder="$t('production.fpu.placeholder.reading_hour')"
                    style="width: 100%"
                    @change="() => formRef?.validateField('reading_hour')"
                  >
                    <el-option
                      v-for="hour in hourOptions"
                      :key="hour.value"
                      :label="hour.label"
                      :value="hour.value"
                    />
                  </el-select>
                </el-form-item>
              </el-col>

              <!-- Operator -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.operator')" prop="recorded_by">
                  <el-input
                    :value="currentOperator"
                    readonly
                    :placeholder="$t('production.fpu.placeholder.operator')"
                  >
                    <template #prefix>
                      <Icon icon="heroicons:user" class="text-gray-600" :width="16" :height="16" />
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Process Parameters -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="mdi:gauge" class="mr-2 text-red-600" />
              {{ $t('production.fpu.sections.process_parameters') }}
            </h3>
            <el-row :gutter="24">
              <!-- Inlet Pressure -->
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.inlet_pressure_psi')" prop="inlet_pressure_psi">
                  <el-input
                    v-model.number="form.inlet_pressure_psi"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter inlet pressure"
                  >
                    <template #prefix>
                      <Icon icon="mdi:arrow-right" class="text-green-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PSI</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Inlet Temperature -->
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.inlet_temp_f')" prop="inlet_temp_f">
                  <el-input
                    v-model.number="form.inlet_temp_f"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter inlet temperature"
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

              <!-- Outlet Pressure -->
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.outlet_pressure_psi')" prop="outlet_pressure_psi">
                  <el-input
                    v-model.number="form.outlet_pressure_psi"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter outlet pressure"
                  >
                    <template #prefix>
                      <Icon icon="mdi:arrow-left" class="text-blue-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PSI</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Outlet Temperature -->
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.outlet_temp_f')" prop="outlet_temp_f">
                  <el-input
                    v-model.number="form.outlet_temp_f"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter outlet temperature"
                  >
                    <template #prefix>
                      <Icon icon="mdi:thermometer" class="text-purple-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">°F</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Gas Flow Rates -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="heroicons:chart-bar" class="mr-2 text-green-600" />
              {{ $t('production.fpu.sections.gas_flow_rates') }}
            </h3>
            <el-row :gutter="24">
              <!-- Total Gas Rate -->
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.total_gas_rate_mmscfd')" prop="total_gas_rate_mmscfd">
                  <el-input
                    v-model.number="form.total_gas_rate_mmscfd"
                    type="number"
                    :min="0"
                    :step="0.001"
                    @input="calculateMetrics"
                    @change="() => formRef?.validateField('total_gas_rate_mmscfd')"
                    placeholder="Enter total gas rate"
                  >
                    <template #prefix>
                      <Icon icon="heroicons:chart-bar" class="text-green-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Fuel Gas Rate -->
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.fuel_gas_rate_mmscfd')" prop="fuel_gas_rate_mmscfd">
                  <el-input
                    v-model.number="form.fuel_gas_rate_mmscfd"
                    type="number"
                    :min="0"
                    :step="0.001"
                    @input="calculateMetrics"
                    @change="() => formRef?.validateField('fuel_gas_rate_mmscfd')"
                    placeholder="Enter fuel gas rate"
                  >
                    <template #prefix>
                      <Icon icon="mdi:fire" class="text-red-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Flare HP Rate -->
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.flare_hp_rate_mmscfd')" prop="flare_hp_rate_mmscfd">
                  <el-input
                    v-model.number="form.flare_hp_rate_mmscfd"
                    type="number"
                    :min="0"
                    :step="0.001"
                    @input="calculateMetrics"
                    @change="() => formRef?.validateField('flare_hp_rate_mmscfd')"
                    placeholder="Enter HP flare rate"
                  >
                    <template #prefix>
                      <Icon icon="mdi:fire-alert" class="text-orange-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Flare LP Rate -->
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.flare_lp_rate_mmscfd')" prop="flare_lp_rate_mmscfd">
                  <el-input
                    v-model.number="form.flare_lp_rate_mmscfd"
                    type="number"
                    :min="0"
                    :step="0.001"
                    @input="calculateMetrics"
                    @change="() => formRef?.validateField('flare_lp_rate_mmscfd')"
                    placeholder="Enter LP flare rate"
                  >
                    <template #prefix>
                      <Icon icon="mdi:fire" class="text-yellow-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>

            <!-- Calculated Metrics (Read-only) -->
            <el-row :gutter="24" v-if="showCalculatedMetrics">
              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.total_flare_rate')">
                  <el-input
                    :value="calculatedMetrics.totalFlareRate"
                    readonly
                    placeholder="Auto-calculated"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.export_gas_rate')">
                  <el-input
                    :value="calculatedMetrics.exportGasRate"
                    readonly
                    placeholder="Auto-calculated"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.flare_percentage')">
                  <el-input
                    :value="calculatedMetrics.flarePercentage"
                    readonly
                    placeholder="Auto-calculated"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">%</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.gas_balance')">
                  <el-input
                    :value="calculatedMetrics.gasBalance"
                    readonly
                    placeholder="Auto-calculated"
                    :class="{'border-red-500': Math.abs(parseFloat(calculatedMetrics.gasBalance)) > 0.1}"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- TEG System -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="mdi:water" class="mr-2 text-cyan-600" />
              {{ $t('production.fpu.sections.teg_system') }}
            </h3>
            <el-row :gutter="24">
              <!-- Contactor Pressure -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.contactor_pressure_psi')" prop="contactor_pressure_psi">
                  <el-input
                    v-model.number="form.contactor_pressure_psi"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter contactor pressure"
                  >
                    <template #prefix>
                      <Icon icon="mdi:gauge" class="text-cyan-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PSI</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Reboiler Temperature -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.reboiler_temp_f')" prop="reboiler_temp_f">
                  <el-input
                    v-model.number="form.reboiler_temp_f"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter reboiler temperature"
                  >
                    <template #prefix>
                      <Icon icon="mdi:thermometer" class="text-red-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">°F</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Circulation Rate -->
              <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.circulation_rate_gph')" prop="circulation_rate_gph">
                  <el-input
                    v-model.number="form.circulation_rate_gph"
                    type="number"
                    :min="0"
                    :step="0.1"
                    placeholder="Enter circulation rate"
                  >
                    <template #prefix>
                      <Icon icon="mdi:rotate-3d-variant" class="text-blue-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">GPH</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Compression System -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
              <Icon icon="mdi:turbine" class="mr-2 text-indigo-600" />
              {{ $t('production.fpu.sections.compression_system') }}
            </h3>
            <el-row :gutter="24">
              <!-- GTC A Seal Gas -->
              <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.gtc_a_seal_gas')" prop="gtc_a_seal_gas">
                  <el-input
                    v-model.number="form.gtc_a_seal_gas"
                    type="number"
                    :min="0"
                    :step="0.001"
                    placeholder="Enter GTC A seal gas"
                  >
                    <template #prefix>
                      <Icon icon="mdi:turbine" class="text-indigo-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- GTC B Seal Gas -->
              <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                <el-form-item :label="$t('production.fpu.fields.gtc_b_seal_gas')" prop="gtc_b_seal_gas">
                  <el-input
                    v-model.number="form.gtc_b_seal_gas"
                    type="number"
                    :min="0"
                    :step="0.001"
                    placeholder="Enter GTC B seal gas"
                  >
                    <template #prefix>
                      <Icon icon="mdi:turbine" class="text-purple-600" :width="16" :height="16" />
                    </template>
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
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
              {{ $t('production.fpu.sections.additional_info') }}
            </h3>
            <!-- Remarks -->
            <el-col :span="24" class="mb-2">
              <el-form-item :label="$t('production.fpu.fields.remarks')" prop="remarks">
                <el-input
                  v-model="form.remarks"
                  type="textarea"
                  :rows="3"
                  :placeholder="$t('production.fpu.placeholder.remarks')"
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
              <el-button @click="onCopyPreviousReading" type="success" plain>
                <Icon icon="mingcute:history-line" class="mr-2" />
                {{ $t('production.fpu.actions.copy_previous') }}
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
import { useUser } from '@/composables/auth/useUser'
import dayjs from 'dayjs';

const user = useUser()
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const title = ref(route.meta.title ?? 'production.fpu.create')
const formRef = ref(null)
const loading = ref(false)

// edit mode
const isEdit = computed(() => !!route.params.id)

// Current operator
const currentOperator = computed(() => user.value?.name || 'Unknown')

// Hour options for 2-hourly readings
const hourOptions = [
  { value: '00:00:00', label: '00:00' },
  { value: '02:00:00', label: '02:00' },
  { value: '04:00:00', label: '04:00' },
  { value: '06:00:00', label: '06:00' },
  { value: '08:00:00', label: '08:00' },
  { value: '10:00:00', label: '10:00' },
  { value: '12:00:00', label: '12:00' },
  { value: '14:00:00', label: '14:00' },
  { value: '16:00:00', label: '16:00' },
  { value: '18:00:00', label: '18:00' },
  { value: '20:00:00', label: '20:00' },
  { value: '22:00:00', label: '22:00' }
]

/**
 * State form - Based on FPU Operations database schema
 */
const form = reactive({
  vessel_id: user.value?.vessel_id || null, // Auto-filled from user
  reading_date: dayjs().format('YYYY-MM-DD'),
  reading_hour: getCurrentHour(),

  // Process parameters
  inlet_pressure_psi: null,
  inlet_temp_f: null,
  outlet_pressure_psi: null,
  outlet_temp_f: null,

  // Gas flow rates
  total_gas_rate_mmscfd: null,
  fuel_gas_rate_mmscfd: null,
  flare_hp_rate_mmscfd: null,
  flare_lp_rate_mmscfd: null,

  // TEG System
  contactor_pressure_psi: null,
  reboiler_temp_f: null,
  circulation_rate_gph: null,

  // Compression System  
  gtc_a_seal_gas: null,
  gtc_b_seal_gas: null,

  remarks: ''
})

// Calculated metrics (derived values)
const calculatedMetrics = reactive({
  totalFlareRate: 0,
  exportGasRate: 0,
  flarePercentage: 0,
  gasBalance: 0
})

// Show calculated metrics only when we have gas flow data
const showCalculatedMetrics = computed(() => {
  return form.total_gas_rate_mmscfd > 0 || form.fuel_gas_rate_mmscfd > 0 || 
         form.flare_hp_rate_mmscfd > 0 || form.flare_lp_rate_mmscfd > 0
})

/* -------------------- HELPERS -------------------- */
// Get current hour rounded to nearest 2-hour interval
function getCurrentHour() {
  const now = new Date()
  const currentHour = now.getHours()
  const roundedHour = Math.floor(currentHour / 2) * 2
  return `${roundedHour.toString().padStart(2, '0')}:00:00`
}

/* -------------------- VALIDATION HELPERS -------------------- */
const now = () => new Date()

// Disable future dates
const disabledDate = (date) => {
  return date.getTime() > now().getTime()
}

const requiredMsg = (attr) => t('common.validation.required', { attribute: attr })

// Generic numeric range validator
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

// Date validator
const notFutureDate = {
  validator: (_rule, value, callback) => {
    if (!value) return callback(new Error(requiredMsg(t('production.fpu.fields.reading_date'))))
    
    const readingDate = dayjs(value)
    if (!readingDate.isValid()) {
      return callback(new Error(t('common.validation.date', { attribute: t('production.fpu.fields.reading_date') })))
    }

    if (readingDate.isAfter(dayjs(), 'day')) {
      return callback(new Error(t('common.validation.before_or_equal', { 
        attribute: t('production.fpu.fields.reading_date'), 
        date: t('common.words.today') 
      })))
    }

    callback()
  },
  trigger: ['change', 'blur']
}

/* -------------------- VALIDATION RULES -------------------- */
const formRules = ref({
  reading_date: [ notFutureDate ],
  reading_hour: [
    { required: true, message: requiredMsg(t('production.fpu.fields.reading_hour')), trigger: 'change' }
  ],
  inlet_pressure_psi: [
    numberRange(0, undefined, t('production.fpu.fields.inlet_pressure_psi'), { allowNull: true })
  ],
  inlet_temp_f: [
    numberRange(0, undefined, t('production.fpu.fields.inlet_temp_f'), { allowNull: true })
  ],
  outlet_pressure_psi: [
    numberRange(0, undefined, t('production.fpu.fields.outlet_pressure_psi'), { allowNull: true })
  ],
  outlet_temp_f: [
    numberRange(0, undefined, t('production.fpu.fields.outlet_temp_f'), { allowNull: true })
  ],
  total_gas_rate_mmscfd: [
    numberRange(0, undefined, t('production.fpu.fields.total_gas_rate_mmscfd'), { allowNull: true })
  ],
  fuel_gas_rate_mmscfd: [
    numberRange(0, undefined, t('production.fpu.fields.fuel_gas_rate_mmscfd'), { allowNull: true })
  ],
  flare_hp_rate_mmscfd: [
    numberRange(0, undefined, t('production.fpu.fields.flare_hp_rate_mmscfd'), { allowNull: true })
  ],
  flare_lp_rate_mmscfd: [
    numberRange(0, undefined, t('production.fpu.fields.flare_lp_rate_mmscfd'), { allowNull: true })
  ],
  contactor_pressure_psi: [
    numberRange(0, undefined, t('production.fpu.fields.contactor_pressure_psi'), { allowNull: true })
  ],
  reboiler_temp_f: [
    numberRange(0, undefined, t('production.fpu.fields.reboiler_temp_f'), { allowNull: true })
  ],
  circulation_rate_gph: [
    numberRange(0, undefined, t('production.fpu.fields.circulation_rate_gph'), { allowNull: true })
  ],
  gtc_a_seal_gas: [
    numberRange(0, undefined, t('production.fpu.fields.gtc_a_seal_gas'), { allowNull: true })
  ],
  gtc_b_seal_gas: [
    numberRange(0, undefined, t('production.fpu.fields.gtc_b_seal_gas'), { allowNull: true })
  ],
  remarks: [
    { min: 0, max: 500, message: t('common.validation.max.string', { attribute: t('common.fields.remarks'), max: 500 }), trigger: 'blur' }
  ]
})

/* -------------------- CALCULATIONS -------------------- */
const calculateMetrics = () => {
  const totalGas = Number(form.total_gas_rate_mmscfd) || 0
  const fuelGas = Number(form.fuel_gas_rate_mmscfd) || 0
  const flareHP = Number(form.flare_hp_rate_mmscfd) || 0
  const flareLP = Number(form.flare_lp_rate_mmscfd) || 0

  // Total flare rate (HP + LP)
  calculatedMetrics.totalFlareRate = (flareHP + flareLP).toFixed(4)

  // Export gas rate (Total - Fuel - Flare)
  calculatedMetrics.exportGasRate = (totalGas - fuelGas - flareHP - flareLP).toFixed(4)

  // Flare percentage of total production
  if (totalGas > 0) {
    calculatedMetrics.flarePercentage = (((flareHP + flareLP) / totalGas) * 100).toFixed(2)
  } else {
    calculatedMetrics.flarePercentage = '0.00'
  }

  // Gas balance check (should be close to 0)
  const balance = totalGas - fuelGas - flareHP - flareLP - parseFloat(calculatedMetrics.exportGasRate)
  calculatedMetrics.gasBalance = balance.toFixed(4)
}

// Watch for changes in gas rates to auto-calculate
watch([
  () => form.total_gas_rate_mmscfd, 
  () => form.fuel_gas_rate_mmscfd, 
  () => form.flare_hp_rate_mmscfd, 
  () => form.flare_lp_rate_mmscfd
], () => {
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

    try {
      loading.value = true

      // Build process_data JSON from individual fields
      const processData = {}
      
      // TEG System data
      if (form.contactor_pressure_psi || form.reboiler_temp_f || form.circulation_rate_gph) {
        processData.teg_system = {}
        if (form.contactor_pressure_psi) processData.teg_system.contactor_pressure = form.contactor_pressure_psi
        if (form.reboiler_temp_f) processData.teg_system.reboiler_temp = form.reboiler_temp_f
        if (form.circulation_rate_gph) processData.teg_system.circulation_rate = form.circulation_rate_gph
      }
      
      // Compression system data
      if (form.gtc_a_seal_gas || form.gtc_b_seal_gas) {
        processData.compression = {}
        if (form.gtc_a_seal_gas) processData.compression.gtc_a_seal_gas = form.gtc_a_seal_gas
        if (form.gtc_b_seal_gas) processData.compression.gtc_b_seal_gas = form.gtc_b_seal_gas
      }

      const payload = {
        vessel_id: form.vessel_id,
        reading_date: form.reading_date,
        reading_hour: form.reading_hour,

        inlet_pressure_psi: sanitizeNumeric(form.inlet_pressure_psi),
        inlet_temp_f: sanitizeNumeric(form.inlet_temp_f),
        outlet_pressure_psi: sanitizeNumeric(form.outlet_pressure_psi),
        outlet_temp_f: sanitizeNumeric(form.outlet_temp_f),

        total_gas_rate_mmscfd: sanitizeNumeric(form.total_gas_rate_mmscfd),
        fuel_gas_rate_mmscfd: sanitizeNumeric(form.fuel_gas_rate_mmscfd),
        flare_hp_rate_mmscfd: sanitizeNumeric(form.flare_hp_rate_mmscfd),
        flare_lp_rate_mmscfd: sanitizeNumeric(form.flare_lp_rate_mmscfd),

        process_data: Object.keys(processData).length > 0 ? processData : null,

        remarks: form.remarks || null
      }

      // endpoint sesuai dengan struktur database
      const url = isEdit.value
        ? `/production/fpu-operations/${route.params.id}`
        : '/production/fpu-operations'
      const method = isEdit.value ? 'put' : 'post'

      const { status, data } = await axios({ method, url, data: payload })

      if (status === 200 || status === 201) {
        ElMessage({ message: t('common.messages.saved'), type: 'success' })
        const id = data?.id || data?.data?.id
        if (id) {
          router.replace({ path: `/production/fpu-operations/${id}` })
        } else {
          router.push({ name: 'production.fpu-operations.index' })
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
          message: t('production.fpu.errors.duplicate_reading'), 
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
    const { data } = await axios.get(`/production/fpu-operations/${route.params.id}`)
    const d = data?.data || data
    if (d) {
      Object.assign(form, {
        vessel_id: d.vessel_id ?? user.value?.vessel_id,
        reading_date: d.reading_date ?? dayjs().format('YYYY-MM-DD'),
        reading_hour: d.reading_hour ?? getCurrentHour(),

        inlet_pressure_psi: d.inlet_pressure_psi ?? null,
        inlet_temp_f: d.inlet_temp_f ?? null,
        outlet_pressure_psi: d.outlet_pressure_psi ?? null,
        outlet_temp_f: d.outlet_temp_f ?? null,

        total_gas_rate_mmscfd: d.total_gas_rate_mmscfd ?? null,
        fuel_gas_rate_mmscfd: d.fuel_gas_rate_mmscfd ?? null,
        flare_hp_rate_mmscfd: d.flare_hp_rate_mmscfd ?? null,
        flare_lp_rate_mmscfd: d.flare_lp_rate_mmscfd ?? null,

        // Extract from process_data JSON
        contactor_pressure_psi: d.process_data?.teg_system?.contactor_pressure ?? null,
        reboiler_temp_f: d.process_data?.teg_system?.reboiler_temp ?? null,
        circulation_rate_gph: d.process_data?.teg_system?.circulation_rate ?? null,

        gtc_a_seal_gas: d.process_data?.compression?.gtc_a_seal_gas ?? null,
        gtc_b_seal_gas: d.process_data?.compression?.gtc_b_seal_gas ?? null,

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
  router.push({ name: 'production.fpu-operations.index' })
}

const onDuplicate = () => {
  const duplicateData = { ...form }
  duplicateData.reading_date = dayjs().format('YYYY-MM-DD')
  duplicateData.reading_hour = getCurrentHour()
  duplicateData.remarks = `Copy of: ${form.remarks || 'Previous reading'}`
  
  router.push({ 
    name: 'production.fpu-operations.create',
    query: { duplicate: JSON.stringify(duplicateData) }
  })
}

const onCopyPreviousReading = async () => {
  try {
    loading.value = true
    // Get previous reading for the same time
    const { data } = await axios.get('/production/fpu-operations/latest', {
      params: { 
        vessel_id: form.vessel_id,
        reading_hour: form.reading_hour
      }
    })
    
    if (data?.data) {
      const prev = data.data
      // Copy all process parameters but keep current date/hour
      Object.assign(form, {
        inlet_pressure_psi: prev.inlet_pressure_psi,
        inlet_temp_f: prev.inlet_temp_f,
        outlet_pressure_psi: prev.outlet_pressure_psi,
        outlet_temp_f: prev.outlet_temp_f,
        
        contactor_pressure_psi: prev.process_data?.teg_system?.contactor_pressure,
        reboiler_temp_f: prev.process_data?.teg_system?.reboiler_temp,
        circulation_rate_gph: prev.process_data?.teg_system?.circulation_rate,
        
        gtc_a_seal_gas: prev.process_data?.compression?.gtc_a_seal_gas,
        gtc_b_seal_gas: prev.process_data?.compression?.gtc_b_seal_gas
      })
      
      ElMessage({ message: t('production.fpu.messages.copied_previous'), type: 'success' })
    } else {
      ElMessage({ message: t('production.fpu.messages.no_previous_data'), type: 'info' })
    }
  } catch (e) {
    console.error('Copy previous error:', e)
    ElMessage.error(t('common.errors.server_error'))
  } finally {
    loading.value = false
  }
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

/* Gas balance warning */
.border-red-500 {
  border-color: #ef4444 !important;
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