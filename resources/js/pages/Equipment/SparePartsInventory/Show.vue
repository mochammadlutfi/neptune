<template>
  <div class="page-container">
    <!-- Loading State -->
    <div v-if="isLoading" class="loading-container">
      <el-skeleton :rows="8" animated />
    </div>

    <!-- Content -->
    <div v-else class="content-layout">
      <!-- Main Content -->
      <div class="main-content">
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
              <h1>Inventory Transaction Details</h1>
              <div class="transaction-meta">
                <el-tag :type="getTransactionTypeTagType(transactionData.transaction_type)" size="large">
                  {{ transactionTypeLabels[transactionData.transaction_type] }}
                </el-tag>
                <span class="transaction-id">#{{ transactionData.id }}</span>
                <span class="transaction-date">{{ formatDateTime(transactionData.transaction_date) }}</span>
              </div>
            </div>
          </div>
          <div class="header-actions">
            <el-button 
              v-if="canEdit"
              type="primary" 
              :icon="Edit"
              @click="handleEdit"
            >
              Edit
            </el-button>
            <el-button 
              v-if="canDelete"
              type="danger" 
              :icon="Delete"
              @click="handleDelete"
            >
              Delete
            </el-button>
          </div>
        </div>

        <!-- Transaction Information -->
        <el-card class="info-card" shadow="never">
          <template #header>
            <h3>Transaction Information</h3>
          </template>
          
          <div class="info-grid">
            <div class="info-item">
              <span class="label">Transaction Type</span>
              <div class="value">
                <el-tag :type="getTransactionTypeTagType(transactionData.transaction_type)">
                  {{ transactionTypeLabels[transactionData.transaction_type] }}
                </el-tag>
              </div>
            </div>

            <div class="info-item">
              <span class="label">Date & Time</span>
              <span class="value">{{ formatDateTime(transactionData.transaction_date) }}</span>
            </div>

            <div class="info-item">
              <span class="label">Reference Number</span>
              <span class="value reference-number">
                {{ transactionData.reference_number || '-' }}
              </span>
            </div>

            <div class="info-item">
              <span class="label">Performed By</span>
              <span class="value">{{ transactionData.performed_by }}</span>
            </div>

            <div class="info-item">
              <span class="label">Location</span>
              <span class="value">{{ transactionData.location }}</span>
            </div>

            <div class="info-item">
              <span class="label">Status</span>
              <el-tag :type="getStatusTagType(transactionData.status)" size="small">
                {{ transactionData.status }}
              </el-tag>
            </div>
          </div>
        </el-card>

        <!-- Spare Part Information -->
        <el-card class="info-card" shadow="never">
          <template #header>
            <h3>Spare Part Details</h3>
          </template>
          
          <div class="part-details-section">
            <div class="part-header">
              <div class="part-main-info">
                <h4>{{ transactionData.part_number }}</h4>
                <p class="part-name">{{ transactionData.part_name }}</p>
                <el-tag size="small" type="info">{{ transactionData.part_category }}</el-tag>
              </div>
              <div class="part-image">
                <el-image
                  :src="transactionData.part_image || '/images/no-image.png'"
                  fit="cover"
                  class="part-img"
                >
                  <template #error>
                    <div class="image-slot">
                      <el-icon><Picture /></el-icon>
                    </div>
                  </template>
                </el-image>
              </div>
            </div>

            <div class="part-info-grid">
              <div class="info-item">
                <span class="label">Part Number</span>
                <span class="value">{{ transactionData.part_number }}</span>
              </div>

              <div class="info-item">
                <span class="label">Part Name</span>
                <span class="value">{{ transactionData.part_name }}</span>
              </div>

              <div class="info-item">
                <span class="label">Category</span>
                <span class="value">{{ transactionData.part_category }}</span>
              </div>

              <div class="info-item">
                <span class="label">Manufacturer</span>
                <span class="value">{{ transactionData.manufacturer || '-' }}</span>
              </div>

              <div class="info-item">
                <span class="label">Current Stock</span>
                <span class="value stock-level" :class="getStockLevelClass(transactionData.current_stock, transactionData.minimum_stock)">
                  {{ transactionData.current_stock }} units
                </span>
              </div>

              <div class="info-item">
                <span class="label">Minimum Stock</span>
                <span class="value">{{ transactionData.minimum_stock }} units</span>
              </div>
            </div>
          </div>
        </el-card>

        <!-- Transaction Details -->
        <el-card class="info-card" shadow="never">
          <template #header>
            <h3>Transaction Details</h3>
          </template>
          
          <div class="transaction-details">
            <div class="quantity-section">
              <div class="quantity-display">
                <div class="quantity-label">Quantity</div>
                <div class="quantity-value" :class="getQuantityClass()">
                  {{ getQuantityDisplay() }}
                </div>
              </div>
              
              <div class="stock-impact">
                <div class="stock-before">
                  <span class="label">Stock Before</span>
                  <span class="value">{{ transactionData.stock_before }} units</span>
                </div>
                <div class="stock-arrow">
                  <el-icon><Right /></el-icon>
                </div>
                <div class="stock-after">
                  <span class="label">Stock After</span>
                  <span class="value">{{ transactionData.stock_after }} units</span>
                </div>
              </div>
            </div>

            <div class="pricing-section">
              <div class="info-grid">
                <div class="info-item">
                  <span class="label">Unit Price</span>
                  <span class="value price-value">${{ formatCurrency(transactionData.unit_price) }}</span>
                </div>

                <div class="info-item">
                  <span class="label">Total Value</span>
                  <span class="value total-value">${{ formatCurrency(transactionData.total_value) }}</span>
                </div>
              </div>
            </div>
          </div>
        </el-card>

        <!-- Additional Information -->
        <el-card v-if="hasAdditionalInfo" class="info-card" shadow="never">
          <template #header>
            <h3>Additional Information</h3>
          </template>
          
          <div class="info-grid">
            <div v-if="transactionData.supplier" class="info-item">
              <span class="label">Supplier</span>
              <span class="value">{{ transactionData.supplier }}</span>
            </div>

            <div v-if="transactionData.work_order" class="info-item">
              <span class="label">Work Order</span>
              <span class="value">{{ transactionData.work_order }}</span>
            </div>

            <div v-if="transactionData.reason" class="info-item">
              <span class="label">Adjustment Reason</span>
              <span class="value">{{ transactionData.reason }}</span>
            </div>

            <div v-if="transactionData.transfer_to" class="info-item">
              <span class="label">Transfer To</span>
              <span class="value">{{ transactionData.transfer_to }}</span>
            </div>

            <div v-if="transactionData.notes" class="info-item full-width">
              <span class="label">Notes</span>
              <p class="value notes-text">{{ transactionData.notes }}</p>
            </div>
          </div>
        </el-card>

        <!-- Related Transactions -->
        <el-card v-if="relatedTransactions.length > 0" class="info-card" shadow="never">
          <template #header>
            <h3>Related Transactions</h3>
          </template>
          
          <div class="related-transactions">
            <el-table :data="relatedTransactions" stripe>
              <el-table-column prop="transaction_date" label="Date" width="120">
                <template #default="{ row }">
                  {{ formatDate(row.transaction_date) }}
                </template>
              </el-table-column>

              <el-table-column prop="transaction_type" label="Type" width="100" align="center">
                <template #default="{ row }">
                  <el-tag :type="getTransactionTypeTagType(row.transaction_type)" size="small">
                    {{ transactionTypeLabels[row.transaction_type] }}
                  </el-tag>
                </template>
              </el-table-column>

              <el-table-column prop="quantity" label="Quantity" width="100" align="right">
                <template #default="{ row }">
                  <span :class="getQuantityClass(row.transaction_type, row.quantity)">
                    {{ getQuantityDisplay(row.transaction_type, row.quantity) }}
                  </span>
                </template>
              </el-table-column>

              <el-table-column prop="performed_by" label="Performed By" show-overflow-tooltip />

              <el-table-column prop="reference_number" label="Reference" width="120">
                <template #default="{ row }">
                  <span v-if="row.reference_number" class="reference-number">
                    {{ row.reference_number }}
                  </span>
                  <span v-else>-</span>
                </template>
              </el-table-column>

              <el-table-column label="Actions" width="80" align="center">
                <template #default="{ row }">
                  <el-button
                    type="primary"
                    :icon="View"
                    size="small"
                    circle
                    @click="handleViewRelated(row)"
                  />
                </template>
              </el-table-column>
            </el-table>
          </div>
        </el-card>
      </div>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Quick Actions -->
        <el-card class="sidebar-card" shadow="never">
          <template #header>
            <h3>Quick Actions</h3>
          </template>
          
          <div class="quick-actions">
            <el-button 
              v-if="canEdit"
              type="primary" 
              :icon="Edit"
              @click="handleEdit"
              class="action-button"
            >
              Edit Transaction
            </el-button>
            
            <el-button 
              :icon="Printer"
              @click="handlePrint"
              class="action-button"
            >
              Print Details
            </el-button>
            
            <el-button 
              :icon="Download"
              @click="handleExport"
              class="action-button"
            >
              Export Data
            </el-button>
            
            <el-button 
              v-if="canDelete"
              type="danger" 
              :icon="Delete"
              @click="handleDelete"
              class="action-button"
            >
              Delete Transaction
            </el-button>
          </div>
        </el-card>

        <!-- Stock Alert -->
        <el-card v-if="hasStockAlert" class="sidebar-card alert-card" shadow="never">
          <template #header>
            <h3>
              <el-icon class="alert-icon"><Warning /></el-icon>
              Stock Alert
            </h3>
          </template>
          
          <div class="alert-content">
            <p v-if="transactionData.current_stock <= 0" class="alert-message danger">
              <strong>Out of Stock!</strong><br>
              This part is currently out of stock.
            </p>
            <p v-else-if="transactionData.current_stock <= transactionData.minimum_stock" class="alert-message warning">
              <strong>Low Stock Warning!</strong><br>
              Current stock is below minimum level.
            </p>
            
            <div class="stock-info">
              <div class="stock-item">
                <span class="label">Current Stock:</span>
                <span class="value">{{ transactionData.current_stock }}</span>
              </div>
              <div class="stock-item">
                <span class="label">Minimum Stock:</span>
                <span class="value">{{ transactionData.minimum_stock }}</span>
              </div>
            </div>
            
            <el-button 
              type="primary" 
              size="small"
              @click="handleCreateStockIn"
              class="restock-button"
            >
              Create Stock In
            </el-button>
          </div>
        </el-card>

        <!-- Transaction History -->
        <el-card class="sidebar-card" shadow="never">
          <template #header>
            <h3>Recent Activity</h3>
          </template>
          
          <div class="activity-timeline">
            <div 
              v-for="activity in recentActivity" 
              :key="activity.id"
              class="activity-item"
            >
              <div class="activity-icon" :class="getActivityIconClass(activity.type)">
                <el-icon>
                  <component :is="getActivityIcon(activity.type)" />
                </el-icon>
              </div>
              <div class="activity-content">
                <div class="activity-title">{{ activity.title }}</div>
                <div class="activity-time">{{ formatRelativeTime(activity.created_at) }}</div>
              </div>
            </div>
          </div>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  ArrowLeft, Edit, Delete, View, Right, Warning, 
  Printer, Download, Picture, Plus, Minus, Switch
} from '@element-plus/icons-vue'

const router = useRouter()
const route = useRoute()

// State Management
const isLoading = ref(true)
const transactionData = ref({})
const relatedTransactions = ref([])
const recentActivity = ref([])

// Mock Data
const mockTransactionData = {
  id: 1,
  transaction_type: 'stock_in',
  transaction_date: '2024-01-20T10:00:00Z',
  part_number: 'PMP-001-BRG',
  part_name: 'Main Pump Bearing',
  part_category: 'Bearings',
  part_image: null,
  manufacturer: 'SKF Industrial',
  quantity: 2,
  unit_price: 1250.00,
  total_value: 2500.00,
  location: 'A-01-15',
  performed_by: 'John Smith',
  reference_number: 'PO-2024-001',
  supplier: 'SKF Industrial',
  current_stock: 7,
  minimum_stock: 2,
  stock_before: 5,
  stock_after: 7,
  status: 'Completed',
  notes: 'Regular stock replenishment for main pump maintenance',
  created_at: '2024-01-20T10:00:00Z',
  updated_at: '2024-01-20T10:00:00Z'
}

const mockRelatedTransactions = [
  {
    id: 2,
    transaction_date: '2024-01-15T14:20:00Z',
    transaction_type: 'stock_out',
    quantity: 1,
    performed_by: 'Mike Johnson',
    reference_number: 'WO-2024-012'
  },
  {
    id: 3,
    transaction_date: '2024-01-10T09:30:00Z',
    transaction_type: 'stock_in',
    quantity: 3,
    performed_by: 'Sarah Wilson',
    reference_number: 'PO-2024-002'
  }
]

const mockRecentActivity = [
  {
    id: 1,
    type: 'created',
    title: 'Transaction created',
    created_at: '2024-01-20T10:00:00Z'
  },
  {
    id: 2,
    type: 'stock_updated',
    title: 'Stock level updated',
    created_at: '2024-01-20T10:01:00Z'
  }
]

// Computed Properties
const transactionTypeLabels = computed(() => ({
  stock_in: 'Stock In',
  stock_out: 'Stock Out',
  adjustment: 'Adjustment',
  transfer: 'Transfer'
}))

const canEdit = computed(() => {
  // Allow editing within 24 hours of creation
  const createdAt = new Date(transactionData.value.created_at)
  const now = new Date()
  const hoursDiff = (now - createdAt) / (1000 * 60 * 60)
  return hoursDiff <= 24
})

const canDelete = computed(() => {
  // Allow deletion within 1 hour of creation
  const createdAt = new Date(transactionData.value.created_at)
  const now = new Date()
  const hoursDiff = (now - createdAt) / (1000 * 60 * 60)
  return hoursDiff <= 1
})

const hasAdditionalInfo = computed(() => {
  return transactionData.value.supplier || 
         transactionData.value.work_order || 
         transactionData.value.reason || 
         transactionData.value.transfer_to || 
         transactionData.value.notes
})

const hasStockAlert = computed(() => {
  return transactionData.value.current_stock <= transactionData.value.minimum_stock
})

// Helper Methods
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(Math.abs(value))
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric'
  })
}

const formatRelativeTime = (dateString) => {
  const now = new Date()
  const date = new Date(dateString)
  const diffInHours = (now - date) / (1000 * 60 * 60)
  
  if (diffInHours < 1) {
    return 'Just now'
  } else if (diffInHours < 24) {
    return `${Math.floor(diffInHours)} hours ago`
  } else {
    return `${Math.floor(diffInHours / 24)} days ago`
  }
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

const getStatusTagType = (status) => {
  const statusMap = {
    'Completed': 'success',
    'Pending': 'warning',
    'Cancelled': 'danger'
  }
  return statusMap[status] || 'info'
}

const getStockLevelClass = (current, minimum) => {
  if (current <= 0) return 'danger'
  if (current <= minimum) return 'warning'
  return 'success'
}

const getQuantityClass = (type = null, quantity = null) => {
  const transType = type || transactionData.value.transaction_type
  const qty = quantity || transactionData.value.quantity
  
  if (transType === 'stock_in' || (transType === 'adjustment' && qty > 0)) {
    return 'quantity-positive'
  } else if (transType === 'stock_out' || (transType === 'adjustment' && qty < 0)) {
    return 'quantity-negative'
  }
  return 'quantity-neutral'
}

const getQuantityDisplay = (type = null, quantity = null) => {
  const transType = type || transactionData.value.transaction_type
  const qty = quantity || transactionData.value.quantity
  
  if (transType === 'stock_in') {
    return `+${qty}`
  } else if (transType === 'stock_out') {
    return `-${qty}`
  } else if (transType === 'adjustment') {
    return qty > 0 ? `+${qty}` : `${qty}`
  }
  return qty.toString()
}

const getActivityIconClass = (type) => {
  const classMap = {
    created: 'activity-created',
    stock_updated: 'activity-updated',
    edited: 'activity-edited'
  }
  return classMap[type] || 'activity-default'
}

const getActivityIcon = (type) => {
  const iconMap = {
    created: Plus,
    stock_updated: Edit,
    edited: Edit
  }
  return iconMap[type] || Edit
}

// Event Handlers
const handleBack = () => {
  router.push('/equipment/sparepart-inventory')
}

const handleEdit = () => {
  if (!canEdit.value) {
    ElMessage.warning('This transaction can no longer be edited')
    return
  }
  router.push(`/equipment/sparepart-inventory/${route.params.id}/edit`)
}

const handleDelete = async () => {
  if (!canDelete.value) {
    ElMessage.warning('This transaction can no longer be deleted')
    return
  }

  try {
    await ElMessageBox.confirm(
      'Are you sure you want to delete this inventory transaction? This action cannot be undone.',
      'Confirm Delete',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning',
        confirmButtonClass: 'el-button--danger'
      }
    )
    
    // Simulate API call
    ElMessage.success('Inventory transaction deleted successfully')
    router.push('/equipment/sparepart-inventory')
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete inventory transaction')
    }
  }
}

const handlePrint = () => {
  window.print()
}

const handleExport = () => {
  ElMessage.success('Exporting transaction data...')
}

const handleCreateStockIn = () => {
  router.push(`/equipment/sparepart-inventory/create?part_id=${transactionData.value.part_id}&type=stock_in`)
}

const handleViewRelated = (row) => {
  router.push(`/equipment/sparepart-inventory/${row.id}`)
}

// Data Fetching
const fetchTransactionData = async () => {
  try {
    isLoading.value = true
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    
    transactionData.value = mockTransactionData
    relatedTransactions.value = mockRelatedTransactions
    recentActivity.value = mockRecentActivity
  } catch (error) {
    ElMessage.error('Failed to fetch transaction data')
  } finally {
    isLoading.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchTransactionData()
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
}

.content-layout {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 24px;
  max-width: 1200px;
  margin: 0 auto;
}

.main-content {
  min-width: 0;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
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

.transaction-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.transaction-id {
  font-family: monospace;
  font-size: 14px;
  color: #6b7280;
  background: #f3f4f6;
  padding: 2px 8px;
  border-radius: 4px;
}

.transaction-date {
  font-size: 14px;
  color: #6b7280;
}

.header-actions {
  display: flex;
  gap: 8px;
}

.info-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 24px;
}

.info-card :deep(.el-card__header) {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.info-card h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  padding: 24px;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-item.full-width {
  grid-column: 1 / -1;
}

.info-item .label {
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.info-item .value {
  font-size: 16px;
  color: #1f2937;
  font-weight: 500;
}

.reference-number {
  font-family: monospace;
  background: #f3f4f6;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 14px;
}

.notes-text {
  margin: 0;
  line-height: 1.5;
  color: #4b5563;
}

.part-details-section {
  padding: 24px;
}

.part-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.part-main-info h4 {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
}

.part-name {
  font-size: 16px;
  color: #6b7280;
  margin: 0 0 8px 0;
}

.part-image {
  width: 80px;
  height: 80px;
}

.part-img {
  width: 100%;
  height: 100%;
  border-radius: 8px;
}

.image-slot {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
  background: #f5f5f5;
  color: #909399;
  font-size: 24px;
  border-radius: 8px;
}

.part-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.stock-level.success {
  color: #16a34a;
}

.stock-level.warning {
  color: #d97706;
}

.stock-level.danger {
  color: #dc2626;
}

.transaction-details {
  padding: 24px;
}

.quantity-section {
  margin-bottom: 24px;
}

.quantity-display {
  text-align: center;
  margin-bottom: 20px;
}

.quantity-label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 8px;
}

.quantity-value {
  font-size: 32px;
  font-weight: 700;
}

.quantity-positive {
  color: #16a34a;
}

.quantity-negative {
  color: #dc2626;
}

.quantity-neutral {
  color: #1f2937;
}

.stock-impact {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 20px;
  padding: 16px;
  background: #f9fafb;
  border-radius: 8px;
}

.stock-before,
.stock-after {
  text-align: center;
}

.stock-before .label,
.stock-after .label {
  display: block;
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
}

.stock-before .value,
.stock-after .value {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.stock-arrow {
  color: #6b7280;
  font-size: 20px;
}

.pricing-section {
  border-top: 1px solid #e5e7eb;
  padding-top: 24px;
}

.price-value {
  color: #059669;
  font-weight: 600;
}

.total-value {
  color: #1f2937;
  font-weight: 700;
  font-size: 18px;
}

.related-transactions {
  padding: 24px;
}

.sidebar {
  position: sticky;
  top: 24px;
  height: fit-content;
}

.sidebar-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 24px;
}

.sidebar-card :deep(.el-card__header) {
  padding: 16px 20px;
  border-bottom: 1px solid #e5e7eb;
}

.sidebar-card h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.quick-actions {
  padding: 20px;
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

.alert-card .alert-icon {
  color: #f59e0b;
}

.alert-content {
  padding: 20px;
}

.alert-message {
  margin: 0 0 16px 0;
  padding: 12px;
  border-radius: 6px;
  font-size: 14px;
}

.alert-message.danger {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
}

.alert-message.warning {
  background: #fffbeb;
  color: #d97706;
  border: 1px solid #fed7aa;
}

.stock-info {
  margin-bottom: 16px;
}

.stock-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 14px;
}

.stock-item .label {
  color: #6b7280;
}

.stock-item .value {
  font-weight: 500;
  color: #1f2937;
}

.restock-button {
  width: 100%;
}

.activity-timeline {
  padding: 20px;
}

.activity-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 16px;
}

.activity-item:last-child {
  margin-bottom: 0;
}

.activity-icon {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.activity-created {
  background: #f0fdf4;
  color: #16a34a;
}

.activity-updated {
  background: #eff6ff;
  color: #2563eb;
}

.activity-edited {
  background: #fef3c7;
  color: #d97706;
}

.activity-default {
  background: #f3f4f6;
  color: #6b7280;
}

.activity-content {
  flex: 1;
}

.activity-title {
  font-size: 14px;
  color: #1f2937;
  font-weight: 500;
  margin-bottom: 2px;
}

.activity-time {
  font-size: 12px;
  color: #6b7280;
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
  .page-header {
    flex-direction: column;
    gap: 16px;
  }
  
  .header-left {
    flex-direction: column;
    gap: 12px;
  }
  
  .transaction-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .part-header {
    flex-direction: column;
    gap: 16px;
  }
  
  .stock-impact {
    flex-direction: column;
    gap: 12px;
  }
  
  .stock-arrow {
    transform: rotate(90deg);
  }
}

@media print {
  .page-container {
    background: white;
    padding: 0;
  }
  
  .header-actions,
  .sidebar {
    display: none;
  }
  
  .content-layout {
    grid-template-columns: 1fr;
  }
  
  .info-card {
    box-shadow: none;
    border: 1px solid #e5e7eb;
  }
}
</style>