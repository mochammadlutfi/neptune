<template>
  <div class="personnel-position-form min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <el-button
            type="default"
            @click="handleBack"
            circle
          >
            <template #icon>
              <Icon icon="mdi:arrow-left" :width="16" :height="16" />
            </template>
          </el-button>
          <div>
            <h1 class="text-2xl font-bold text-gray-900">
              {{ isEdit ? $t('master.personnel_positions.edit') : $t('master.personnel_positions.create') }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">
              {{ isEdit ? $t('master.personnel_positions.edit_description') : $t('master.personnel_positions.create_description') }}
            </p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <el-button type="default" @click="handleReset" :disabled="isLoading">
            <template #icon>
              <Icon icon="mdi:refresh" :width="16" :height="16" />
            </template>
            {{ $t('common.reset') }}
          </el-button>
          <el-button type="primary" @click="handleSave" :loading="isLoading">
            <template #icon>
              <Icon icon="mdi:content-save" :width="16" :height="16" />
            </template>
            {{ $t('common.save') }}
          </el-button>
        </div>
      </div>
    </div>

    <!-- Form Content -->
    <div class="max-w-4xl mx-auto p-6">
      <el-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-position="top"
        class="personnel-position-form-content"
      >
        <!-- Basic Information -->
        <el-card class="form-section" shadow="hover">
          <template #header>
            <div class="flex items-center">
              <Icon icon="mdi:information" class="text-blue-600 mr-2" :width="20" :height="20" />
              <span class="font-semibold">{{ $t('master.personnel_positions.basic_information') }}</span>
            </div>
          </template>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Position Code -->
            <el-form-item :label="$t('master.personnel_positions.fields.code')" prop="code">
              <el-input
                v-model="formData.code"
                placeholder="Enter position code..."
                maxlength="20"
                show-word-limit
              >
                <template #prefix>
                  <Icon icon="mdi:identifier" :width="16" :height="16" />
                </template>
              </el-input>
            </el-form-item>

            <!-- Position Name -->
            <el-form-item :label="$t('master.personnel_positions.fields.name')" prop="name">
              <el-input
                v-model="formData.name"
                placeholder="Enter position name..."
                maxlength="100"
                show-word-limit
              >
                <template #prefix>
                  <Icon icon="mdi:account" :width="16" :height="16" />
                </template>
              </el-input>
            </el-form-item>

            <!-- Department -->
            <el-form-item :label="$t('master.personnel_positions.fields.department')" prop="department">
              <el-select
                v-model="formData.department"
                placeholder="Select department..."
                filterable
                allow-create
                class="w-full"
              >
                <el-option
                  v-for="dept in departments"
                  :key="dept"
                  :label="dept"
                  :value="dept"
                />
              </el-select>
            </el-form-item>

            <!-- Level -->
            <el-form-item :label="$t('master.personnel_positions.fields.level')" prop="level">
              <el-select
                v-model="formData.level"
                placeholder="Select level..."
                class="w-full"
              >
                <el-option
                  v-for="level in levels"
                  :key="level"
                  :label="$t(`master.personnel_positions.levels.${level.toLowerCase()}`)"
                  :value="level"
                />
              </el-select>
            </el-form-item>

            <!-- Minimum Experience Years -->
            <el-form-item :label="$t('master.personnel_positions.fields.min_experience_years')" prop="min_experience_years">
              <el-input-number
                v-model="formData.min_experience_years"
                :min="0"
                :max="50"
                :step="1"
                class="w-full"
              />
            </el-form-item>

            <!-- Status -->
            <el-form-item :label="$t('common.status')" prop="is_active">
              <el-switch
                v-model="formData.is_active"
                :active-text="$t('common.active')"
                :inactive-text="$t('common.inactive')"
                active-color="#13ce66"
                inactive-color="#ff4949"
              />
            </el-form-item>
          </div>
        </el-card>

        <!-- Position Flags -->
        <el-card class="form-section mt-6" shadow="hover">
          <template #header>
            <div class="flex items-center">
              <Icon icon="mdi:flag" class="text-orange-600 mr-2" :width="20" :height="20" />
              <span class="font-semibold">{{ $t('master.personnel_positions.position_flags') }}</span>
            </div>
          </template>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Essential Position -->
            <el-form-item>
              <template #label>
                <div class="flex items-center">
                  <span>{{ $t('master.personnel_positions.fields.is_essential') }}</span>
                  <el-tooltip content="Position critical for operations" placement="top">
                    <Icon icon="mdi:help-circle" class="ml-1 text-gray-400" :width="16" :height="16" />
                  </el-tooltip>
                </div>
              </template>
              <el-switch
                v-model="formData.is_essential"
                :active-text="$t('common.yes')"
                :inactive-text="$t('common.no')"
                active-color="#e6a23c"
                inactive-color="#dcdfe6"
              />
            </el-form-item>

            <!-- Minimum Manning -->
            <el-form-item>
              <template #label>
                <div class="flex items-center">
                  <span>{{ $t('master.personnel_positions.fields.is_minimum_manning') }}</span>
                  <el-tooltip content="Required for minimum manning requirements" placement="top">
                    <Icon icon="mdi:help-circle" class="ml-1 text-gray-400" :width="16" :height="16" />
                  </el-tooltip>
                </div>
              </template>
              <el-switch
                v-model="formData.is_minimum_manning"
                :active-text="$t('common.yes')"
                :inactive-text="$t('common.no')"
                active-color="#f56c6c"
                inactive-color="#dcdfe6"
              />
            </el-form-item>
          </div>
        </el-card>

        <!-- Required Certificates -->
        <el-card class="form-section mt-6" shadow="hover">
          <template #header>
            <div class="flex items-center">
              <Icon icon="mdi:certificate" class="text-green-600 mr-2" :width="20" :height="20" />
              <span class="font-semibold">{{ $t('master.personnel_positions.required_certificates') }}</span>
            </div>
          </template>

          <div class="space-y-4">
            <!-- Certificate Input -->
            <div class="flex items-center space-x-2">
              <el-input
                v-model="newCertificate"
                :placeholder="$t('master.personnel_positions.certificate_placeholder')"
                @keyup.enter="addCertificate"
                class="flex-1"
              >
                <template #prefix>
                  <Icon icon="mdi:certificate" :width="16" :height="16" />
                </template>
              </el-input>
              <el-button type="primary" @click="addCertificate" :disabled="!newCertificate.trim()">
                <template #icon>
                  <Icon icon="mdi:plus" :width="16" :height="16" />
                </template>
                {{ $t('common.add') }}
              </el-button>
            </div>

            <!-- Certificates List -->
            <div v-if="formData.required_certificates && formData.required_certificates.length > 0" class="space-y-2">
              <div
                v-for="(certificate, index) in formData.required_certificates"
                :key="index"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center">
                  <Icon icon="mdi:certificate" class="text-green-600 mr-2" :width="16" :height="16" />
                  <span>{{ certificate }}</span>
                </div>
                <el-button
                  type="danger"
                  size="small"
                  circle
                  @click="removeCertificate(index)"
                >
                  <template #icon>
                    <Icon icon="mdi:close" :width="14" :height="14" />
                  </template>
                </el-button>
              </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
              <Icon icon="mdi:certificate-outline" :width="48" :height="48" class="mx-auto mb-2 opacity-50" />
              <p>{{ $t('master.personnel_positions.no_certificates') }}</p>
            </div>
          </div>
        </el-card>
      </el-form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

// Reactive data
const formRef = ref()
const isLoading = ref(false)
const newCertificate = ref('')

// Check if this is edit mode
const isEdit = computed(() => !!route.params.id)

// Form data
const formData = reactive({
  code: '',
  name: '',
  department: '',
  level: '',
  is_essential: false,
  is_minimum_manning: false,
  min_experience_years: 0,
  required_certificates: [],
  is_active: true
})

// Constants
const levels = ['Director', 'Manager', 'Supervisor', 'Staff', 'Operator', 'Technician']
const departments = ref([
  'Operations',
  'Maintenance',
  'HSE',
  'Marine',
  'Engineering',
  'Administration',
  'Finance',
  'IT',
  'HR',
  'Catering'
])

// Form validation rules
const formRules = {
  name: [
    { required: true, message: 'Position name is required', trigger: 'blur' },
    { min: 2, max: 100, message: 'Position name must be between 2 and 100 characters', trigger: 'blur' }
  ],
  level: [
    { required: true, message: 'Position level is required', trigger: 'change' }
  ],
  min_experience_years: [
    { required: true, message: 'Minimum experience years is required', trigger: 'blur' },
    { type: 'number', min: 0, max: 50, message: 'Experience years must be between 0 and 50', trigger: 'blur' }
  ]
}

// Methods
const handleBack = () => {
  router.push({ name: 'master.personnel_positions.index' })
}

const handleReset = async () => {
  try {
    await ElMessageBox.confirm(
      'Are you sure you want to reset the form? All unsaved changes will be lost.',
      'Confirm Reset',
      {
        confirmButtonText: 'Reset',
        cancelButtonText: 'Cancel',
        type: 'warning'
      }
    )
    
    if (isEdit.value) {
      await loadPersonnelPosition()
    } else {
      resetForm()
    }
    
    ElMessage.success('Form has been reset')
  } catch (error) {
    // User cancelled
  }
}

const resetForm = () => {
  Object.assign(formData, {
    code: '',
    name: '',
    department: '',
    level: '',
    is_essential: false,
    is_minimum_manning: false,
    min_experience_years: 0,
    required_certificates: [],
    is_active: true
  })
  formRef.value?.clearValidate()
}

const handleSave = async () => {
  try {
    await formRef.value.validate()
    
    isLoading.value = true
    
    const payload = { ...formData }
    
    if (isEdit.value) {
      await axios.put(`/api/master/personnel-positions/${route.params.id}/update`, payload)
      ElMessage.success('Personnel position updated successfully')
    } else {
      await axios.post('/api/master/personnel-positions/store', payload)
      ElMessage.success('Personnel position created successfully')
    }
    
    router.push({ name: 'master.personnel_positions.index' })
  } catch (error) {
    if (error.response?.data?.message) {
      ElMessage.error(error.response.data.message)
    } else {
      ElMessage.error('Failed to save personnel position')
    }
  } finally {
    isLoading.value = false
  }
}

const addCertificate = () => {
  const certificate = newCertificate.value.trim()
  if (certificate && !formData.required_certificates.includes(certificate)) {
    formData.required_certificates.push(certificate)
    newCertificate.value = ''
  }
}

const removeCertificate = (index) => {
  formData.required_certificates.splice(index, 1)
}

const loadPersonnelPosition = async () => {
  try {
    isLoading.value = true
    const response = await axios.get(`/api/master/personnel-positions/${route.params.id}`)
    const data = response.data
    
    Object.assign(formData, {
      code: data.code || '',
      name: data.name || '',
      department: data.department || '',
      level: data.level || '',
      is_essential: data.is_essential || false,
      is_minimum_manning: data.is_minimum_manning || false,
      min_experience_years: data.min_experience_years || 0,
      required_certificates: data.required_certificates || [],
      is_active: data.is_active !== undefined ? data.is_active : true
    })
  } catch (error) {
    ElMessage.error('Failed to load personnel position data')
    router.push({ name: 'master.personnel_positions.index' })
  } finally {
    isLoading.value = false
  }
}

// Lifecycle
onMounted(() => {
  if (isEdit.value) {
    loadPersonnelPosition()
  }
})
</script>

<style scoped>
.personnel-position-form {
  background-color: #f9fafb;
}

.form-section {
  border-radius: 8px;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.personnel-position-form-content :deep(.el-form-item__label) {
  font-weight: 500;
  color: #374151;
}

.personnel-position-form-content :deep(.el-input__wrapper) {
  border-radius: 6px;
}

.personnel-position-form-content :deep(.el-select) {
  width: 100%;
}

.personnel-position-form-content :deep(.el-card__header) {
  background-color: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}
</style>