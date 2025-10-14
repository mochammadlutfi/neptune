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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <el-form-item :label="$t('production.sales_gas_metering.fields.vessel_id')" prop="vessel_id">
                <select-vessel
                  v-model="form.vessel_id"
                  :vessel-id="form.vessel_id"
                  :placeholder="$t('production.sales_gas_metering.placeholder.vessel_id')"
                  @change="loadGasBuyer"
                  disabled
                />
              </el-form-item>

              <el-form-item :label="$t('production.sales_gas_metering.fields.date')" prop="date">
                <el-date-picker
                  v-model="form.date"
                  type="date"
                  :placeholder="$t('production.sales_gas_metering.placeholder.date')"
                  format="DD-MM-YYYY"
                  value-format="YYYY-MM-DD"
                  style="width: 100%"
                  :disabled-date="disabledDate"
                  @change="() => formRef?.validateField('date')"
                />
              </el-form-item>

              <el-form-item :label="$t('production.sales_gas_metering.fields.time')" prop="time">
                <el-time-picker
                  v-model="form.time"
                  :placeholder="$t('production.sales_gas_metering.placeholder.time')"
                  format="HH:00"
                  value-format="HH:00:00"
                  style="width: 100%"
                  @change="() => formRef?.validateField('time')"
                />
              </el-form-item>
            </div>
          </div>

          <!-- Gas Quality Readings -->
          <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <el-form-item :label="$t('production.sales_gas_metering.fields.pressure_psig')" prop="pressure_psig">
                <el-input
                  v-model.number="form.pressure_psig"
                  type="number"
                  :min="0"
                  :step="0.01"
                  :placeholder="$t('production.sales_gas_metering.placeholder.pressure_psig')"
                >
                  <template #suffix>
                    <span class="text-gray-500 text-sm">psig</span>
                  </template>
                </el-input>
              </el-form-item>

              <el-form-item :label="$t('production.sales_gas_metering.fields.temperature_f')" prop="temperature_f">
                <el-input
                  v-model.number="form.temperature_f"
                  type="number"
                  :min="0"
                  :step="0.01"
                  :placeholder="$t('production.sales_gas_metering.placeholder.temperature_f')"
                >
                  <template #suffix>
                    <span class="text-gray-500 text-sm">°F</span>
                  </template>
                </el-input>
              </el-form-item>

              <el-form-item :label="$t('production.sales_gas_metering.fields.h2o_lb_mmscf')" prop="h2o_lb_mmscf">
                <el-input
                  v-model.number="form.h2o_lb_mmscf"
                  type="number"
                  :min="0"
                  :step="0.01"
                  :placeholder="$t('production.sales_gas_metering.placeholder.h2o_lb_mmscf')"
                >
                  <template #suffix>
                    <span class="text-gray-500 text-sm">lb/MMscf</span>
                  </template>
                </el-input>
              </el-form-item>

              <el-form-item :label="$t('production.sales_gas_metering.fields.co2_mol_pct')" prop="co2_mol_pct">
                <el-input
                  v-model.number="form.co2_mol_pct"
                  type="number"
                  :min="0"
                  :max="100"
                  :step="0.01"
                  :placeholder="$t('production.sales_gas_metering.placeholder.co2_mol_pct')"
                >
                  <template #suffix>
                    <span class="text-gray-500 text-sm">mol%</span>
                  </template>
                </el-input>
              </el-form-item>

              <el-form-item :label="$t('production.sales_gas_metering.fields.ghv')" prop="ghv">
                <el-input
                  v-model.number="form.ghv"
                  type="number"
                  :min="0"
                  :step="0.01"
                  :placeholder="$t('production.sales_gas_metering.placeholder.ghv')"
                >
                  <template #suffix>
                    <span class="text-gray-500 text-sm">BTU/scf</span>
                  </template>
                </el-input>
              </el-form-item>

              <el-form-item :label="$t('production.sales_gas_metering.fields.specific_gravity')" prop="specific_gravity">
                <el-input
                  v-model.number="form.specific_gravity"
                  type="number"
                  :min="0"
                  :max="2"
                  :step="0.0001"
                  :placeholder="$t('production.sales_gas_metering.placeholder.specific_gravity')"
                />
              </el-form-item>

              <el-form-item :label="$t('production.sales_gas_metering.fields.ejgp_pressure_psig')" prop="ejgp_pressure_psig">
                <el-input
                  v-model.number="form.ejgp_pressure_psig"
                  type="number"
                  :min="0"
                  :step="0.01"
                  :placeholder="$t('production.sales_gas_metering.placeholder.ejgp_pressure_psig')"
                >
                  <template #suffix>
                    <span class="text-gray-500 text-sm">psig</span>
                  </template>
                </el-input>
              </el-form-item>
            </div>
          </div>

          <!-- Flow Rates per Buyer -->
          <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
              <div>
                  <h3 class="text-lg font-semibold mb-4 text-gray-700">{{ $t('production.sales_gas_metering.fields.hcdp_temperature') }}</h3>
                  <div class="overflow-x-auto">
                    <el-table 
                      :data="form.hcdp" 
                      class="base-table" 
                    >
                      <el-table-column
                        :label="$t('production.sales_gas_metering.fields.hcdp_equipment')"
                        prop="name"
                      />
                      
                      <el-table-column
                        :label="$t('production.sales_gas_metering.fields.temperature_f')"
                        align="center"
                        width="250"
                      >
                        <template #default="scope">
                          <el-input
                            v-model.number="scope.row.temp"
                            type="number"
                            class="w-full"
                            :min="0"
                            :step="0.01"
                            @input="calculateTotal"
                            :placeholder="$t('production.sales_gas_metering.placeholder.temperature_f')"
                          >
                            <template #suffix>
                              <span class="text-gray-500 text-sm">F</span>
                            </template>
                          </el-input>
                        </template>
                      </el-table-column>
                    </el-table>
                  </div>
              </div>
              <div>
                  <h3 class="text-lg font-semibold mb-4 text-gray-700">{{ $t('production.sales_gas_metering.fields.flow_rates') }}</h3>
                  <div class="overflow-x-auto">
                    <el-table 
                      :data="form.flowrates" 
                      class="base-table"
                      :summary-method="getSummaries"
                      show-summary
                    >
                      <el-table-column
                        :label="$t('production.sales_gas_metering.fields.gas_buyer')"
                        prop="buyer.name"
                      />
                      
                      <el-table-column
                        :label="$t('production.sales_gas_metering.fields.primary_stream')"
                        align="center"
                      >
                        <template #default="scope">
                          <el-input
                            v-model.number="scope.row.primary_stream"
                            type="number"
                            class="w-full"
                            :min="0"
                            :step="0.01"
                            @input="calculateTotal"
                            :placeholder="$t('production.sales_gas_metering.placeholder.primary_stream')"
                          >
                            <template #suffix>
                              <span class="text-gray-500 text-sm">MMSCFD</span>
                            </template>
                          </el-input>
                        </template>
                      </el-table-column>

                      <el-table-column
                        :label="$t('production.sales_gas_metering.fields.backup_stream')"
                        align="center"
                      >
                        <template #default="scope">
                          <el-input
                            v-model.number="scope.row.backup_stream"
                            type="number"
                            class="w-full"
                            :min="0"
                            :step="0.01"
                            :placeholder="$t('production.sales_gas_metering.placeholder.backup_stream')"
                          >
                            <template #suffix>
                              <span class="text-gray-500 text-sm">MMSCFD</span>
                            </template>
                          </el-input>
                        </template>
                      </el-table-column>
                    </el-table>
                  </div>
              </div>
            </div>
          </div>

          <!-- Remarks -->
          <div class="mb-2">
            <el-form-item :label="$t('production.sales_gas_metering.fields.remarks')" prop="remarks">
              <el-input
                v-model="form.remarks"
                type="textarea"
                :rows="3"
                :placeholder="$t('production.sales_gas_metering.placeholder.remarks')"
                maxlength="500"
                show-word-limit
              />
            </el-form-item>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="p-6 border-t bg-gray-50">
          <div class="flex justify-between items-center">
            <div class="flex space-x-3">
              <el-button v-if="isEdit" @click="onDuplicate" type="info" plain>
                <Icon icon="mingcute:copy-line" class="mr-2" />
                {{ $t('common.actions.duplicate') }}
              </el-button>
            </div>
            
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

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import dayjs from 'dayjs'

import PageHeader from '@/components/PageHeader.vue'
import SelectVessel from '@/components/select/SelectVessel.vue'
import { useUser } from '@/composables/auth/useUser'


const { userVesselId } = useUser();
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const title = ref(route.meta.title ?? 'production.sales_gas_metering.create')
const formRef = ref(null)
const loading = ref(false)
const isEdit = computed(() => !!route.params.id)

// Form State
const form = reactive({
  vessel_id: userVesselId,
  date: dayjs().format('YYYY-MM-DD'),
  time: dayjs().format('HH:00:00'),
  
  // Gas Quality Readings
  pressure_psig: null,
  temperature_f: null,
  h2o_lb_mmscf: null,
  co2_mol_pct: null,
  ghv: null,
  specific_gravity: null,
  ejgp_pressure_psig: null,
  hcdp : [
    {
      "name" : "HCDP A",
      "temp" : null,
    },
    {
      "name" : "HCDP B",
      "temp" : null,
    }
  ],
  // Flow Rates
  flowrates: [],
  
  remarks: ''
})

// Validation
const disabledDate = (date) => date.getTime() > new Date().getTime()

const requiredMsg = (attr) => t('common.validation.required', { attribute: attr })

const numberRange = (min, max, attr, opts = {}) => ({
  validator: (_rule, value, callback) => {
    if (opts.allowNull && !value) return callback()
    if (!opts.allowNull && !value) return callback(new Error(requiredMsg(attr)))
    
    const num = Number(value)
    if (isNaN(num)) return callback(new Error(t('common.validation.numeric', { attribute: attr })))
    if (min !== undefined && num < min) return callback(new Error(t('common.validation.min.numeric', { attribute: attr, min })))
    if (max !== undefined && num > max) return callback(new Error(t('common.validation.max.numeric', { attribute: attr, max })))
    
    callback()
  },
  trigger: ['change', 'blur']
})

const formRules = ref({
  vessel_id: [{ required: true, message: requiredMsg(t('production.sales_gas_metering.fields.vessel_id')), trigger: 'change' }],
  date: [{ required: true, message: requiredMsg(t('production.sales_gas_metering.fields.date')), trigger: 'change' }],
  reading_hour: [{ required: true, message: requiredMsg(t('production.sales_gas_metering.fields.time')), trigger: 'change' }],
  pressure_psig: [numberRange(0, 5000, t('production.sales_gas_metering.fields.pressure_psig'), { allowNull: true })],
  temperature_f: [numberRange(-100, 500, t('production.sales_gas_metering.fields.temperature_f'), { allowNull: true })],
  co2_mol: [numberRange(0, 100, t('production.sales_gas_metering.fields.co2'), { allowNull: true })],
  specific_gravity: [numberRange(0, 2, t('production.sales_gas_metering.fields.specific_gravity'), { allowNull: true })]
})

// Methods
const calculateTotal = () => {
  form.total_flow_rate = form.flowrates.reduce((sum, row) => sum + (Number(row.primary_stream) || 0), 0)
}

const getSummaries = (param) => {
  const { columns } = param
  const sums = []
  columns.forEach((column, index) => {
    if (index === 0) {
      sums[index] = t('production.sales_gas_metering.fields.total')
    } else if (index === 1) {
      const total = form.flowrates.reduce((sum, row) => sum + (Number(row.primary_stream) || 0), 0)
      sums[index] = `${total.toFixed(2)} MMSCFD`
    } else {
      sums[index] = ''
    }
  })
  return sums
}


const onSubmit = async () => {
  if (!formRef.value) return
  formRef.value.validate(async (valid) => {
    if (!valid) {
      return ElMessage({ message: t('common.errors.validation_failed'), type: 'error' })
    }

    try {
      loading.value = true

      // endpoint sesuai dengan struktur database baru
      const url = isEdit.value
        ? `/production/sales-gas-metering/${route.params.id}/update`
        : '/production/sales-gas-metering/store'
      const method = isEdit.value ? 'put' : 'post'

      const { status, data } = await axios({ method, url, data: form })

      if (status === 200 || status === 201) {
        ElMessage({ message: t('common.messages.saved'), type: 'success' })
        const id = data?.id || data?.data?.id
        if (id) {
          router.replace({ path: `/production/sales-gas-metering/${id}` })
        } else {
          router.push({ name: 'production.sales_gas_metering.index' })
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
          message: t('production.sales_gas_allocation.validation.duplicate_entry'), 
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

const onBack = () => {
  router.push({ name: 'production.sales_gas_metering.index' })
}

const onDuplicate = () => {
  // Duplicate logic here
}
const loadGasBuyer = async () => {
  try {
    loading.value = true
    form.lines = []
    const { data } = await axios.get(`/master/gas-buyer`, { params: { vessel_id: form.vessel_id } })
    const d = data?.data || data
    if (d) {
      d.forEach(item => {
        form.flowrates.push({
          buyer_id: item.id,
          buyer: {
            id: item.id,
            name: item.name,
          },
          primary_stream: null,
          backup_stream:null
        })
      })
    }
  } catch (e) {
    console.error('Load error:', e)
    ElMessage.error(t('common.errors.server_error'))
    onBack()
  } finally {
    loading.value = false
  }
}

const loadData = async () => {
  // Load edit data logic here
}

onMounted(() => {
  loadGasBuyer()
  loadData()
})
</script>