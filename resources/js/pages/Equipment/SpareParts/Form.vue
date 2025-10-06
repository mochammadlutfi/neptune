<template>
  <div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-title">
        <h1>{{ isEditing ? $t('equipment.sparepart.edit') : $t('equipment.sparepart.create') }}</h1>
        <p class="page-description">
          {{ isEditing ? 'Update spare part information' : 'Add new spare part to inventory' }}
        </p>
      </div>
      <div class="page-actions">
        <el-button @click="handleCancel">
          {{ $t('common.cancel') }}
        </el-button>
        <el-button 
          type="primary" 
          @click="handleSubmit"
          :loading="isSubmitting"
        >
          {{ isEditing ? $t('common.update') : $t('common.create') }}
        </el-button>
      </div>
    </div>

    <!-- Form Content -->
    <el-card class="form-card" shadow="never">
      <el-form
        ref="formRef"
        :model="formData"
        :rules="formRules"
        label-position="top"
        class="spare-part-form"
      >
        <!-- Basic Information Section -->
        <div class="form-section">
          <div class="section-header">
            <h3>{{ $t('equipment.sparepart.sections.basic_info') }}</h3>
            <p>Essential spare part identification and classification</p>
          </div>
          
          <div class="form-grid">
            <el-form-item 
              :label="$t('equipment.sparepart.fields.part_number')" 
              prop="part_number"
              class="form-item-full"
            >
              <el-input
                v-model="formData.part_number"
                :placeholder="$t('equipment.sparepart.placeholder.part_number')"
                maxlength="50"
                show-word-limit
              />
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.part_name')" 
              prop="part_name"
              class="form-item-full"
            >
              <el-input
                v-model="formData.part_name"
                :placeholder="$t('equipment.sparepart.placeholder.part_name')"
                maxlength="100"
                show-word-limit
              />
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.category')" 
              prop="category"
            >
              <el-select
                v-model="formData.category"
                :placeholder="$t('equipment.sparepart.placeholder.category')"
                class="full-width"
              >
                <el-option
                  v-for="(label, value) in categoryOptions"
                  :key="value"
                  :label="label"
                  :value="value"
                />
              </el-select>
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.criticality')" 
              prop="criticality"
            >
              <el-select
                v-model="formData.criticality"
                placeholder="Select criticality level"
                class="full-width"
              >
                <el-option
                  v-for="(label, value) in criticalityOptions"
                  :key="value"
                  :label="label"
                  :value="value"
                />
              </el-select>
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.status')" 
              prop="status"
            >
              <el-select
                v-model="formData.status"
                placeholder="Select status"
                class="full-width"
              >
                <el-option
                  v-for="(label, value) in statusOptions"
                  :key="value"
                  :label="label"
                  :value="value"
                />
              </el-select>
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.unit_of_measure')" 
              prop="unit_of_measure"
            >
              <el-select
                v-model="formData.unit_of_measure"
                :placeholder="$t('equipment.sparepart.placeholder.unit_of_measure')"
                class="full-width"
              >
                <el-option
                  v-for="unit in unitOptions"
                  :key="unit"
                  :label="unit"
                  :value="unit"
                />
              </el-select>
            </el-form-item>
          </div>
        </div>

        <!-- Manufacturer & Supplier Information -->
        <div class="form-section">
          <div class="section-header">
            <h3>{{ $t('equipment.sparepart.sections.supplier_info') }}</h3>
            <p>Manufacturer and supplier details</p>
          </div>
          
          <div class="form-grid">
            <el-form-item 
              :label="$t('equipment.sparepart.fields.manufacturer')" 
              prop="manufacturer"
            >
              <el-input
                v-model="formData.manufacturer"
                :placeholder="$t('equipment.sparepart.placeholder.manufacturer')"
              />
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.model')" 
              prop="model"
            >
              <el-input
                v-model="formData.model"
                :placeholder="$t('equipment.sparepart.placeholder.model')"
              />
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.supplier_name')" 
              prop="supplier_name"
            >
              <el-select
                v-model="formData.supplier_name"
                :placeholder="$t('equipment.sparepart.placeholder.supplier_name')"
                filterable
                allow-create
                class="full-width"
              >
                <el-option
                  v-for="supplier in supplierOptions"
                  :key="supplier"
                  :label="supplier"
                  :value="supplier"
                />
              </el-select>
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.supplier_part_number')" 
              prop="supplier_part_number"
            >
              <el-input
                v-model="formData.supplier_part_number"
                :placeholder="$t('equipment.sparepart.placeholder.supplier_part_number')"
              />
            </el-form-item>
          </div>
        </div>

        <!-- Pricing & Lead Time -->
        <div class="form-section">
          <div class="section-header">
            <h3>{{ $t('equipment.sparepart.sections.pricing') }}</h3>
            <p>Cost and procurement information</p>
          </div>
          
          <div class="form-grid">
            <el-form-item 
              :label="$t('equipment.sparepart.fields.unit_price')" 
              prop="unit_price"
            >
              <el-input
                v-model.number="formData.unit_price"
                :placeholder="$t('equipment.sparepart.placeholder.unit_price')"
                type="number"
                step="0.01"
                min="0"
              >
                <template #prepend>$</template>
              </el-input>
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.currency')" 
              prop="currency"
            >
              <el-select
                v-model="formData.currency"
                placeholder="Select currency"
                class="full-width"
              >
                <el-option label="USD" value="USD" />
                <el-option label="EUR" value="EUR" />
                <el-option label="GBP" value="GBP" />
                <el-option label="IDR" value="IDR" />
              </el-select>
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.lead_time_days')" 
              prop="lead_time_days"
            >
              <el-input
                v-model.number="formData.lead_time_days"
                :placeholder="$t('equipment.sparepart.placeholder.lead_time_days')"
                type="number"
                min="0"
              >
                <template #append>days</template>
              </el-input>
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.minimum_order_quantity')" 
              prop="minimum_order_quantity"
            >
              <el-input
                v-model.number="formData.minimum_order_quantity"
                :placeholder="$t('equipment.sparepart.placeholder.minimum_order_quantity')"
                type="number"
                min="1"
              />
            </el-form-item>
          </div>
        </div>

        <!-- Stock Management -->
        <div class="form-section">
          <div class="section-header">
            <h3>{{ $t('equipment.sparepart.sections.stock_management') }}</h3>
            <p>Inventory levels and storage information</p>
          </div>
          
          <div class="form-grid">
            <el-form-item 
              :label="$t('equipment.sparepart.fields.minimum_stock')" 
              prop="minimum_stock"
            >
              <el-input
                v-model.number="formData.minimum_stock"
                :placeholder="$t('equipment.sparepart.placeholder.minimum_stock')"
                type="number"
                min="0"
              />
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.maximum_stock')" 
              prop="maximum_stock"
            >
              <el-input
                v-model.number="formData.maximum_stock"
                :placeholder="$t('equipment.sparepart.placeholder.maximum_stock')"
                type="number"
                min="0"
              />
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.reorder_point')" 
              prop="reorder_point"
            >
              <el-input
                v-model.number="formData.reorder_point"
                :placeholder="$t('equipment.sparepart.placeholder.reorder_point')"
                type="number"
                min="0"
              />
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.storage_location')" 
              prop="storage_location"
            >
              <el-input
                v-model="formData.storage_location"
                :placeholder="$t('equipment.sparepart.placeholder.storage_location')"
              />
            </el-form-item>
          </div>
        </div>

        <!-- Equipment Compatibility -->
        <div class="form-section">
          <div class="section-header">
            <h3>{{ $t('equipment.sparepart.sections.compatibility') }}</h3>
            <p>Equipment and system compatibility</p>
          </div>
          
          <div class="form-grid">
            <el-form-item 
              :label="$t('equipment.sparepart.fields.equipment_compatibility')" 
              prop="equipment_compatibility"
              class="form-item-full"
            >
              <el-select
                v-model="formData.equipment_compatibility"
                :placeholder="$t('equipment.sparepart.placeholder.equipment_compatibility')"
                multiple
                filterable
                allow-create
                class="full-width"
              >
                <el-option
                  v-for="equipment in equipmentOptions"
                  :key="equipment"
                  :label="equipment"
                  :value="equipment"
                />
              </el-select>
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.interchangeable_parts')" 
              prop="interchangeable_parts"
              class="form-item-full"
            >
              <el-input
                v-model="formData.interchangeable_parts"
                :placeholder="$t('equipment.sparepart.placeholder.interchangeable_parts')"
                type="textarea"
                :rows="3"
              />
            </el-form-item>
          </div>
        </div>

        <!-- Additional Information -->
        <div class="form-section">
          <div class="section-header">
            <h3>{{ $t('equipment.sparepart.sections.additional_info') }}</h3>
            <p>Additional specifications and notes</p>
          </div>
          
          <div class="form-grid">
            <el-form-item 
              :label="$t('equipment.sparepart.fields.specifications')" 
              prop="specifications"
              class="form-item-full"
            >
              <el-input
                v-model="formData.specifications"
                :placeholder="$t('equipment.sparepart.placeholder.specifications')"
                type="textarea"
                :rows="4"
              />
            </el-form-item>

            <el-form-item 
              :label="$t('equipment.sparepart.fields.notes')" 
              prop="notes"
              class="form-item-full"
            >
              <el-input
                v-model="formData.notes"
                :placeholder="$t('equipment.sparepart.placeholder.notes')"
                type="textarea"
                :rows="3"
              />
            </el-form-item>
          </div>
        </div>
      </el-form>

      <!-- Summary Card -->
      <div class="summary-section">
        <el-card class="summary-card" shadow="never">
          <template #header>
            <span class="summary-title">Part Summary</span>
          </template>
          <div class="summary-content">
            <div class="summary-item">
              <span class="summary-label">Part Number:</span>
              <span class="summary-value">{{ formData.part_number || 'Not specified' }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Category:</span>
              <span class="summary-value">{{ categoryOptions[formData.category] || 'Not selected' }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Unit Price:</span>
              <span class="summary-value">${{ formatCurrency(formData.unit_price) }}</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Lead Time:</span>
              <span class="summary-value">{{ formData.lead_time_days || 0 }} days</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Stock Range:</span>
              <span class="summary-value">{{ formData.minimum_stock || 0 }} - {{ formData.maximum_stock || 0 }}</span>
            </div>
          </div>
        </el-card>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'

const router = useRouter()
const route = useRoute()

// State Management
const formRef = ref()
const isSubmitting = ref(false)
const isEditing = computed(() => !!route.params.id)

// Form Data
const formData = reactive({
  part_number: '',
  part_name: '',
  category: '',
  criticality: 'normal',
  status: 'active',
  unit_of_measure: '',
  manufacturer: '',
  model: '',
  supplier_name: '',
  supplier_part_number: '',
  unit_price: 0,
  currency: 'USD',
  lead_time_days: 0,
  minimum_order_quantity: 1,
  minimum_stock: 0,
  maximum_stock: 0,
  reorder_point: 0,
  storage_location: '',
  equipment_compatibility: [],
  interchangeable_parts: '',
  specifications: '',
  notes: ''
})

// Validation Rules
const formRules = reactive({
  part_number: [
    { required: true, message: 'Part number is required', trigger: 'blur' },
    { min: 3, max: 50, message: 'Length should be 3 to 50 characters', trigger: 'blur' }
  ],
  part_name: [
    { required: true, message: 'Part name is required', trigger: 'blur' },
    { min: 3, max: 100, message: 'Length should be 3 to 100 characters', trigger: 'blur' }
  ],
  category: [
    { required: true, message: 'Category is required', trigger: 'change' }
  ],
  criticality: [
    { required: true, message: 'Criticality is required', trigger: 'change' }
  ],
  status: [
    { required: true, message: 'Status is required', trigger: 'change' }
  ],
  unit_of_measure: [
    { required: true, message: 'Unit of measure is required', trigger: 'change' }
  ],
  supplier_name: [
    { required: true, message: 'Supplier name is required', trigger: 'blur' }
  ],
  unit_price: [
    { required: true, message: 'Unit price is required', trigger: 'blur' },
    { type: 'number', min: 0, message: 'Unit price must be greater than 0', trigger: 'blur' }
  ],
  lead_time_days: [
    { required: true, message: 'Lead time is required', trigger: 'blur' },
    { type: 'number', min: 0, message: 'Lead time must be 0 or greater', trigger: 'blur' }
  ],
  minimum_stock: [
    { type: 'number', min: 0, message: 'Minimum stock must be 0 or greater', trigger: 'blur' }
  ],
  maximum_stock: [
    { type: 'number', min: 0, message: 'Maximum stock must be 0 or greater', trigger: 'blur' },
    {
      validator: (rule, value, callback) => {
        if (value && formData.minimum_stock && value < formData.minimum_stock) {
          callback(new Error('Maximum stock must be greater than minimum stock'))
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ],
  reorder_point: [
    { type: 'number', min: 0, message: 'Reorder point must be 0 or greater', trigger: 'blur' }
  ]
})

// Options
const categoryOptions = computed(() => ({
  mechanical: 'Mechanical',
  electrical: 'Electrical',
  instrumentation: 'Instrumentation',
  safety: 'Safety',
  consumables: 'Consumables',
  filters: 'Filters',
  seals_gaskets: 'Seals & Gaskets',
  bearings: 'Bearings',
  valves: 'Valves',
  pumps: 'Pumps'
}))

const criticalityOptions = computed(() => ({
  critical: 'Critical',
  important: 'Important',
  normal: 'Normal',
  low: 'Low'
}))

const statusOptions = computed(() => ({
  active: 'Active',
  discontinued: 'Discontinued',
  obsolete: 'Obsolete'
}))

const unitOptions = ref([
  'Each', 'Piece', 'Set', 'Meter', 'Liter', 'Kilogram', 'Gram', 'Foot', 'Inch', 'Gallon'
])

const supplierOptions = ref([
  'SKF Industrial',
  'Parker Hannifin',
  'Donaldson Company',
  'Emerson Process',
  'Honeywell',
  'Siemens',
  'ABB',
  'Schneider Electric'
])

const equipmentOptions = ref([
  'Main Process Pump',
  'Backup Pump',
  'Safety Relief Valve',
  'Hydraulic System',
  'Lube Oil System',
  'Cooling Water System',
  'Fuel Oil System',
  'Compressed Air System'
])

// Helper Methods
const formatCurrency = (value) => {
  if (!value) return '0.00'
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value)
}

// Data Fetching
const fetchSparePartData = async (id) => {
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    
    // Mock data for editing
    if (id === '1') {
      Object.assign(formData, {
        part_number: 'PMP-001-BRG',
        part_name: 'Main Pump Bearing',
        category: 'bearings',
        criticality: 'critical',
        status: 'active',
        unit_of_measure: 'Each',
        manufacturer: 'SKF',
        model: 'NU 2320 ECM',
        supplier_name: 'SKF Industrial',
        supplier_part_number: 'SKF-NU2320ECM',
        unit_price: 1250.00,
        currency: 'USD',
        lead_time_days: 14,
        minimum_order_quantity: 1,
        minimum_stock: 2,
        maximum_stock: 6,
        reorder_point: 3,
        storage_location: 'A-01-15',
        equipment_compatibility: ['Main Process Pump', 'Backup Pump'],
        interchangeable_parts: 'Compatible with NU 2320 ECP, NU 2320 ECML',
        specifications: 'Inner diameter: 100mm, Outer diameter: 180mm, Width: 46mm',
        notes: 'Critical spare part for main process pump. Keep minimum 2 units in stock.'
      })
    }
  } catch (error) {
    ElMessage.error('Failed to fetch spare part data')
  }
}

// Event Handlers
const handleSubmit = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    
    isSubmitting.value = true
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    ElMessage.success(
      isEditing.value 
        ? 'Spare part updated successfully' 
        : 'Spare part created successfully'
    )
    
    router.push('/equipment/sparepart')
  } catch (error) {
    if (error !== false) { // validation error returns false
      ElMessage.error('Failed to save spare part')
    }
  } finally {
    isSubmitting.value = false
  }
}

const handleCancel = () => {
  router.push('/equipment/sparepart')
}

// Lifecycle
onMounted(() => {
  if (isEditing.value) {
    fetchSparePartData(route.params.id)
  }
})
</script>

<style scoped>
.page-container {
  padding: 24px;
  background-color: #f5f7fa;
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.page-title h1 {
  font-size: 28px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
}

.page-description {
  color: #6b7280;
  font-size: 14px;
  margin: 0;
}

.form-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.spare-part-form {
  max-width: none;
}

.form-section {
  margin-bottom: 32px;
  padding-bottom: 24px;
  border-bottom: 1px solid #e5e7eb;
}

.form-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.section-header {
  margin-bottom: 24px;
}

.section-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
}

.section-header p {
  color: #6b7280;
  font-size: 14px;
  margin: 0;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
}

.form-item-full {
  grid-column: 1 / -1;
}

.full-width {
  width: 100%;
}

.summary-section {
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #e5e7eb;
}

.summary-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.summary-title {
  font-weight: 600;
  color: #1f2937;
}

.summary-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.summary-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.summary-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.summary-value {
  font-size: 14px;
  color: #1f2937;
  font-weight: 500;
}

:deep(.el-form-item__label) {
  font-weight: 500;
  color: #374151;
}

:deep(.el-input__inner) {
  border-radius: 6px;
}

:deep(.el-select) {
  width: 100%;
}

:deep(.el-textarea__inner) {
  border-radius: 6px;
}
</style>