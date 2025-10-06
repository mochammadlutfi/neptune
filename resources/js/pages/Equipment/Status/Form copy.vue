<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center space-x-3">
            <router-link 
              to="/equipment/status" 
              class="flex items-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <Icon icon="heroicons:arrow-left" class="w-5 h-5 mr-2" />
              {{ $t('base.actions.back') }}
            </router-link>
            <div class="h-6 border-l border-gray-300 dark:border-gray-600"></div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
              {{ isEditMode ? $t('equipment.status.edit') : $t('equipment.status.create') }}
            </h1>
          </div>
          
          <!-- Mobile Entry Mode Toggle -->
          <div class="flex items-center space-x-3">
            <el-switch
              v-model="mobileEntryMode"
              :active-text="$t('equipment.status.mobile_entry')"
              size="small"
              class="hidden sm:flex"
            />
            <el-button
              @click="mobileEntryMode = !mobileEntryMode"
              :type="mobileEntryMode ? 'primary' : 'default'"
              size="small"
              class="sm:hidden"
            >
              <Icon icon="heroicons:device-phone-mobile" class="w-4 h-4" />
            </el-button>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <el-form
        ref="formRef"
        :model="form"
        :rules="rules"
        label-position="top"
        class="space-y-6"
      >
        <!-- Equipment Selection -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
              <Icon icon="heroicons:cog-6-tooth" class="w-5 h-5 mr-2 text-blue-500" />
              {{ $t('equipment.status.equipment_selection') }}
            </h3>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Vessel Selection -->
              <el-form-item :label="$t('base.common.vessel')" prop="vessel_id">
                <el-select
                  v-model="form.vessel_id"
                  :placeholder="$t('equipment.status.select_vessel')"
                  class="w-full"
                  @change="onVesselChange"
                >
                  <el-option
                    v-for="vessel in vessels"
                    :key="vessel.id"
                    :label="vessel.name"
                    :value="vessel.id"
                  />
                </el-select>
              </el-form-item>

              <!-- Equipment Selection -->
              <el-form-item :label="$t('equipment.status.fields.equipment_name')" prop="equipment_id">
                <el-select
                  v-model="form.equipment_id"
                  :placeholder="$t('equipment.status.select_equipment')"
                  :loading="loadingEquipment"
                  filterable
                  class="w-full"
                  @change="onEquipmentChange"
                >
                  <el-option
                    v-for="equipment in filteredEquipment"
                    :key="equipment.id"
                    :label="`${equipment.name} (${equipment.tag})`"
                    :value="equipment.id"
                  >
                    <div class="flex justify-between items-center">
                      <span>{{ equipment.name }}</span>
                      <el-tag :type="getEquipmentTypeColor(equipment.type)" size="small">
                        {{ $t(`equipment.types.${equipment.type}`) }}
                      </el-tag>
                    </div>
                  </el-option>
                </el-select>
              </el-form-item>
            </div>

            <!-- Selected Equipment Info -->
            <div v-if="selectedEquipment" class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <Icon icon="heroicons:information-circle" class="w-5 h-5 text-blue-500" />
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ selectedEquipment.name }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                      {{ $t('equipment.status.fields.equipment_tag') }}: {{ selectedEquipment.tag }}
                    </p>
                  </div>
                </div>
                <el-tag :type="getStatusColor(selectedEquipment.current_status)" size="large">
                  {{ $t(`equipment.status.operational_status.${selectedEquipment.current_status}`) }}
                </el-tag>
              </div>
            </div>
          </div>
        </div>

        <!-- Status Recording -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
              <Icon icon="heroicons:clipboard-document-list" class="w-5 h-5 mr-2 text-green-500" />
              {{ $t('equipment.status.status_recording') }}
            </h3>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Recording Time -->
              <el-form-item :label="$t('equipment.status.fields.recorded_at')" prop="recorded_at">
                <el-date-picker
                  v-model="form.recorded_at"
                  type="datetime"
                  :placeholder="$t('equipment.status.select_time')"
                  class="w-full"
                  format="DD/MM/YYYY HH:mm"
                  value-format="YYYY-MM-DD HH:mm:ss"
                />
              </el-form-item>

              <!-- Operational Status -->
              <el-form-item :label="$t('equipment.status.fields.operational_status')" prop="operational_status">
                <el-select
                  v-model="form.operational_status"
                  :placeholder="$t('equipment.status.select_status')"
                  class="w-full"
                >
                  <el-option
                    v-for="(label, status) in operationalStatuses"
                    :key="status"
                    :label="label"
                    :value="status"
                  >
                    <div class="flex items-center space-x-2">
                      <div :class="`w-3 h-3 rounded-full ${getStatusDotColor(status)}`"></div>
                      <span>{{ label }}</span>
                    </div>
                  </el-option>
                </el-select>
              </el-form-item>
            </div>
          </div>
        </div>

        <!-- Performance Readings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                <Icon icon="heroicons:chart-bar" class="w-5 h-5 mr-2 text-purple-500" />
                {{ $t('equipment.status.performance_readings') }}
              </h3>
              <el-button 
                @click="showAdvancedReadings = !showAdvancedReadings"
                text
                type="primary"
                size="small"
              >
                <Icon :icon="showAdvancedReadings ? 'heroicons:chevron-up' : 'heroicons:chevron-down'" class="w-4 h-4 mr-1" />
                {{ showAdvancedReadings ? $t('equipment.status.basic_readings') : $t('equipment.status.advanced_readings') }}
              </el-button>
            </div>
          </div>
          
          <div class="p-6">
            <!-- Basic Readings -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <!-- Running Hours -->
              <el-form-item :label="$t('equipment.status.fields.running_hours')" prop="running_hours">
                <el-input-number
                  v-model="form.running_hours"
                  :placeholder="$t('equipment.status.enter_hours')"
                  :min="0"
                  :precision="1"
                  class="w-full"
                  :class="{ 'mobile-number-input': mobileEntryMode }"
                />
              </el-form-item>

              <!-- Load Percentage -->
              <el-form-item :label="$t('equipment.status.fields.load_percentage')" prop="load_percentage">
                <el-input-number
                  v-model="form.load_percentage"
                  :placeholder="$t('equipment.status.enter_load')"
                  :min="0"
                  :max="100"
                  :precision="1"
                  class="w-full"
                  :class="{ 'mobile-number-input': mobileEntryMode }"
                />
              </el-form-item>

              <!-- Pressure Reading -->
              <el-form-item :label="$t('equipment.status.fields.pressure_reading')" prop="pressure_reading">
                <el-input-number
                  v-model="form.pressure_reading"
                  :placeholder="$t('equipment.status.enter_pressure')"
                  :min="0"
                  :precision="2"
                  class="w-full"
                  :class="{ 'mobile-number-input': mobileEntryMode }"
                />
              </el-form-item>

              <!-- Temperature Reading -->
              <el-form-item :label="$t('equipment.status.fields.temperature_reading')" prop="temperature_reading">
                <el-input-number
                  v-model="form.temperature_reading"
                  :placeholder="$t('equipment.status.enter_temperature')"
                  :precision="1"
                  class="w-full"
                  :class="{ 'mobile-number-input': mobileEntryMode }"
                />
              </el-form-item>

              <!-- Speed RPM -->
              <el-form-item :label="$t('equipment.status.fields.speed_rpm')" prop="speed_rpm">
                <el-input-number
                  v-model="form.speed_rpm"
                  :placeholder="$t('equipment.status.enter_rpm')"
                  :min="0"
                  :precision="0"
                  class="w-full"
                  :class="{ 'mobile-number-input': mobileEntryMode }"
                />
              </el-form-item>

              <!-- Power Output -->
              <el-form-item :label="$t('equipment.status.fields.power_output_kw')" prop="power_output_kw">
                <el-input-number
                  v-model="form.power_output_kw"
                  :placeholder="$t('equipment.status.enter_power')"
                  :min="0"
                  :precision="2"
                  class="w-full"
                  :class="{ 'mobile-number-input': mobileEntryMode }"
                />
              </el-form-item>
            </div>

            <!-- Advanced Readings -->
            <div v-show="showAdvancedReadings" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Vibration Reading -->
                <el-form-item :label="$t('equipment.status.fields.vibration_reading')" prop="vibration_reading">
                  <el-input-number
                    v-model="form.vibration_reading"
                    :placeholder="$t('equipment.status.enter_vibration')"
                    :min="0"
                    :precision="3"
                    class="w-full"
                    :class="{ 'mobile-number-input': mobileEntryMode }"
                  />
                </el-form-item>

                <!-- Fuel Consumption Rate -->
                <el-form-item :label="$t('equipment.status.fields.fuel_consumption_rate')" prop="fuel_consumption_rate">
                  <el-input-number
                    v-model="form.fuel_consumption_rate"
                    :placeholder="$t('equipment.status.enter_fuel')"
                    :min="0"
                    :precision="2"
                    class="w-full"
                    :class="{ 'mobile-number-input': mobileEntryMode }"
                  />
                </el-form-item>

                <!-- Efficiency Percentage -->
                <el-form-item :label="$t('equipment.status.fields.efficiency_percentage')" prop="efficiency_percentage">
                  <el-input-number
                    v-model="form.efficiency_percentage"
                    :placeholder="$t('equipment.status.enter_efficiency')"
                    :min="0"
                    :max="100"
                    :precision="1"
                    class="w-full"
                    :class="{ 'mobile-number-input': mobileEntryMode }"
                  />
                </el-form-item>
              </div>
            </div>
          </div>
        </div>

        <!-- Remarks -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
              <Icon icon="heroicons:document-text" class="w-5 h-5 mr-2 text-orange-500" />
              {{ $t('base.common.remarks') }}
            </h3>
          </div>
          
          <div class="p-6">
            <el-form-item prop="remarks">
              <el-input
                v-model="form.remarks"
                type="textarea"
                :rows="4"
                :placeholder="$t('equipment.status.remarks_placeholder')"
                class="w-full"
              />
            </el-form-item>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 rounded-lg shadow-sm">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div class="flex items-center space-x-3 text-sm text-gray-600 dark:text-gray-400">
              <Icon icon="heroicons:information-circle" class="w-4 h-4" />
              <span>{{ $t('equipment.status.auto_save_enabled') }}</span>
            </div>
            
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
              <el-button @click="saveDraft" :loading="saving">
                <Icon icon="heroicons:document" class="w-4 h-4 mr-2" />
                {{ $t('equipment.status.save_draft') }}
              </el-button>
              
              <el-button @click="resetForm">
                <Icon icon="heroicons:arrow-path" class="w-4 h-4 mr-2" />
                {{ $t('base.actions.reset') }}
              </el-button>
              
              <el-button
                type="primary"
                @click="submitForm"
                :loading="submitting"
                :disabled="!isFormValid"
              >
                <Icon icon="heroicons:check" class="w-4 h-4 mr-2" />
                {{ isEditMode ? $t('base.actions.update') : $t('base.actions.submit') }}
              </el-button>
            </div>
          </div>
        </div>
      </el-form>
    </div>

    <!-- Mobile Quick Actions FAB -->
    <div v-if="mobileEntryMode" class="fixed bottom-6 right-6 sm:hidden">
      <el-button
        type="primary"
        size="large"
        circle
        @click="showQuickActions = !showQuickActions"
        class="shadow-lg"
      >
        <Icon icon="heroicons:ellipsis-horizontal" class="w-6 h-6" />
      </el-button>
      
      <!-- Quick Actions Menu -->
      <div
        v-show="showQuickActions"
        class="absolute bottom-16 right-0 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-2"
      >
        <el-button @click="quickSetRunning" size="small" class="w-full mb-2">
          <Icon icon="heroicons:play" class="w-4 h-4 mr-2" />
          {{ $t('equipment.status.quick_set_running') }}
        </el-button>
        <el-button @click="quickSetStandby" size="small" class="w-full mb-2">
          <Icon icon="heroicons:pause" class="w-4 h-4 mr-2" />
          {{ $t('equipment.status.quick_set_standby') }}
        </el-button>
        <el-button @click="quickSetMaintenance" size="small" class="w-full">
          <Icon icon="heroicons:wrench-screwdriver" class="w-4 h-4 mr-2" />
          {{ $t('equipment.status.quick_set_maintenance') }}
        </el-button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()

// Reactive data
const formRef = ref()
const mobileEntryMode = ref(false)
const showAdvancedReadings = ref(false)
const showQuickActions = ref(false)
const loadingEquipment = ref(false)
const saving = ref(false)
const submitting = ref(false)

// Form data
const form = reactive({
  vessel_id: null,
  equipment_id: null,
  recorded_at: new Date().toISOString().slice(0, 19).replace('T', ' '),
  operational_status: 'running',
  running_hours: null,
  load_percentage: null,
  pressure_reading: null,
  temperature_reading: null,
  vibration_reading: null,
  speed_rpm: null,
  fuel_consumption_rate: null,
  power_output_kw: null,
  efficiency_percentage: null,
  remarks: ''
})

// Sample data (replace with API calls)
const vessels = ref([
  { id: 1, name: 'FPU Trunojoyo-01', code: 'TRU01' },
  { id: 2, name: 'FSO Arco Ardjuna', code: 'ARD01' },
  { id: 3, name: 'Drilling Rig Maleo', code: 'MAL01' }
])

const equipmentList = ref([
  {
    id: 1,
    vessel_id: 1,
    name: 'Gas Turbine Generator #1',
    tag: 'GTG-001',
    type: 'gas_turbine',
    category: 'production',
    current_status: 'running'
  },
  {
    id: 2,
    vessel_id: 1,
    name: 'Gas Turbine Generator #2',
    tag: 'GTG-002',
    type: 'gas_turbine',
    category: 'production',
    current_status: 'standby'
  },
  {
    id: 3,
    vessel_id: 1,
    name: 'Main Oil Export Pump A',
    tag: 'MOEP-A',
    type: 'pump',
    category: 'production',
    current_status: 'running'
  },
  {
    id: 4,
    vessel_id: 1,
    name: 'HP Compressor Train #1',
    tag: 'HPCT-001',
    type: 'compressor',
    category: 'production',
    current_status: 'maintenance'
  }
])

// Computed properties
const isEditMode = computed(() => !!route.params.id)

const filteredEquipment = computed(() => {
  if (!form.vessel_id) return []
  return equipmentList.value.filter(eq => eq.vessel_id === form.vessel_id)
})

const selectedEquipment = computed(() => {
  if (!form.equipment_id) return null
  return equipmentList.value.find(eq => eq.id === form.equipment_id)
})

const operationalStatuses = computed(() => ({
  running: t('equipment.status.operational_status.running'),
  standby: t('equipment.status.operational_status.standby'),
  maintenance: t('equipment.status.operational_status.maintenance'),
  breakdown: t('equipment.status.operational_status.breakdown'),
  not_available: t('equipment.status.operational_status.not_available')
}))

const isFormValid = computed(() => {
  return form.vessel_id && form.equipment_id && form.operational_status && form.recorded_at
})

// Form validation rules
const rules = reactive({
  vessel_id: [
    { required: true, message: t('equipment.status.vessel_required'), trigger: 'change' }
  ],
  equipment_id: [
    { required: true, message: t('equipment.status.equipment_required'), trigger: 'change' }
  ],
  recorded_at: [
    { required: true, message: t('equipment.status.time_required'), trigger: 'change' }
  ],
  operational_status: [
    { required: true, message: t('equipment.status.status_required'), trigger: 'change' }
  ]
})

// Methods
const onVesselChange = () => {
  form.equipment_id = null
  loadingEquipment.value = true
  // Simulate API call
  setTimeout(() => {
    loadingEquipment.value = false
  }, 500)
}

const onEquipmentChange = () => {
  if (selectedEquipment.value) {
    // Auto-populate some fields based on equipment type
    nextTick(() => {
      // You can set default values based on equipment type here
    })
  }
}

const getEquipmentTypeColor = (type) => {
  const colors = {
    gas_turbine: 'danger',
    generator: 'warning',
    compressor: 'primary',
    pump: 'success',
    motor: 'info'
  }
  return colors[type] || 'info'
}

const getStatusColor = (status) => {
  const colors = {
    running: 'success',
    standby: 'warning',
    maintenance: 'info',
    breakdown: 'danger',
    not_available: 'info'
  }
  return colors[status] || 'info'
}

const getStatusDotColor = (status) => {
  const colors = {
    running: 'bg-green-500',
    standby: 'bg-yellow-500',
    maintenance: 'bg-blue-500',
    breakdown: 'bg-red-500',
    not_available: 'bg-gray-500'
  }
  return colors[status] || 'bg-gray-500'
}

const quickSetRunning = () => {
  form.operational_status = 'running'
  form.load_percentage = 85
  showQuickActions.value = false
}

const quickSetStandby = () => {
  form.operational_status = 'standby'
  form.load_percentage = 0
  showQuickActions.value = false
}

const quickSetMaintenance = () => {
  form.operational_status = 'maintenance'
  form.load_percentage = 0
  showQuickActions.value = false
}

const saveDraft = async () => {
  saving.value = true
  try {
    // API call to save draft
    await new Promise(resolve => setTimeout(resolve, 1000))
    ElMessage.success(t('equipment.status.draft_saved'))
  } catch (error) {
    ElMessage.error(t('common.error_msg'))
  } finally {
    saving.value = false
  }
}

const resetForm = () => {
  ElMessageBox.confirm(
    t('equipment.status.reset_confirm'),
    t('base.actions.reset'),
    {
      confirmButtonText: t('base.actions.confirm'),
      cancelButtonText: t('base.actions.cancel'),
      type: 'warning'
    }
  ).then(() => {
    formRef.value.resetFields()
    ElMessage.success(t('equipment.status.form_reset'))
  })
}

const submitForm = async () => {
  try {
    await formRef.value.validate()
    submitting.value = true
    
    // API call to submit form
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    ElMessage.success(
      isEditMode.value 
        ? t('equipment.status.update_success') 
        : t('equipment.status.create_success')
    )
    
    router.push('/equipment/status')
  } catch (error) {
    if (error !== false) {
      ElMessage.error(t('common.error_msg'))
    }
  } finally {
    submitting.value = false
  }
}

// Auto-save functionality
let autoSaveTimeout = null
watch(form, () => {
  if (autoSaveTimeout) clearTimeout(autoSaveTimeout)
  autoSaveTimeout = setTimeout(() => {
    if (isFormValid.value) {
      saveDraft()
    }
  }, 10000) // Auto-save every 10 seconds
}, { deep: true })

// Lifecycle
onMounted(() => {
  // Load existing data if in edit mode
  if (isEditMode.value) {
    // API call to load equipment status data
  }
  
  // Set default vessel if only one available
  if (vessels.value.length === 1) {
    form.vessel_id = vessels.value[0].id
    onVesselChange()
  }
})
</script>

<style scoped>
.mobile-number-input :deep(.el-input-number) {
  font-size: 16px;
}

.mobile-number-input :deep(.el-input__inner) {
  font-size: 16px;
  height: 44px;
}

@media (max-width: 640px) {
  .mobile-number-input :deep(.el-input-number) {
    width: 100% !important;
  }
}
</style>