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
          <div class="mb-2">
            <el-row :gutter="24">
              <!-- Well -->
              <el-col :md="8" :sm="8" :xs="24">
                <el-form-item :label="$t('production.sales_gas_allocation.fields.vessel_id')" prop="vessel_id">
                  <select-vessel
                    v-model="form.vessel_id"
                    :vessel-id="form.vessel_id"
                    :placeholder="$t('production.sales_gas_allocation.placeholder.vessel_id')"
                    @change="loadGasBuyer"
                  />
                </el-form-item>
              </el-col>

              <!-- Production Date -->
              <el-col :md="8" :sm="8" :xs="24">
                <el-form-item :label="$t('production.sales_gas_allocation.fields.date')" prop="date">
                  <el-date-picker
                    v-model="form.date"
                    type="date"
                    :placeholder="$t('production.sales_gas_allocation.placeholder.date')"
                    format="DD-MM-YYYY"
                    value-format="YYYY-MM-DD"
                    style="width: 100%"
                    :disabled-date="disabledDate"
                    @change="() => formRef?.validateField('date')"
                  />
                </el-form-item>
              </el-col>
              
              <el-col :md="8" :sm="8" :xs="24">
                <el-form-item :label="$t('production.sales_gas_allocation.fields.total')" prop="total">
                  <el-input
                    v-model.number="form.total"
                    type="number"
                    class="w-full"
                    readonly
                    :min="0"
                    :step="0.001"
                    :placeholder="$t('production.sales_gas_allocation.placeholder.total')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCF</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

            </el-row>
          </div>

          <div class="mb-2">
            <div class="overflow-x-auto">
              <el-table class="base-table" :data="form.lines">
                <el-table-column
                  :label="$t('production.sales_gas_allocation.fields.gas_buyer')"
                  prop="buyer.name"
                />
                <el-table-column
                  :label="$t('production.sales_gas_allocation.fields.allocation')">
                  <template #default="scope">
                    <el-input
                      v-model.number="scope.row.allocation"
                      type="number"
                      class="w-full"
                      :min="0"
                      :step="0.01"
                      :change="calculateTotal()"
                      :placeholder="$t('production.sales_gas_allocation.placeholder.allocation')"
                    >
                      <template #suffix>
                        <span class="text-gray-500 text-sm">MMSCF</span>
                      </template>
                    </el-input>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="mb-2">
            <!-- Remarks -->
            <el-col :span="24" class="mb-2">
              <el-form-item :label="$t('production.sales_gas_allocation.fields.remarks')" prop="remarks">
                <el-input
                  v-model="form.remarks"
                  type="textarea"
                  :rows="3"
                  :placeholder="$t('production.sales_gas_allocation.placeholder.remarks')"
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
import SelectVessel from '@/components/select/SelectVessel.vue'
import { useUser } from '@/composables/auth/useUser'
import dayjs from 'dayjs';

const { userVesselId } = useUser();
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const title = ref(route.meta.title ?? 'production.sales_gas_allocation.create')
const formRef = ref(null)
const loading = ref(false)

// edit mode
const isEdit = computed(() => !!route.params.id)

/**
 * State form - Updated to match database schema
 */
const form = reactive({
  vessel_id: userVesselId || null,
  date: dayjs().format('YYYY-MM-DD'),
  total : 0,
  lines : [],
  remarks: ''
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
    if (!value) return callback(new Error(requiredMsg(t('production.sales_gas_allocation.fields.date'))))
    
    const productionDate = dayjs(value)
    if (!productionDate.isValid()) {
      return callback(new Error(t('common.validation.date', { attribute: t('production.sales_gas_allocation.fields.date') })))
    }

    if (productionDate.isAfter(dayjs(), 'day')) {
      return callback(new Error(t('common.validation.before_or_equal', { 
        attribute: t('production.sales_gas_allocation.fields.date'), 
        date: t('common.words.today') 
      })))
    }

    callback()
  },
  trigger: ['change', 'blur']
}

/* -------------------- VALIDATION RULES -------------------- */
const formRules = ref({
  vessel_id: [
    { required: true, message: requiredMsg(t('production.sales_gas_allocation.fields.vessel_id')), trigger: 'change' }
  ],
  date: [ notFutureDate ],
  remarks: [
    { min: 0, max: 500, message: t('common.validation.max', { attribute: t('production.sales_gas_allocation.fields.remarks'), max: 500 }), trigger: 'blur' }
  ]
})


/* -------------------- HELPERS -------------------- */
const sanitizeNumeric = (v) => (v === '' || v === null || v === undefined ? null : Number(v))

const calculateTotal = () => {
    let total = 0;

    for (var i = 0; i < form.lines.length; i++) {
      total += form.lines[i].allocation;
    }

    form.total = total;
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

      // endpoint sesuai dengan struktur database baru
      const url = isEdit.value
        ? `/production/sales-gas-allocation/${route.params.id}/update`
        : '/production/sales-gas-allocation/store'
      const method = isEdit.value ? 'put' : 'post'

      const { status, data } = await axios({ method, url, data: form })

      if (status === 200 || status === 201) {
        ElMessage({ message: t('common.messages.saved'), type: 'success' })
        const id = data?.id || data?.data?.id
        if (id) {
          router.replace({ path: `/production/sales-gas-allocation/${id}` })
        } else {
          router.push({ name: 'production.sales_gas_allocation.index' })
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

/* -------------------- LOAD DATA (edit mode) -------------------- */
const loadData = async () => {
  if (!isEdit.value) return
  try {
    loading.value = true
    const { data } = await axios.get(`/production/sales-gas-allocation/${route.params.id}`)
    const d = data?.data || data
    if (d) {
      form.vessel_id = d.vessel_id ?? user.value?.vessel_id
      form.date = d.date ?? dayjs().format('YYYY-MM-DD')
      form.remarks = d.remarks ?? ''
      // form.lines = d.lines || []
      form.lines = d.lines.map(item => ({
        ...item,
        allocation: sanitizeNumeric(item.allocation),
      }))
      calculateTotal()
    }
  } catch (e) {
    console.error('Load error:', e)
    ElMessage.error(t('common.errors.server_error'))
    onBack()
  } finally {
    loading.value = false
  }
}



const loadGasBuyer = async () => {
  try {
    loading.value = true
    form.lines = []
    const { data } = await axios.get(`/master/gas-buyer`, { params: { vessel_id: form.vessel_id } })
    const d = data?.data || data
    if (d) {
      d.forEach(item => {
        form.lines.push({
          buyer_id: item.id,
          buyer: {
            id: item.id,
            name: item.name,
          },
          allocation: null,
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
/* -------------------- ACTIONS -------------------- */
const onBack = () => {
  router.push({ name: 'production.well-production.index' })
}

const onDuplicate = () => {
  const duplicateData = { ...form }
  duplicateData.date = dayjs().format('YYYY-MM-DD')
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
  loadGasBuyer()
  loadData()
})
</script>