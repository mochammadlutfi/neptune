<template>
  <div class="page-container">
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-title">
        <h1>{{ $t('equipment.sparepart.title') }}</h1>
        <p class="page-description">Manage spare parts inventory and equipment compatibility</p>
      </div>
      <div class="page-actions">
        <el-button 
          type="primary" 
          @click="handleCreate"
          :icon="Plus"
        >
          {{ $t('equipment.sparepart.create') }}
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
                v-model="filters.category"
                :placeholder="$t('equipment.sparepart.placeholder.category')"
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
                v-model="filters.criticality"
                placeholder="Select criticality"
                clearable
                class="filter-item"
              >
                <el-option
                  v-for="(label, value) in criticalityOptions"
                  :key="value"
                  :label="label"
                  :value="value"
                />
              </el-select>

              <el-select
                v-model="filters.status"
                placeholder="Select status"
                clearable
                class="filter-item"
              >
                <el-option
                  v-for="(label, value) in statusOptions"
                  :key="value"
                  :label="label"
                  :value="value"
                />
              </el-select>

              <el-select
                v-model="filters.supplier_name"
                placeholder="Select supplier"
                clearable
                filterable
                class="filter-item"
              >
                <el-option
                  v-for="supplier in supplierOptions"
                  :key="supplier"
                  :label="supplier"
                  :value="supplier"
                />
              </el-select>

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
          <div class="summary-icon critical">
            <el-icon><Warning /></el-icon>
          </div>
          <div class="summary-content">
            <div class="summary-value">{{ summaryData.critical_parts }}</div>
            <div class="summary-label">Critical Parts</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="summary-icon active">
            <el-icon><CircleCheck /></el-icon>
          </div>
          <div class="summary-content">
            <div class="summary-value">{{ summaryData.active_parts }}</div>
            <div class="summary-label">Active Parts</div>
          </div>
        </div>
        <div class="summary-card">
          <div class="summary-icon suppliers">
            <el-icon><Shop /></el-icon>
          </div>
          <div class="summary-content">
            <div class="summary-value">{{ summaryData.total_suppliers }}</div>
            <div class="summary-label">Suppliers</div>
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
              prop="part_number"
              :label="$t('equipment.sparepart.fields.part_number')"
              sortable="custom"
              min-width="140"
              show-overflow-tooltip
            >
              <template #default="{ row }">
                <div class="part-number-cell">
                  <span class="part-number">{{ row.part_number }}</span>
                  <el-tag
                    v-if="row.criticality === 'critical'"
                    type="danger"
                    size="small"
                    class="criticality-tag"
                  >
                    Critical
                  </el-tag>
                </div>
              </template>
            </el-table-column>

            <el-table-column
              prop="part_name"
              :label="$t('equipment.sparepart.fields.part_name')"
              sortable="custom"
              min-width="180"
              show-overflow-tooltip
            />

            <el-table-column
              prop="category"
              label="Category"
              sortable="custom"
              min-width="120"
            >
              <template #default="{ row }">
                <el-tag :type="getCategoryTagType(row.category)" size="small">
                  {{ categoryOptions[row.category] || row.category }}
                </el-tag>
              </template>
            </el-table-column>

            <el-table-column
              prop="supplier_name"
              :label="$t('equipment.sparepart.fields.supplier_name')"
              sortable="custom"
              min-width="150"
              show-overflow-tooltip
            />

            <el-table-column
              prop="unit_price"
              :label="$t('equipment.sparepart.fields.unit_price')"
              sortable="custom"
              min-width="120"
              align="right"
            >
              <template #default="{ row }">
                <span class="price-value">${{ formatCurrency(row.unit_price) }}</span>
              </template>
            </el-table-column>

            <el-table-column
              prop="lead_time_days"
              :label="$t('equipment.sparepart.fields.lead_time_days')"
              sortable="custom"
              min-width="120"
              align="center"
            >
              <template #default="{ row }">
                <span>{{ row.lead_time_days }} days</span>
              </template>
            </el-table-column>

            <el-table-column
              prop="status"
              :label="$t('equipment.sparepart.fields.status')"
              sortable="custom"
              min-width="100"
              align="center"
            >
              <template #default="{ row }">
                <el-tag :type="getStatusTagType(row.status)" size="small">
                  {{ statusOptions[row.status] || row.status }}
                </el-tag>
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
                    />
                  </el-tooltip>
                  <el-tooltip content="Delete" placement="top">
                    <el-button
                      type="danger"
                      :icon="Delete"
                      size="small"
                      circle
                      @click="handleDelete(row)"
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
  Plus, Edit, Delete, View, Warning, CircleCheck, 
  Shop, Money 
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
  category: '',
  criticality: '',
  status: '',
  supplier_name: '',
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
    part_number: 'PMP-001-BRG',
    part_name: 'Main Pump Bearing',
    category: 'bearings',
    supplier_name: 'SKF Industrial',
    unit_price: 1250.00,
    lead_time_days: 14,
    criticality: 'critical',
    status: 'active',
    manufacturer: 'SKF',
    model: 'NU 2320 ECM',
    equipment_compatibility: ['Main Process Pump', 'Backup Pump'],
    minimum_stock: 2,
    maximum_stock: 6,
    storage_location: 'A-01-15'
  },
  {
    id: 2,
    part_number: 'VLV-002-SL',
    part_name: 'Safety Relief Valve Seal',
    category: 'seals_gaskets',
    supplier_name: 'Parker Hannifin',
    unit_price: 85.50,
    lead_time_days: 7,
    criticality: 'important',
    status: 'active',
    manufacturer: 'Parker',
    model: 'O-Ring 2-329',
    equipment_compatibility: ['Safety Relief Valve'],
    minimum_stock: 10,
    maximum_stock: 25,
    storage_location: 'B-02-08'
  },
  {
    id: 3,
    part_number: 'FLT-003-CRT',
    part_name: 'Oil Filter Cartridge',
    category: 'filters',
    supplier_name: 'Donaldson Company',
    unit_price: 45.75,
    lead_time_days: 5,
    criticality: 'normal',
    status: 'active',
    manufacturer: 'Donaldson',
    model: 'P551329',
    equipment_compatibility: ['Hydraulic System', 'Lube Oil System'],
    minimum_stock: 20,
    maximum_stock: 50,
    storage_location: 'C-01-12'
  }
])

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

const supplierOptions = computed(() => {
  const suppliers = [...new Set(mockData.value.map(item => item.supplier_name))]
  return suppliers.sort()
})

// Summary Data
const summaryData = computed(() => ({
  critical_parts: mockData.value.filter(item => item.criticality === 'critical').length,
  active_parts: mockData.value.filter(item => item.status === 'active').length,
  total_suppliers: supplierOptions.value.length,
  total_value: mockData.value.reduce((sum, item) => sum + item.unit_price, 0)
}))

// Filtered and Sorted Data
const tableData = computed(() => {
  let data = [...mockData.value]

  // Apply filters
  if (filters.search) {
    const searchTerm = filters.search.toLowerCase()
    data = data.filter(item =>
      item.part_number.toLowerCase().includes(searchTerm) ||
      item.part_name.toLowerCase().includes(searchTerm) ||
      item.supplier_name.toLowerCase().includes(searchTerm)
    )
  }

  if (filters.category) {
    data = data.filter(item => item.category === filters.category)
  }

  if (filters.criticality) {
    data = data.filter(item => item.criticality === filters.criticality)
  }

  if (filters.status) {
    data = data.filter(item => item.status === filters.status)
  }

  if (filters.supplier_name) {
    data = data.filter(item => item.supplier_name === filters.supplier_name)
  }

  if (filters.part_number) {
    data = data.filter(item => 
      item.part_number.toLowerCase().includes(filters.part_number.toLowerCase())
    )
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
  }).format(value)
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

const getStatusTagType = (status) => {
  const typeMap = {
    active: 'success',
    discontinued: 'warning',
    obsolete: 'danger'
  }
  return typeMap[status] || ''
}

// Data Fetching
const fetchData = async () => {
  isLoading.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    // In real implementation, this would be an API call
  } catch (error) {
    ElMessage.error('Failed to fetch spare parts data')
  } finally {
    isLoading.value = false
  }
}

// Event Handlers
const handleCreate = () => {
  router.push('/equipment/sparepart/create')
}

const handleView = (row) => {
  router.push(`/equipment/sparepart/${row.id}`)
}

const handleEdit = (row) => {
  router.push(`/equipment/sparepart/${row.id}/edit`)
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(
      `Are you sure you want to delete spare part "${row.part_name}"?`,
      'Confirm Delete',
      {
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        type: 'warning',
        confirmButtonClass: 'el-button--danger'
      }
    )
    
    // Simulate API call
    ElMessage.success('Spare part deleted successfully')
    await fetchData()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('Failed to delete spare part')
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
    ElMessage.warning('Please select spare parts to export')
    return
  }
  
  ElMessage.success(`Exporting ${selectedRows.value.length} spare parts...`)
}

const clearFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
}

// Lifecycle
onMounted(() => {
  fetchData()
})

// Watchers
watch(
  () => [filters.search, filters.category, filters.criticality, filters.status, filters.supplier_name, filters.part_number],
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

.summary-icon.critical {
  background-color: #fef2f2;
  color: #dc2626;
}

.summary-icon.active {
  background-color: #f0fdf4;
  color: #16a34a;
}

.summary-icon.suppliers {
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

.part-number-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}

.part-number {
  font-weight: 500;
}

.criticality-tag {
  font-size: 10px;
}

.price-value {
  font-weight: 500;
  color: #059669;
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