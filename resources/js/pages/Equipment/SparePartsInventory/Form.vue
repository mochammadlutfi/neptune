<template>
  <div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-title">
        <h1>{{ isEdit ? 'Edit' : 'Create' }} {{ $t('equipment.sparepart_inventory.title') }}</h1>
        <p class="page-description">
          {{ isEdit ? 'Update inventory transaction details' : 'Record new spare parts inventory transaction' }}
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
          {{ isEdit ? $t('common.update') : $t('common.create') }}
        </el-button>
      </div>
    </div>

    <!-- Content -->
    <div class="content-layout">
      <!-- Main Form -->
      <div class="main-content">
        <el-card class="form-card" shadow="never">
          <el-form
            ref="formRef"
            :model="formData"
            :rules="formRules"
            label-position="top"
            class="inventory-form"
          >
            <!-- Transaction Type & Basic Info -->
            <div class="form-section">
              <div class="section-header">
                <h3>Transaction Information</h3>
                <p>Select transaction type and basic details</p>
              </div>
              
              <div class="form-grid">
                <el-form-item 
                  label="Transaction Type" 
                  prop="transaction_type"
                  class="col-span-2"
                >
                  <el-radio-group 
                    v-model="formData.transaction_type" 
                    @change="handleTransactionTypeChange"
                    class="transaction-type-group"
                  >
                    <el-radio-button label="stock_in">
                      <el-icon><Plus /></el-icon>
                      Stock In
                    </el-radio-button>
                    <el-radio-button label="stock_out">
                      <el-icon><Minus /></el-icon>
                      Stock Out
                    </el-radio-button>
                    <el-radio-button label="adjustment">
                      <el-icon><Edit /></el-icon>
                      Adjustment
                    </el-radio-button>
                    <el-radio-button label="transfer">
                      <el-icon><Switch /></el-icon>
                      Transfer
                    </el-radio-button>
                  </el-radio-group>
                </el-form-item>

                <el-form-item 
                  :label="$t('equipment.sparepart_inventory.fields.transaction_date')" 
                  prop="transaction_date"
                >
                  <el-date-picker
                    v-model="formData.transaction_date"
                    type="datetime"
                    placeholder="Select date and time"
                    format="YYYY-MM-DD HH:mm"
                    value-format="YYYY-MM-DD HH:mm:ss"
                    style="width: 100%"
                  />
                </el-form-item>

                <el-form-item 
                  :label="$t('equipment.sparepart_inventory.fields.reference_number')" 
                  prop="reference_number"
                >
                  <el-input
                    v-model="formData.reference_number"
                    :placeholder="getReferencePlaceholder()"
                  />
                </el-form-item>
              </div>
            </div>

            <!-- Spare Part Selection -->
            <div class="form-section">
              <div class="section-header">
                <h3>Spare Part Details</h3>
                <p>Select the spare part and specify quantity</p>
              </div>
              
              <div class="form-grid">
                <el-form-item 
                  :label="$t('equipment.sparepart_inventory.fields.part_number')" 
                  prop="part_id"
                  class="col-span-2"
                >
                  <el-select
                    v-model="formData.part_id"
                    filterable
                    remote
                    reserve-keyword
                    placeholder="Search and select spare part"
                    :remote-method="searchSpareParts"
                    :loading="isSearching"
                    @change="handlePartSelection"
                    style="width: 100%"
                  >
                    <el-option
                      v-for="part in sparePartOptions"
                      :key="part.id"
                      :label="`${part.part_number} - ${part.name}`"
                      :value="part.id"
                    >
                      <div class="part-option">
                        <div class="part-main">
                          <span class="part-number">{{ part.part_number }}</span>
                          <span class="part-name">{{ part.name }}</span>
                        </div>
                        <div class="part-details">
                          <span class="current-stock">Stock: {{ part.current_stock }}</span>
                          <span class="unit-price">${{ formatCurrency(part.unit_price) }}</span>
                        </div>
                      </div>
                    </el-option>
                  </el-select>
                </el-form-item>

                <el-form-item 
                  :label="$t('equipment.sparepart_inventory.fields.quantity')" 
                  prop="quantity"
                >
                  <el-input-number
                    v-model="formData.quantity"
                    :min="getQuantityMin()"
                    :max="getQuantityMax()"
                    :step="1"
                    :precision="0"
                    style="width: 100%"
                  />
                </el-form-item>

                <el-form-item 
                  :label="$t('equipment.sparepart_inventory.fields.unit_price')" 
                  prop="unit_price"
                >
                  <el-input-number
                    v-model="formData.unit_price"
                    :min="0"
                    :step="0.01"
                    :precision="2"
                    style="width: 100%"
                  />
                </el-form-item>
              </div>

              <!-- Selected Part Info -->
              <div v-if="selectedPart" class="selected-part-info">
                <div class="part-card">
                  <div class="part-header">
                    <h4>{{ selectedPart.part_number }}</h4>
                    <el-tag :type="getStockLevelType(selectedPart.current_stock, selectedPart.minimum_stock)">
                      Current Stock: {{ selectedPart.current_stock }}
                    </el-tag>
                  </div>
                  <div class="part-details">
                    <p><strong>Name:</strong> {{ selectedPart.name }}</p>
                    <p><strong>Category:</strong> {{ selectedPart.category }}</p>
                    <p><strong>Location:</strong> {{ selectedPart.location }}</p>
                    <p><strong>Unit Price:</strong> ${{ formatCurrency(selectedPart.unit_price) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Location & Additional Details -->
            <div class="form-section">
              <div class="section-header">
                <h3>Location & Additional Information</h3>
                <p>Specify storage location and additional transaction details</p>
              </div>
              
              <div class="form-grid">
                <el-form-item 
                  :label="$t('equipment.sparepart_inventory.fields.location')" 
                  prop="location"
                >
                  <el-select
                    v-model="formData.location"
                    filterable
                    allow-create
                    placeholder="Select or enter location"
                    style="width: 100%"
                  >
                    <el-option
                      v-for="location in locationOptions"
                      :key="location"
                      :label="location"
                      :value="location"
                    />
                  </el-select>
                </el-form-item>

                <el-form-item 
                  label="Performed By" 
                  prop="performed_by"
                >
                  <el-select
                    v-model="formData.performed_by"
                    filterable
                    placeholder="Select operator"
                    style="width: 100%"
                  >
                    <el-option
                      v-for="operator in operatorOptions"
                      :key="operator.id"
                      :label="operator.name"
                      :value="operator.name"
                    />
                  </el-select>
                </el-form-item>

                <!-- Conditional Fields -->
                <el-form-item 
                  v-if="formData.transaction_type === 'stock_in'"
                  label="Supplier" 
                  prop="supplier"
                >
                  <el-input
                    v-model="formData.supplier"
                    placeholder="Enter supplier name"
                  />
                </el-form-item>

                <el-form-item 
                  v-if="formData.transaction_type === 'stock_out'"
                  label="Work Order" 
                  prop="work_order"
                >
                  <el-input
                    v-model="formData.work_order"
                    placeholder="Enter work order number"
                  />
                </el-form-item>

                <el-form-item 
                  v-if="formData.transaction_type === 'adjustment'"
                  label="Adjustment Reason" 
                  prop="reason"
                >
                  <el-select
                    v-model="formData.reason"
                    placeholder="Select reason"
                    style="width: 100%"
                  >
                    <el-option label="Physical Count Adjustment" value="physical_count" />
                    <el-option label="Damaged/Lost" value="damaged" />
                    <el-option label="Expired" value="expired" />
                    <el-option label="System Correction" value="system_correction" />
                    <el-option label="Other" value="other" />
                  </el-select>
                </el-form-item>

                <el-form-item 
                  v-if="formData.transaction_type === 'transfer'"
                  label="Transfer To" 
                  prop="transfer_to"
                >
                  <el-input
                    v-model="formData.transfer_to"
                    placeholder="Enter destination location"
                  />
                </el-form-item>

                <el-form-item 
                  :label="$t('equipment.sparepart_inventory.fields.notes')" 
                  prop="notes"
                  class="col-span-2"
                >
                  <el-input
                    v-model="formData.notes"
                    type="textarea"
                    :rows="3"
                    placeholder="Enter additional notes or comments"
                  />
                </el-form-item>
              </div>
            </div>
          </el-form>
        </el-card>
      </div>

      <!-- Summary Sidebar -->
      <div class="sidebar">
        <el-card class="summary-card" shadow="never">
          <template #header>
            <h3>Transaction Summary</h3>
          </template>
          
          <div class="summary-content">
            <div class="summary-item">
              <span class="label">Transaction Type:</span>
              <el-tag 
                v-if="formData.transaction_type" 
                :type="getTransactionTypeTagType(formData.transaction_type)"
                size="small"
              >
                {{ transactionTypeLabels[formData.transaction_type] }}
              </el-tag>
              <span v-else class="empty-value">Not selected</span>
            </div>

            <div class="summary-item" v-if="selectedPart">
              <span class="label">Part:</span>
              <div class="part-summary">
                <div class="part-number">{{ selectedPart.part_number }}</div>
                <div class="part-name">{{ selectedPart.name }}</div>
              </div>
            </div>

            <div class="summary-item" v-if="formData.quantity">
              <span class="label">Quantity:</span>
              <span class="quantity-value" :class="getQuantityClass()">
                {{ getQuantityDisplay() }}
              </span>
            </div>

            <div class="summary-item" v-if="formData.unit_price">
              <span class="label">Unit Price:</span>
              <span class="price-value">${{ formatCurrency(formData.unit_price) }}</span>
            </div>

            <div class="summary-item" v-if="totalValue > 0">
              <span class="label">Total Value:</span>
              <span class="total-value">${{ formatCurrency(totalValue) }}</span>
            </div>

            <div class="summary-item" v-if="selectedPart && formData.quantity">
              <span class="label">Stock After Transaction:</span>
              <span class="stock-after">{{ calculateStockAfter() }}</span>
            </div>

            <!-- Warnings -->
            <div v-if="hasWarnings" class="warnings-section">
              <h4>Warnings</h4>
              <div v-for="warning in warnings" :key="warning.type" class="warning-item">
                <el-icon class="warning-icon"><Warning /></el-icon>
                <span>{{ warning.message }}</span>
              </div>
            </div>
          </div>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  Plus, Minus, Edit, Switch, Warning 
} from '@element-plus/icons-vue'

const router = useRouter()
const route = useRoute()

// State Management
const formRef = ref()
const isSubmitting = ref(false)
const isSearching = ref(false)
const sparePartOptions = ref([])
const selectedPart = ref(null)

// Check if editing
const isEdit = computed(() => route.params.id && route.params.id !== 'create')

// Form Data
const formData = reactive({
  transaction_type: 'stock_in',
  transaction_date: new Date().toISOString().slice(0, 19),
  part_id: '',
  quantity: 1,
  unit_price: 0,
  location: '',
  performed_by: '',
  reference_number: '',
  supplier: '',
  work_order: '',
  reason: '',
  transfer_to: '',
  notes: ''
})

// Form Validation Rules
const formRules = reactive({
  transaction_type: [
    { required: true, message: 'Please select transaction type', trigger: 'change' }
  ],
  transaction_date: [
    { required: true, message: 'Please select transaction date', trigger: 'change' }
  ],
  part_id: [
    { required: true, message: 'Please select a spare part', trigger: 'change' }
  ],
  quantity: [
    { required: true, message: 'Please enter quantity', trigger: 'blur' },
    { type: 'number', min: 1, message: 'Quantity must be greater than 0', trigger: 'blur' }
  ],
  unit_price: [
    { required: true, message: 'Please enter unit price', trigger: 'blur' },
    { type: 'number', min: 0, message: 'Unit price must be greater than or equal to 0', trigger: 'blur' }
  ],
  location: [
    { required: true, message: 'Please select or enter location', trigger: 'blur' }
  ],
  performed_by: [
    { required: true, message: 'Please select operator', trigger: 'change' }
  ],
  supplier: [
    { required: true, message: 'Please enter supplier name', trigger: 'blur' }
  ],
  work_order: [
    { required: true, message: 'Please enter work order number', trigger: 'blur' }
  ],
  reason: [
    { required: true, message: 'Please select adjustment reason', trigger: 'change' }
  ],
  transfer_to: [
    { required: true, message: 'Please enter transfer destination', trigger: 'blur' }
  ]
})

// Mock Data
const mockSpareParts = ref([
  {
    id: 1,
    part_number: 'PMP-001-BRG',
    name: 'Main Pump Bearing',
    category: 'Bearings',
    current_stock: 5,
    minimum_stock: 2,
    unit_price: 1250.00,
    location: 'A-01-15'
  },
  {
    id: 2,
    part_number: 'VLV-002-SL',
    name: 'Safety Relief Valve Seal',
    category: 'Seals & Gaskets',
    current_stock: 12,
    minimum_stock: 5,
    unit_price: 85.50,
    location: 'B-02-08'
  },
  {
    id: 3,
    part_number: 'FLT-003-CRT',
    name: 'Oil Filter Cartridge',
    category: 'Filters',
    current_stock: 8,
    minimum_stock: 10,
    unit_price: 45.75,
    location: 'C-01-12'
  }
])

const locationOptions = ref([
  'A-01-15', 'A-02-10', 'B-01-05', 'B-02-08', 'C-01-12', 'C-02-20',
  'Workshop-01', 'Workshop-02', 'Emergency-Stock'
])

const operatorOptions = ref([
  { id: 1, name: 'John Smith' },
  { id: 2, name: 'Mike Johnson' },
  { id: 3, name: 'Sarah Wilson' },
  { id: 4, name: 'David Brown' }
])

// Computed Properties
const transactionTypeLabels = computed(() => ({
  stock_in: 'Stock In',
  stock_out: 'Stock Out',
  adjustment: 'Adjustment',
  transfer: 'Transfer'
}))

const totalValue = computed(() => {
  return (formData.quantity || 0) * (formData.unit_price || 0)
})

const warnings = computed(() => {
  const warningList = []
  
  if (selectedPart.value && formData.quantity) {
    const stockAfter = calculateStockAfter()
    
    if (formData.transaction_type === 'stock_out' && stockAfter < 0) {
      warningList.push({
        type: 'negative_stock',
        message: 'This transaction will result in negative stock'
      })
    }
    
    if (stockAfter < selectedPart.value.minimum_stock) {
      warningList.push({
        type: 'below_minimum',
        message: 'Stock will be below minimum level after this transaction'
      })
    }
  }
  
  return warningList
})

const hasWarnings = computed(() => warnings.value.length > 0)

// Helper Methods
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(Math.abs(value))
}

const getTransactionTypeTagType = (type) => {
  const typeMap = {
    stock_in: 'success',
    stock_out: 'warning',
    adjustment: 'info',
    transfer: 'primary'
  }
  return typeMap[type] || ''
}

const getReferencePlaceholder = () => {
  const placeholders = {
    stock_in: 'e.g., PO-2024-001',
    stock_out: 'e.g., WO-2024-015',
    adjustment: 'e.g., ADJ-2024-003',
    transfer: 'e.g., TRF-2024-008'
  }
  return placeholders[formData.transaction_type] || 'Enter reference number'
}

const getQuantityMin = () => {
  return formData.transaction_type === 'adjustment' ? -999 : 1
}

const getQuantityMax = () => {
  if (formData.transaction_type === 'stock_out' && selectedPart.value) {
    return selectedPart.value.current_stock
  }
  return 999
}

const getStockLevelType = (current, minimum) => {
  if (current <= 0) return 'danger'
  if (current <= minimum) return 'warning'
  return 'success'
}

const getQuantityClass = () => {
  if (formData.transaction_type === 'stock_in') return 'quantity-positive'
  if (formData.transaction_type === 'stock_out') return 'quantity-negative'
  if (formData.transaction_type === 'adjustment') {
    return formData.quantity > 0 ? 'quantity-positive' : 'quantity-negative'
  }
  return 'quantity-neutral'
}

const getQuantityDisplay = () => {
  if (formData.transaction_type === 'stock_in') {
    return `+${formData.quantity}`
  } else if (formData.transaction_type === 'stock_out') {
    return `-${formData.quantity}`
  } else if (formData.transaction_type === 'adjustment') {
    return formData.quantity > 0 ? `+${formData.quantity}` : `${formData.quantity}`
  }
  return formData.quantity.toString()
}

const calculateStockAfter = () => {
  if (!selectedPart.value || !formData.quantity) return 0
  
  let change = 0
  if (formData.transaction_type === 'stock_in') {
    change = formData.quantity
  } else if (formData.transaction_type === 'stock_out') {
    change = -formData.quantity
  } else if (formData.transaction_type === 'adjustment') {
    change = formData.quantity
  }
  
  return selectedPart.value.current_stock + change
}

// Event Handlers
const handleTransactionTypeChange = () => {
  // Update form rules based on transaction type
  updateFormRules()
}

const updateFormRules = () => {
  // Clear conditional rules
  delete formRules.supplier
  delete formRules.work_order
  delete formRules.reason
  delete formRules.transfer_to
  
  // Add rules based on transaction type
  if (formData.transaction_type === 'stock_in') {
    formRules.supplier = [
      { required: true, message: 'Please enter supplier name', trigger: 'blur' }
    ]
  } else if (formData.transaction_type === 'stock_out') {
    formRules.work_order = [
      { required: true, message: 'Please enter work order number', trigger: 'blur' }
    ]
  } else if (formData.transaction_type === 'adjustment') {
    formRules.reason = [
      { required: true, message: 'Please select adjustment reason', trigger: 'change' }
    ]
  } else if (formData.transaction_type === 'transfer') {
    formRules.transfer_to = [
      { required: true, message: 'Please enter transfer destination', trigger: 'blur' }
    ]
  }
}

const searchSpareParts = (query) => {
  if (query) {
    isSearching.value = true
    setTimeout(() => {
      sparePartOptions.value = mockSpareParts.value.filter(part =>
        part.part_number.toLowerCase().includes(query.toLowerCase()) ||
        part.name.toLowerCase().includes(query.toLowerCase())
      )
      isSearching.value = false
    }, 200)
  } else {
    sparePartOptions.value = mockSpareParts.value
  }
}

const handlePartSelection = (partId) => {
  selectedPart.value = mockSpareParts.value.find(part => part.id === partId)
  if (selectedPart.value) {
    formData.unit_price = selectedPart.value.unit_price
    formData.location = selectedPart.value.location
  }
}

const handleSubmit = async () => {
  try {
    await formRef.value.validate()
    
    if (hasWarnings.value) {
      await ElMessageBox.confirm(
        'There are warnings for this transaction. Do you want to continue?',
        'Confirm Transaction',
        {
          confirmButtonText: 'Continue',
          cancelButtonText: 'Cancel',
          type: 'warning'
        }
      )
    }
    
    isSubmitting.value = true
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    ElMessage.success(
      isEdit.value 
        ? 'Inventory transaction updated successfully' 
        : 'Inventory transaction created successfully'
    )
    
    router.push('/equipment/sparepart-inventory')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to save inventory transaction')
    }
  } finally {
    isSubmitting.value = false
  }
}

const handleCancel = () => {
  router.push('/equipment/sparepart-inventory')
}

// Lifecycle
onMounted(() => {
  // Initialize spare parts options
  sparePartOptions.value = mockSpareParts.value
  
  // Load data if editing
  if (isEdit.value) {
    loadTransactionData()
  }
  
  // Update form rules
  updateFormRules()
})

const loadTransactionData = async () => {
  try {
    // Simulate API call to load transaction data
    const mockTransaction = {
      id: route.params.id,
      transaction_type: 'stock_in',
      transaction_date: '2024-01-20 10:00:00',
      part_id: 1,
      quantity: 2,
      unit_price: 1250.00,
      location: 'A-01-15',
      performed_by: 'John Smith',
      reference_number: 'PO-2024-001',
      supplier: 'SKF Industrial',
      notes: 'Regular stock replenishment'
    }
    
    Object.assign(formData, mockTransaction)
    handlePartSelection(mockTransaction.part_id)
  } catch (error) {
    ElMessage.error('Failed to load transaction data')
  }
}

// Watchers
watch(() => formData.transaction_type, updateFormRules)
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

.content-layout {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 24px;
}

.main-content {
  min-width: 0;
}

.form-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.inventory-form {
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
  margin-bottom: 20px;
}

.section-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.section-header p {
  color: #6b7280;
  font-size: 14px;
  margin: 0;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.col-span-2 {
  grid-column: span 2;
}

.transaction-type-group {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.transaction-type-group .el-radio-button {
  margin-right: 0;
}

.part-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.part-main {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.part-number {
  font-weight: 500;
  color: #1f2937;
}

.part-name {
  font-size: 12px;
  color: #6b7280;
}

.part-details {
  display: flex;
  flex-direction: column;
  gap: 2px;
  text-align: right;
}

.current-stock {
  font-size: 12px;
  color: #059669;
}

.unit-price {
  font-size: 12px;
  color: #6b7280;
}

.selected-part-info {
  margin-top: 16px;
}

.part-card {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
}

.part-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.part-header h4 {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.part-details p {
  margin: 4px 0;
  font-size: 14px;
  color: #4b5563;
}

.sidebar {
  position: sticky;
  top: 24px;
  height: fit-content;
}

.summary-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.summary-card :deep(.el-card__header) {
  padding: 20px 20px 0 20px;
  border-bottom: none;
}

.summary-card h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.summary-content {
  padding: 0 20px 20px 20px;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f3f4f6;
}

.summary-item:last-child {
  border-bottom: none;
  margin-bottom: 0;
}

.summary-item .label {
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.empty-value {
  font-size: 14px;
  color: #9ca3af;
  font-style: italic;
}

.part-summary {
  text-align: right;
}

.part-number {
  font-weight: 500;
  color: #1f2937;
  display: block;
}

.part-name {
  font-size: 12px;
  color: #6b7280;
}

.quantity-positive {
  color: #16a34a;
  font-weight: 500;
}

.quantity-negative {
  color: #dc2626;
  font-weight: 500;
}

.quantity-neutral {
  color: #1f2937;
  font-weight: 500;
}

.price-value {
  font-weight: 500;
  color: #059669;
}

.total-value {
  font-weight: 600;
  color: #1f2937;
  font-size: 16px;
}

.stock-after {
  font-weight: 500;
  color: #1f2937;
}

.warnings-section {
  margin-top: 20px;
  padding-top: 16px;
  border-top: 1px solid #f3f4f6;
}

.warnings-section h4 {
  font-size: 14px;
  font-weight: 600;
  color: #dc2626;
  margin: 0 0 12px 0;
}

.warning-item {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  font-size: 13px;
  color: #dc2626;
}

.warning-icon {
  font-size: 16px;
}

@media (max-width: 1024px) {
  .content-layout {
    grid-template-columns: 1fr;
  }
  
  .sidebar {
    position: static;
  }
}

@media (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .col-span-2 {
    grid-column: span 1;
  }
  
  .transaction-type-group {
    flex-direction: column;
  }
}
</style>