<template>
  <div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-title">
        <h1>{{ $t('equipment.sparepart_inventory.title') }}</h1>
        <p class="page-description">Track spare parts inventory movements and stock levels</p>
      </div>
      <div class="page-actions">
        <el-button 
          type="primary" 
          @click="handleCreate"
          :icon="Plus"
        >
          {{ $t('equipment.sparepart_inventory.create') }}
        </el-button>
      </div>
    </div>

    <!-- Content Card -->
    <el-card class="content-card" shadow="never">
      <!-- Advanced Filter Section -->
      <div class="filter-section">
        <TableControls
          v-model:search="filters.search"
          v-model:page-size="pagination.pageSize"
          :loading="isLoading"
          @refresh="fetchData"
          @export="handleBulkExport"
          @clear-filters="clearFilters"
        >
          <template #filters>
            <div class="filter-grid">
              <el-select
                v-model="filters.transaction_type"
                placeholder="Select transaction type"
                clearable
                class="filter-item"
              >
                <el-option
                  v-for="(label, value) in transactionTypeOptions"
                  :key="value"
                  :label="label"
                  :value="value"
                />
              </el-select>

              <el-select
                v-model="filters.part_category"
                placeholder="Select part category"
                clearable
                class="filter-item"
              >
                <el-option
                  v-for="(label, value) in categoryOptions"
                  :key="value"
                  :label="label"
                  :value="value"
                />
              </el-select>

              <el-select
                v-model="filters.location"
                placeholder="Select location"
                clearable
                filterable
                class="filter-item"
              >
                <el-option
                  v-for="location in locationOptions"
                  :key="location"
                  :label="location"
                  :value="location"
                />
              </el-select>

              <el-date-picker
                v-model="filters.date_range"
                type="daterange"
                range-separator="To"
                start-placeholder="Start date"
                end-placeholder="End date"
                format="YYYY-MM-DD"
                value-format="YYYY-MM-DD"
                class="filter-item"
              />

              <el-input
                v-model="filters.part_number"
                placeholder="Search by part number"
                clearable
                class="filter-item"
              />
            </div>
          </template>
        </TableControls>
      </div>

      <!-- Summary Cards -->
      <div class="summary-cards">
        <div class="summary-card">
          <div class="summary-icon stock-in">
            <el-icon><Plus /></el-icon>
          </div>
          <div class="summary-content">
            <div class="summary-value">{{ summaryData.total_stock_in }}</div>
            <div class="summary-label">Stock In (This Month)</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="summary-icon stock-out">
            <el-icon><Minus /></el-icon>
          </div>
          <div class="summary-content">
            <div class="summary-value">{{ summaryData.total_stock_out }}</div>
            <div class="summary-label">Stock Out (This Month)</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="summary-icon adjustments">
            <el-icon><Edit /></el-icon>
          </div>
          <div class="summary-content">
            <div class="summary-value">{{ summaryData.total_adjustments }}</div>
            <div class="summary-label">Adjustments</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="summary-icon value">
            <el-icon><Money /></el-icon>
          </div>
          <div class="summary-content">
            <div class="summary-value">${{ formatCurrency(summaryData.total_value) }}</div>
            <div class="summary-label">Total Value</div>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="content-area">
        <!-- Loading State -->
        <el-skeleton v-if="isLoading" :rows="8" animated />
        
        <!-- Data Table -->
        <div v-else class="table-container">
          <el-table
            :data="tableData"
            v-loading="isLoading"
            @selection-change="handleSelectionChange"
            @sort-change="handleSortChange"
            stripe
            class="data-table"
          >
            <el-table-column type="selection" width="55" />
            
            <el-table-column
              prop="transaction_date"
              label="Date"
              sortable="custom"
              min-width="120"
            >
              <template #default="{ row }">
                <span>{{ formatDate(row.transaction_date) }}</span>
              </template>
            </el-table-column>

            <el-table-column
              prop="transaction_type"
              label="Type"
              sortable="custom"
              min-width="100"
              align="center"
            >
              <template #default="{ row }">
                <el-tag :type="getTransactionTypeTagType(row.transaction_type)" size="small">
                  {{ transactionTypeOptions[row.transaction_type] }}
                </el-tag>
              </template>
            </el-table-column>

            <el-table-column
              prop="part_number"
              :label="$t('equipment.sparepart_inventory.fields.part_number')"
              sortable="custom"
              min-width="140"
              show-overflow-tooltip
            >
              <template #default="{ row }">
                <div class="part-info">
                  <span class="part-number">{{ row.part_number }}</span>
                  <span class="part-name">{{ row.part_name }}</span>
                </div>
              </template>
            </el-table-column>

            <el-table-column
              prop="quantity"
              :label="$t('equipment.sparepart_inventory.fields.quantity')"
              sortable="custom"
              min-width="100"
              align="right"
            >
              <template #default="{ row }">
                <span :class="getQuantityClass(row.transaction_type, row.quantity)">
                  {{ getQuantityDisplay(row.transaction_type, row.quantity) }}
                </span>
              </template>
            </el-table-column>

            <el-table-column
              prop="unit_price"
              :label="$t('equipment.sparepart_inventory.fields.unit_price')"
              sortable="custom"
              min-width="120"
              align="right"
            >
              <template #default="{ row }">
                <span class="price-value">${{ formatCurrency(row.unit_price) }}</span>
              </template>
            </el-table-column>

            <el-table-column
              prop="total_value"
              label="Total Value"
              sortable="custom"
              min-width="120"
              align="right"
            >
              <template #default="{ row }">
                <span class="total-value">${{ formatCurrency(row.total_value) }}</span>
              </template>
            </el-table-column>

            <el-table-column
              prop="location"
              :label="$t('equipment.sparepart_inventory.fields.location')"
              sortable="custom"
              min-width="120"
              show-overflow-tooltip
            />

            <el-table-column
              prop="performed_by"
              label="Performed By"
              sortable="custom"
              min-width="120"
              show-overflow-tooltip
            />

            <el-table-column
              prop="reference_number"
              label="Reference"
              min-width="120"
              show-overflow-tooltip
            >
              <template #default="{ row }">
                <span v-if="row.reference_number" class="reference-number">
                  {{ row.reference_number }}
                </span>
                <span v-else class="no-reference">-</span>
              </template>
            </el-table-column>

            <el-table-column label="Actions" width="120" align="center" fixed="right">
              <template #default="{ row }">
                <div class="action-buttons">
                  <el-tooltip content="View Details" placement="top">
                    <el-button
                      type="primary"
                      :icon="View"
                      size="small"
                      circle
                      @click="handleView(row)"
                    />
                  </el-tooltip>
                  <el-tooltip content="Edit" placement="top">
                    <el-button
                      type="warning"
                      :icon="Edit"
                      size="small"
                      circle
                      @click="handleEdit(row)"
                      :disabled="!canEdit(row)"
                    />
                  </el-tooltip>
                  <el-tooltip content="Delete" placement="top">
                    <el-button
                      type="danger"
                      :icon="Delete"
                      size="small"
                      circle
                      @click="handleDelete(row)"
                      :disabled="!canDelete(row)"
                    />
                  </el-tooltip>
                </div>
              </template>
            </el-table-column>
          </el-table>

          <!-- Pagination -->
          <div class="pagination-container">
            <el-pagination
              v-model:current-page="pagination.currentPage"
              v-model:page-size="pagination.pageSize"
              :total="pagination.total"
              :page-sizes="[10, 25, 50, 100]"
              layout="total, sizes, prev, pager, next, jumper"
              @size-change="handleSizeChange"
              @current-change="handleCurrentChange"
            />
          </div>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  Plus, Edit, Delete, View, Minus, Money 
} from '@element-plus/icons-vue'
import { useQuery } from '@tanstack/vue-query'
import TableControls from '@/components/TableControls.vue'

const router = useRouter()

// State Management
const isLoading = ref(false)
const selectedRows = ref([])

// Filters
const filters = reactive({
  search: '',
  transaction_type: '',
  part_category: '',
  location: '',
  date_range: null,
  part_number: ''
})

// Pagination
const pagination = reactive({
  currentPage: 1,
  pageSize: 25,
  total: 0
})

// Sorting
const sortConfig = reactive({
  prop: '',
  order: ''
})

// Mock Data
const mockData = ref([
  {
    id: 1,
    transaction_date: '2024-01-20T10:00:00Z',
    transaction_type: 'stock_in',
    part_number: 'PMP-001-BRG',
    part_name: 'Main Pump Bearing',
    part_category: 'bearings',
    quantity: 2,
    unit_price: 1250.00,
    total_value: 2500.00,
    location: 'A-01-15',
    performed_by: 'John Smith',
    reference_number: 'PO-2024-001',
    supplier: 'SKF Industrial',
    notes: 'Regular stock replenishment',
    created_at: '2024-01-20T10:00:00Z'
  },
  {
    id: 2,
    transaction_date: '2024-01-18T15:30:00Z',
    transaction_type: 'stock_out',
    part_number: 'VLV-002-SL',
    part_name: 'Safety Relief Valve Seal',
    part_category: 'seals_gaskets',
    quantity: 5,
    unit_price: 85.50,
    total_value: 427.50,
    location: 'B-02-08',
    performed_by: 'Mike Johnson',
    reference_number: 'WO-2024-015',
    work_order: 'Valve Maintenance',
    notes: 'Used for scheduled maintenance',
    created_at: '2024-01-18T15:30:00Z'
  },
  {
    id: 3,
    transaction_date: '2024-01-17T09:15:00Z',
    transaction_type: 'adjustment',
    part_number: 'FLT-003-CRT',
    part_name: 'Oil Filter Cartridge',
    part_category: 'filters',
    quantity: -2,
    unit_price: 45.75,
    total_value: -91.50,
    location: 'C-01-12',
    performed_by: 'Sarah Wilson',
    reference_number: 'ADJ-2024-003',
    reason: 'Damaged during handling',
    notes: 'Physical count adjustment',
    created_at: '2024-01-17T09:15:00Z'
  },
  {
    id: 4,
    transaction_date: '2024-01-15T14:20:00Z',
    transaction_type: 'transfer',
    part_number: 'PMP-001-BRG',
    part_name: 'Main Pump Bearing',
    part_category: 'bearings',
    quantity: 1,
    unit_price: 1250.00,
    total_value: 1250.00,
    location: 'A-01-15',
    transfer_to: 'Workshop-01',
    performed_by: 'David Brown',
    reference_number: 'TRF-2024-008',
    notes: 'Transfer to workshop for installation',
    created_at: '2024-01-15T14:20:00Z'
  }
])

// Options
const transactionTypeOptions = computed(() => ({
  stock_in: 'Stock In',
  stock_out: 'Stock Out',
  adjustment: 'Adjustment',
  transfer: 'Transfer'
}))

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

const locationOptions = computed(() => {
  const locations = [...new Set(mockData.value.map(item => item.location))]
  return locations.sort()
})

// Summary Data
const summaryData = computed(() => {
  const currentMonth = new Date().getMonth()
  const currentYear = new Date().getFullYear()
  
  const thisMonthData = mockData.value.filter(item => {
    const itemDate = new Date(item.transaction_date)
    return itemDate.getMonth() === currentMonth && itemDate.getFullYear() === currentYear
  })
  
  return {
    total_stock_in: thisMonthData
      .filter(item => item.transaction_type === 'stock_in')
      .reduce((sum, item) => sum + item.quantity, 0),
    total_stock_out: thisMonthData
      .filter(item => item.transaction_type === 'stock_out')
      .reduce((sum, item) => sum + item.quantity, 0),
    total_adjustments: thisMonthData
      .filter(item => item.transaction_type === 'adjustment')
      .length,
    total_value: thisMonthData
      .reduce((sum, item) => sum + Math.abs(item.total_value), 0)
  }
})

// Filtered and Sorted Data
const tableData = computed(() => {
  let data = [...mockData.value]

  // Apply filters
  if (filters.search) {
    const searchTerm = filters.search.toLowerCase()
    data = data.filter(item =>
      item.part_number.toLowerCase().includes(searchTerm) ||
      item.part_name.toLowerCase().includes(searchTerm) ||
      item.performed_by.toLowerCase().includes(searchTerm) ||
      (item.reference_number && item.reference_number.toLowerCase().includes(searchTerm))
    )
  }

  if (filters.transaction_type) {
    data = data.filter(item => item.transaction_type === filters.transaction_type)
  }

  if (filters.part_category) {
    data = data.filter(item => item.part_category === filters.part_category)
  }

  if (filters.location) {
    data = data.filter(item => item.location === filters.location)
  }

  if (filters.part_number) {
    data = data.filter(item => 
      item.part_number.toLowerCase().includes(filters.part_number.toLowerCase())
    )
  }

  if (filters.date_range && filters.date_range.length === 2) {
    const [startDate, endDate] = filters.date_range
    data = data.filter(item => {
      const itemDate = item.transaction_date.split('T')[0]
      return itemDate >= startDate && itemDate <= endDate
    })
  }

  // Apply sorting
  if (sortConfig.prop && sortConfig.order) {
    data.sort((a, b) => {
      const aVal = a[sortConfig.prop]
      const bVal = b[sortConfig.prop]
      
      if (sortConfig.order === 'ascending') {
        return aVal > bVal ? 1 : -1
      } else {
        return aVal < bVal ? 1 : -1
      }
    })
  }

  // Update pagination total
  pagination.total = data.length

  // Apply pagination
  const start = (pagination.currentPage - 1) * pagination.pageSize
  const end = start + pagination.pageSize
  return data.slice(start, end)
})

// Helper Methods
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(Math.abs(value))
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

const getTransactionTypeTagType = (type) => {
  const typeMap = {
    stock_in: 'success',
    stock_out: 'warning',
    adjustment: 'info',
    transfer: 'primary'
  }
  return typeMap[type] || ''
}

const getQuantityClass = (type, quantity) => {
  if (type === 'stock_in' || (type === 'adjustment' && quantity > 0)) {
    return 'quantity-positive'
  } else if (type === 'stock_out' || (type === 'adjustment' && quantity < 0)) {
    return 'quantity-negative'
  }
  return 'quantity-neutral'
}

const getQuantityDisplay = (type, quantity) => {
  if (type === 'stock_in') {
    return `+${quantity}`
  } else if (type === 'stock_out') {
    return `-${quantity}`
  } else if (type === 'adjustment') {
    return quantity > 0 ? `+${quantity}` : `${quantity}`
  }
  return quantity.toString()
}

const canEdit = (row) => {
  // Allow editing within 24 hours of creation
  const createdAt = new Date(row.created_at)
  const now = new Date()
  const hoursDiff = (now - createdAt) / (1000 * 60 * 60)
  return hoursDiff <= 24
}

const canDelete = (row) => {
  // Allow deletion within 1 hour of creation
  const createdAt = new Date(row.created_at)
  const now = new Date()
  const hoursDiff = (now - createdAt) / (1000 * 60 * 60)
  return hoursDiff <= 1
}

// Data Fetching
const fetchData = async () => {
  isLoading.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    // In real implementation, this would be an API call
  } catch (error) {
    ElMessage.error('Failed to fetch inventory data')
  } finally {
    isLoading.value = false
  }
}

// Event Handlers
const handleCreate = () => {
  router.push('/equipment/sparepart-inventory/create')
}

const handleView = (row) => {
  router.push(`/equipment/sparepart-inventory/${row.id}`)
}

const handleEdit = (row) => {
  if (!canEdit(row)) {
    ElMessage.warning('This transaction can no longer be edited')
    return
  }
  router.push(`/equipment/sparepart-inventory/${row.id}/edit`)
}

const handleDelete = async (row) => {
  if (!canDelete(row)) {
    ElMessage.warning('This transaction can no longer be deleted')
    return
  }

  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete this inventory transaction?`,
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
    await fetchData()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete inventory transaction')
    }
  }
}

const handleSelectionChange = (selection) => {
  selectedRows.value = selection
}

const handleSortChange = ({ prop, order }) => {
  sortConfig.prop = prop
  sortConfig.order = order
}

const handleSizeChange = (size) => {
  pagination.pageSize = size
  pagination.currentPage = 1
}

const handleCurrentChange = (page) => {
  pagination.currentPage = page
}

const handleBulkExport = () => {
  if (selectedRows.value.length === 0) {
    ElMessage.warning('Please select transactions to export')
    return
  }
  
  ElMessage.success(`Exporting ${selectedRows.value.length} transactions...`)
}

const clearFilters = () => {
  Object.keys(filters).forEach(key => {
    if (key === 'date_range') {
      filters[key] = null
    } else {
      filters[key] = ''
    }
  })
}

// Lifecycle
onMounted(() => {
  fetchData()
})

// Watchers
watch(
  () => [filters.search, filters.transaction_type, filters.part_category, filters.location, filters.date_range, filters.part_number],
  () => {
    pagination.currentPage = 1
  },
  { deep: true }
)
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

.content-card {
  border-radius: 12px;
  border: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filter-section {
  margin-bottom: 24px;
}

.filter-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-top: 16px;
}

.filter-item {
  width: 100%;
}

.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}

.summary-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.summary-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.summary-icon.stock-in {
  background-color: #f0fdf4;
  color: #16a34a;
}

.summary-icon.stock-out {
  background-color: #fef3c7;
  color: #d97706;
}

.summary-icon.adjustments {
  background-color: #eff6ff;
  color: #2563eb;
}

.summary-icon.value {
  background-color: #fefce8;
  color: #ca8a04;
}

.summary-content {
  flex: 1;
}

.summary-value {
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
  line-height: 1;
}

.summary-label {
  font-size: 14px;
  color: #6b7280;
  margin-top: 4px;
}

.content-area {
  background: white;
  border-radius: 8px;
}

.table-container {
  overflow: hidden;
}

.data-table {
  width: 100%;
}

.part-info {
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
}

.reference-number {
  font-family: monospace;
  font-size: 12px;
  background: #f3f4f6;
  padding: 2px 6px;
  border-radius: 4px;
}

.no-reference {
  color: #9ca3af;
}

.action-buttons {
  display: flex;
  gap: 4px;
  justify-content: center;
}

.pagination-container {
  display: flex;
  justify-content: center;
  padding: 20px 0;
  border-top: 1px solid #e5e7eb;
  margin-top: 16px;
}
</style>