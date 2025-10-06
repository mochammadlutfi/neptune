<template>
  <div class="page-container">
    <!-- Loading State -->
    <div v-if="isLoading" class="loading-container">
      <el-skeleton :rows="8" animated />
    </div>

    <!-- Content -->
    <div v-else class="content-wrapper">
      <!-- Page Header -->
      <div class="page-header">
        <div class="header-left">
          <el-button 
            :icon="ArrowLeft" 
            @click="handleBack"
            class="back-button"
          >
            Back
          </el-button>
          <div class="page-title">
            <h1>{{ sparePartData.part_name }}</h1>
            <div class="page-meta">
              <span class="part-number">{{ sparePartData.part_number }}</span>
              <el-tag 
                :type="getCriticalityTagType(sparePartData.criticality)" 
                size="small"
              >
                {{ criticalityOptions[sparePartData.criticality] }}
              </el-tag>
              <el-tag 
                :type="getStatusTagType(sparePartData.status)" 
                size="small"
              >
                {{ statusOptions[sparePartData.status] }}
              </el-tag>
            </div>
          </div>
        </div>
        <div class="header-actions">
          <el-button 
            type="warning" 
            :icon="Edit"
            @click="handleEdit"
          >
            {{ $t('common.edit') }}
          </el-button>
          <el-button 
            type="danger" 
            :icon="Delete"
            @click="handleDelete"
          >
            {{ $t('common.delete') }}
          </el-button>
        </div>
      </div>

      <div class="content-layout">
        <!-- Main Content -->
        <div class="main-content">
          <!-- Basic Information -->
          <el-card class="info-card" shadow="never">
            <template #header>
              <div class="card-header">
                <span class="card-title">{{ $t('equipment.sparepart.sections.basic_info') }}</span>
              </div>
            </template>
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.part_number') }}</span>
                <span class="info-value">{{ sparePartData.part_number }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.part_name') }}</span>
                <span class="info-value">{{ sparePartData.part_name }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.category') }}</span>
                <span class="info-value">
                  <el-tag :type="getCategoryTagType(sparePartData.category)" size="small">
                    {{ categoryOptions[sparePartData.category] }}
                  </el-tag>
                </span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.unit_of_measure') }}</span>
                <span class="info-value">{{ sparePartData.unit_of_measure }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.criticality') }}</span>
                <span class="info-value">
                  <el-tag :type="getCriticalityTagType(sparePartData.criticality)" size="small">
                    {{ criticalityOptions[sparePartData.criticality] }}
                  </el-tag>
                </span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.status') }}</span>
                <span class="info-value">
                  <el-tag :type="getStatusTagType(sparePartData.status)" size="small">
                    {{ statusOptions[sparePartData.status] }}
                  </el-tag>
                </span>
              </div>
            </div>
          </el-card>

          <!-- Manufacturer & Supplier Information -->
          <el-card class="info-card" shadow="never">
            <template #header>
              <div class="card-header">
                <span class="card-title">{{ $t('equipment.sparepart.sections.supplier_info') }}</span>
              </div>
            </template>
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.manufacturer') }}</span>
                <span class="info-value">{{ sparePartData.manufacturer || 'Not specified' }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.model') }}</span>
                <span class="info-value">{{ sparePartData.model || 'Not specified' }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.supplier_name') }}</span>
                <span class="info-value">{{ sparePartData.supplier_name }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.supplier_part_number') }}</span>
                <span class="info-value">{{ sparePartData.supplier_part_number || 'Not specified' }}</span>
              </div>
            </div>
          </el-card>

          <!-- Pricing & Procurement -->
          <el-card class="info-card" shadow="never">
            <template #header>
              <div class="card-header">
                <span class="card-title">{{ $t('equipment.sparepart.sections.pricing') }}</span>
              </div>
            </template>
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.unit_price') }}</span>
                <span class="info-value price-value">
                  {{ sparePartData.currency }} {{ formatCurrency(sparePartData.unit_price) }}
                </span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.lead_time_days') }}</span>
                <span class="info-value">{{ sparePartData.lead_time_days }} days</span>
              </div>
              <div class="info-item">
                <span class="info-label">{{ $t('equipment.sparepart.fields.minimum_order_quantity') }}</span>
                <span class="info-value">{{ sparePartData.minimum_order_quantity }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Last Updated</span>
                <span class="info-value">{{ formatDate(sparePartData.updated_at) }}</span>
              </div>
            </div>
          </el-card>

          <!-- Stock Management -->
          <el-card class="info-card" shadow="never">
            <template #header>
              <div class="card-header">
                <span class="card-title">{{ $t('equipment.sparepart.sections.stock_management') }}</span>
              </div>
            </template>
            <div class="stock-info">
              <div class="stock-levels">
                <div class="stock-item">
                  <div class="stock-label">Current Stock</div>
                  <div class="stock-value current">{{ sparePartData.current_stock || 0 }}</div>
                </div>
                <div class="stock-item">
                  <div class="stock-label">Minimum Stock</div>
                  <div class="stock-value minimum">{{ sparePartData.minimum_stock }}</div>
                </div>
                <div class="stock-item">
                  <div class="stock-label">Maximum Stock</div>
                  <div class="stock-value maximum">{{ sparePartData.maximum_stock }}</div>
                </div>
                <div class="stock-item">
                  <div class="stock-label">Reorder Point</div>
                  <div class="stock-value reorder">{{ sparePartData.reorder_point }}</div>
                </div>
              </div>
              
              <div class="stock-details">
                <div class="info-item">
                  <span class="info-label">{{ $t('equipment.sparepart.fields.storage_location') }}</span>
                  <span class="info-value">{{ sparePartData.storage_location || 'Not specified' }}</span>
                </div>
                <div class="info-item">
                  <span class="info-label">Stock Status</span>
                  <span class="info-value">
                    <el-tag :type="getStockStatusType()" size="small">
                      {{ getStockStatus() }}
                    </el-tag>
                  </span>
                </div>
              </div>
            </div>
          </el-card>

          <!-- Equipment Compatibility -->
          <el-card class="info-card" shadow="never">
            <template #header>
              <div class="card-header">
                <span class="card-title">{{ $t('equipment.sparepart.sections.compatibility') }}</span>
              </div>
            </template>
            <div class="compatibility-content">
              <div class="info-item full-width">
                <span class="info-label">{{ $t('equipment.sparepart.fields.equipment_compatibility') }}</span>
                <div class="equipment-tags">
                  <el-tag
                    v-for="equipment in sparePartData.equipment_compatibility"
                    :key="equipment"
                    type="info"
                    size="small"
                    class="equipment-tag"
                  >
                    {{ equipment }}
                  </el-tag>
                  <span v-if="!sparePartData.equipment_compatibility?.length" class="no-data">
                    No equipment specified
                  </span>
                </div>
              </div>
              
              <div class="info-item full-width" v-if="sparePartData.interchangeable_parts">
                <span class="info-label">{{ $t('equipment.sparepart.fields.interchangeable_parts') }}</span>
                <span class="info-value">{{ sparePartData.interchangeable_parts }}</span>
              </div>
            </div>
          </el-card>

          <!-- Specifications & Notes -->
          <el-card class="info-card" shadow="never">
            <template #header>
              <div class="card-header">
                <span class="card-title">{{ $t('equipment.sparepart.sections.additional_info') }}</span>
              </div>
            </template>
            <div class="additional-info">
              <div class="info-item full-width" v-if="sparePartData.specifications">
                <span class="info-label">{{ $t('equipment.sparepart.fields.specifications') }}</span>
                <div class="info-value specifications">{{ sparePartData.specifications }}</div>
              </div>
              
              <div class="info-item full-width" v-if="sparePartData.notes">
                <span class="info-label">{{ $t('equipment.sparepart.fields.notes') }}</span>
                <div class="info-value notes">{{ sparePartData.notes }}</div>
              </div>
            </div>
          </el-card>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Quick Actions -->
          <el-card class="sidebar-card" shadow="never">
            <template #header>
              <span class="sidebar-title">Quick Actions</span>
            </template>
            <div class="quick-actions">
              <el-button 
                type="primary" 
                :icon="Plus" 
                @click="handleCreateInventoryTransaction"
                class="action-button"
              >
                Add to Inventory
              </el-button>
              <el-button 
                type="warning" 
                :icon="Edit" 
                @click="handleEdit"
                class="action-button"
              >
                Edit Part
              </el-button>
              <el-button 
                type="info" 
                :icon="Document" 
                @click="handleViewHistory"
                class="action-button"
              >
                View History
              </el-button>
              <el-button 
                type="success" 
                :icon="Download" 
                @click="handleExport"
                class="action-button"
              >
                Export Data
              </el-button>
            </div>
          </el-card>

          <!-- Stock Alert -->
          <el-card 
            v-if="showStockAlert" 
            class="sidebar-card alert-card" 
            shadow="never"
          >
            <template #header>
              <span class="sidebar-title">
                <el-icon class="alert-icon"><Warning /></el-icon>
                Stock Alert
              </span>
            </template>
            <div class="alert-content">
              <p>{{ getStockAlertMessage() }}</p>
              <el-button 
                type="primary" 
                size="small" 
                @click="handleReorder"
                class="alert-action"
              >
                Create Purchase Order
              </el-button>
            </div>
          </el-card>

          <!-- Recent Transactions -->
          <el-card class="sidebar-card" shadow="never">
            <template #header>
              <span class="sidebar-title">Recent Transactions</span>
            </template>
            <div class="recent-transactions">
              <div 
                v-for="transaction in recentTransactions" 
                :key="transaction.id"
                class="transaction-item"
              >
                <div class="transaction-header">
                  <span class="transaction-type">{{ transaction.type }}</span>
                  <span class="transaction-date">{{ formatDate(transaction.date) }}</span>
                </div>
                <div class="transaction-details">
                  <span class="transaction-quantity">{{ transaction.quantity }} units</span>
                  <span class="transaction-user">by {{ transaction.user }}</span>
                </div>
              </div>
              <div v-if="!recentTransactions.length" class="no-transactions">
                No recent transactions
              </div>
            </div>
          </el-card>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  ArrowLeft, Edit, Delete, Plus, Document, Download, Warning 
} from '@element-plus/icons-vue'

const router = useRouter()
const route = useRoute()

// State Management
const isLoading = ref(true)
const sparePartData = ref({})
const recentTransactions = ref([])

// Mock Data
const mockSparePartData = {
  id: 1,
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
  current_stock: 1,
  storage_location: 'A-01-15',
  equipment_compatibility: ['Main Process Pump', 'Backup Pump'],
  interchangeable_parts: 'Compatible with NU 2320 ECP, NU 2320 ECML',
  specifications: 'Inner diameter: 100mm, Outer diameter: 180mm, Width: 46mm, Material: Chrome Steel, Cage: Brass, Lubrication: Standard grease',
  notes: 'Critical spare part for main process pump. Keep minimum 2 units in stock. Requires special handling during installation.',
  created_at: '2024-01-15T08:00:00Z',
  updated_at: '2024-01-20T14:30:00Z'
}

const mockTransactions = [
  {
    id: 1,
    type: 'Stock In',
    quantity: 2,
    date: '2024-01-20T10:00:00Z',
    user: 'John Smith'
  },
  {
    id: 2,
    type: 'Stock Out',
    quantity: 1,
    date: '2024-01-18T15:30:00Z',
    user: 'Mike Johnson'
  },
  {
    id: 3,
    type: 'Stock In',
    quantity: 3,
    date: '2024-01-15T09:15:00Z',
    user: 'Sarah Wilson'
  }
]

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

// Computed Properties
const showStockAlert = computed(() => {
  const current = sparePartData.value.current_stock || 0
  const reorderPoint = sparePartData.value.reorder_point || 0
  return current <= reorderPoint
})

// Helper Methods
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getCategoryTagType = (category) => {
  const typeMap = {
    mechanical: 'primary',
    electrical: 'warning',
    instrumentation: 'info',
    safety: 'danger',
    consumables: 'success',
    filters: '',
    seals_gaskets: 'primary',
    bearings: 'warning',
    valves: 'info',
    pumps: 'success'
  }
  return typeMap[category] || ''
}

const getCriticalityTagType = (criticality) => {
  const typeMap = {
    critical: 'danger',
    important: 'warning',
    normal: 'info',
    low: 'success'
  }
  return typeMap[criticality] || ''
}

const getStatusTagType = (status) => {
  const typeMap = {
    active: 'success',
    discontinued: 'warning',
    obsolete: 'danger'
  }
  return typeMap[status] || ''
}

const getStockStatus = () => {
  const current = sparePartData.value.current_stock || 0
  const minimum = sparePartData.value.minimum_stock || 0
  const reorderPoint = sparePartData.value.reorder_point || 0
  
  if (current === 0) return 'Out of Stock'
  if (current <= reorderPoint) return 'Low Stock'
  if (current < minimum) return 'Below Minimum'
  return 'In Stock'
}

const getStockStatusType = () => {
  const current = sparePartData.value.current_stock || 0
  const minimum = sparePartData.value.minimum_stock || 0
  const reorderPoint = sparePartData.value.reorder_point || 0
  
  if (current === 0) return 'danger'
  if (current <= reorderPoint) return 'warning'
  if (current < minimum) return 'warning'
  return 'success'
}

const getStockAlertMessage = () => {
  const current = sparePartData.value.current_stock || 0
  const reorderPoint = sparePartData.value.reorder_point || 0
  
  if (current === 0) {
    return 'This part is out of stock. Immediate reorder required.'
  }
  if (current <= reorderPoint) {
    return `Stock level (${current}) is at or below reorder point (${reorderPoint}). Consider reordering.`
  }
  return ''
}

// Data Fetching
const fetchSparePartData = async () => {
  isLoading.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 800))
    
    sparePartData.value = mockSparePartData
    recentTransactions.value = mockTransactions
  } catch (error) {
    ElMessage.error('Failed to fetch spare part data')
  } finally {
    isLoading.value = false
  }
}

// Event Handlers
const handleBack = () => {
  router.push('/equipment/sparepart')
}

const handleEdit = () => {
  router.push(`/equipment/sparepart/${route.params.id}/edit`)
}

const handleDelete = async () => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete spare part "${sparePartData.value.part_name}"?`,
      'Confirm Delete',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning',
        confirmButtonClass: 'el-button--danger'
      }
    )
    
    ElMessage.success('Spare part deleted successfully')
    router.push('/equipment/sparepart')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete spare part')
    }
  }
}

const handleCreateInventoryTransaction = () => {
  router.push(`/equipment/sparepart-inventory/create?part_id=${route.params.id}`)
}

const handleViewHistory = () => {
  router.push(`/equipment/sparepart/${route.params.id}/history`)
}

const handleExport = () => {
  ElMessage.success('Exporting spare part data...')
}

const handleReorder = () => {
  ElMessage.success('Creating purchase order...')
}

// Lifecycle
onMounted(() => {
  fetchSparePartData()
})
</script>

<style scoped>
.page-container {
  padding: 24px;
  background-color: #f5f7fa;
  min-height: 100vh;
}

.loading-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px;
}

.content-wrapper {
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-left {
  display: flex;
  align-items: flex-start;
  gap: 16px;
}

.back-button {
  margin-top: 4px;
}

.page-title h1 {
  font-size: 28px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
}

.page-meta {
  display: flex;
  align-items: center;
  gap: 12px;
}

.part-number {
  font-size: 16px;
  color: #6b7280;
  font-weight: 500;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.content-layout {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 24px;
}

.main-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.info-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.info-item.full-width {
  grid-column: 1 / -1;
}

.info-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.info-value {
  font-size: 14px;
  color: #1f2937;
  font-weight: 500;
}

.price-value {
  color: #059669;
  font-weight: 600;
}

.stock-info {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.stock-levels {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 16px;
}

.stock-item {
  text-align: center;
  padding: 16px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.stock-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 8px;
}

.stock-value {
  font-size: 24px;
  font-weight: 600;
}

.stock-value.current {
  color: #2563eb;
}

.stock-value.minimum {
  color: #dc2626;
}

.stock-value.maximum {
  color: #16a34a;
}

.stock-value.reorder {
  color: #ca8a04;
}

.stock-details {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
}

.compatibility-content,
.additional-info {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.equipment-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.equipment-tag {
  margin: 0;
}

.no-data {
  color: #9ca3af;
  font-style: italic;
}

.specifications,
.notes {
  background: #f8fafc;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  line-height: 1.6;
  white-space: pre-wrap;
}

.sidebar {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.sidebar-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.sidebar-title {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
}

.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.action-button {
  width: 100%;
  justify-content: flex-start;
}

.alert-card {
  border-left: 4px solid #f59e0b;
}

.alert-icon {
  color: #f59e0b;
  margin-right: 8px;
}

.alert-content p {
  margin: 0 0 16px 0;
  color: #6b7280;
  line-height: 1.5;
}

.alert-action {
  width: 100%;
}

.recent-transactions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.transaction-item {
  padding: 12px;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.transaction-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}

.transaction-type {
  font-weight: 500;
  color: #1f2937;
  font-size: 14px;
}

.transaction-date {
  font-size: 12px;
  color: #6b7280;
}

.transaction-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  color: #6b7280;
}

.no-transactions {
  text-align: center;
  color: #9ca3af;
  font-style: italic;
  padding: 24px;
}

@media (max-width: 1024px) {
  .content-layout {
    grid-template-columns: 1fr;
  }
  
  .sidebar {
    order: -1;
  }
}
</style>