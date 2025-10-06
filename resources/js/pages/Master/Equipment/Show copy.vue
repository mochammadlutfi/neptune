<template>
  <div class="equipment-detail min-h-screen bg-gray-50">
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
              {{ $t('master.equipment.detail') }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">
              View detailed equipment specifications and configuration
            </p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <el-button type="primary" plain @click="handlePrint">
            <template #icon>
              <Icon icon="mdi:printer" :width="16" :height="16" />
            </template>
            Print
          </el-button>
          <el-button 
            v-if="can('update', 'equipment')"
            type="primary" 
            @click="handleEdit"
          >
            <template #icon>
              <Icon icon="mdi:pencil" :width="16" :height="16" />
            </template>
            Edit Equipment
          </el-button>
        </div>
      </div>
    </div>

    <!-- Equipment Details Content -->
    <div class="max-w-7xl mx-auto p-6">
      <el-skeleton :loading="loading" animated>
        <template #template>
          <div class="space-y-6">
            <el-skeleton-item variant="rect" style="width: 100%; height: 200px" />
            <el-skeleton-item variant="rect" style="width: 100%; height: 150px" />
            <el-skeleton-item variant="rect" style="width: 100%; height: 150px" />
          </div>
        </template>

        <template #default>
          <div v-if="equipment" class="space-y-6">
            <!-- Equipment Header Card -->
            <el-card class="detail-card" shadow="hover">
              <template #header>
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <Icon icon="mdi:wrench" class="text-blue-600 mr-2" :width="24" :height="24" />
                    <div>
                      <h2 class="text-xl font-semibold text-gray-900">{{ equipment.name }}</h2>
                      <p class="text-sm text-gray-500 mt-1">{{ equipment.manufacturer }} {{ equipment.model }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-4">
                    <!-- Critical Badge -->
                    <div v-if="equipment.is_critical" class="flex items-center space-x-2 bg-red-50 text-red-700 px-3 py-2 rounded-lg">
                      <el-icon class="text-red-600">
                        <Warning />
                      </el-icon>
                      <span class="font-medium">{{ $t('master.equipment.critical_yes') }}</span>
                    </div>
                    <!-- Status Badge -->
                    <div class="flex items-center space-x-2">
                      <el-icon :class="getEquipmentStatusIconClass(equipment.status)">
                        <component :is="getEquipmentStatusIcon(equipment.status)" />
                      </el-icon>
                      <el-tag :type="getEquipmentStatusTagType(equipment.status)" size="large">
                        {{ getEquipmentStatusLabel(equipment.status) }}
                      </el-tag>
                    </div>
                  </div>
                </div>
              </template>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Equipment Code -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.code') }}</label>
                  <div class="detail-value">
                    <el-tag type="info" class="font-mono">{{ equipment.code }}</el-tag>
                  </div>
                </div>

                <!-- Equipment Tag -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.tag') }}</label>
                  <div class="detail-value">
                    <el-tag class="font-mono">{{ equipment.tag }}</el-tag>
                  </div>
                </div>

                <!-- Vessel -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.vessel_name') }}</label>
                  <div class="detail-value">{{ equipment.vessel?.name || '-' }}</div>
                </div>

                <!-- Serial Number -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.serial_number') }}</label>
                  <div class="detail-value font-mono">{{ equipment.serial_number || '-' }}</div>
                </div>
              </div>
            </el-card>

            <!-- Equipment Type & Category -->
            <el-card class="detail-card" shadow="hover">
              <template #header>
                <div class="flex items-center">
                  <Icon icon="mdi:tag-outline" class="text-green-600 mr-2" :width="20" :height="20" />
                  <span class="font-semibold">Type & Category</span>
                </div>
              </template>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Equipment Type -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.type') }}</label>
                  <div class="detail-value">
                    <el-tag :type="getEquipmentTypeTagType(equipment.type)">
                      {{ getEquipmentTypeLabel(equipment.type) }}
                    </el-tag>
                  </div>
                </div>

                <!-- Category -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.category') }}</label>
                  <div class="detail-value">{{ equipment.category || '-' }}</div>
                </div>

                <!-- Sub Category -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.sub_category') }}</label>
                  <div class="detail-value">{{ equipment.sub_category || '-' }}</div>
                </div>
              </div>
            </el-card>

            <!-- Capacity & Performance -->
            <el-card class="detail-card" shadow="hover">
              <template #header>
                <div class="flex items-center">
                  <Icon icon="mdi:gauge" class="text-orange-600 mr-2" :width="20" :height="20" />
                  <span class="font-semibold">Capacity & Performance</span>
                </div>
              </template>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Design Capacity -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.design_capacity') }}</label>
                  <div class="detail-value">
                    <span v-if="equipment.design_capacity" class="font-medium text-blue-600">
                      {{ formatNumber(equipment.design_capacity) }} {{ equipment.capacity_unit }}
                    </span>
                    <span v-else class="text-gray-400">-</span>
                  </div>
                </div>

                <!-- Operating Capacity -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.operating_capacity') }}</label>
                  <div class="detail-value">
                    <span v-if="equipment.operating_capacity" class="font-medium text-green-600">
                      {{ formatNumber(equipment.operating_capacity) }} {{ equipment.capacity_unit }}
                    </span>
                    <span v-else class="text-gray-400">-</span>
                  </div>
                </div>

                <!-- Power Rating -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.power_rating_kw') }}</label>
                  <div class="detail-value">
                    <span v-if="equipment.power_rating_kw" class="font-medium text-purple-600">
                      {{ formatNumber(equipment.power_rating_kw) }} kW
                    </span>
                    <span v-else class="text-gray-400">-</span>
                  </div>
                </div>

                <!-- Capacity Utilization -->
                <div v-if="equipment.design_capacity && equipment.operating_capacity" class="detail-item">
                  <label class="detail-label">Capacity Utilization</label>
                  <div class="detail-value">
                    <el-progress 
                      :percentage="capacityUtilization" 
                      :color="getUtilizationColor(capacityUtilization)"
                      :stroke-width="8"
                      class="mt-2"
                    />
                    <span class="text-sm text-gray-600 mt-1 block">{{ capacityUtilization }}%</span>
                  </div>
                </div>
              </div>
            </el-card>

            <!-- Operating Conditions -->
            <el-card class="detail-card" shadow="hover">
              <template #header>
                <div class="flex items-center">
                  <Icon icon="mdi:thermometer" class="text-red-600 mr-2" :width="20" :height="20" />
                  <span class="font-semibold">Operating Conditions</span>
                </div>
              </template>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Design Pressure -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.design_pressure_psi') }}</label>
                  <div class="detail-value">
                    {{ equipment.design_pressure_psi ? `${formatNumber(equipment.design_pressure_psi)} PSI` : '-' }}
                  </div>
                </div>

                <!-- Operating Pressure -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.operating_pressure_psi') }}</label>
                  <div class="detail-value">
                    {{ equipment.operating_pressure_psi ? `${formatNumber(equipment.operating_pressure_psi)} PSI` : '-' }}
                  </div>
                </div>

                <!-- Design Temperature -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.design_temperature_f') }}</label>
                  <div class="detail-value">
                    {{ equipment.design_temperature_f ? `${formatNumber(equipment.design_temperature_f)} °F` : '-' }}
                  </div>
                </div>

                <!-- Operating Temperature -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.operating_temperature_f') }}</label>
                  <div class="detail-value">
                    {{ equipment.operating_temperature_f ? `${formatNumber(equipment.operating_temperature_f)} °F` : '-' }}
                  </div>
                </div>
              </div>
            </el-card>

            <!-- Dates & Timeline -->
            <el-card class="detail-card" shadow="hover">
              <template #header>
                <div class="flex items-center">
                  <Icon icon="mdi:calendar" class="text-purple-600 mr-2" :width="20" :height="20" />
                  <span class="font-semibold">Dates & Timeline</span>
                </div>
              </template>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Purchase Date -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.purchase_date') }}</label>
                  <div class="detail-value">{{ formatDate(equipment.purchase_date) || '-' }}</div>
                </div>

                <!-- Installation Date -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.installation_date') }}</label>
                  <div class="detail-value">{{ formatDate(equipment.installation_date) || '-' }}</div>
                </div>

                <!-- Commissioning Date -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.commissioning_date') }}</label>
                  <div class="detail-value">{{ formatDate(equipment.commissioning_date) || '-' }}</div>
                </div>

                <!-- Warranty Expiry -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.warranty_expiry_date') }}</label>
                  <div class="detail-value">
                    <span :class="getWarrantyStatusColor(equipment.warranty_expiry_date)">
                      {{ formatDate(equipment.warranty_expiry_date) || '-' }}
                    </span>
                  </div>
                </div>
              </div>
            </el-card>

            <!-- Maintenance & Criticality -->
            <el-card class="detail-card" shadow="hover">
              <template #header>
                <div class="flex items-center">
                  <Icon icon="mdi:tools" class="text-teal-600 mr-2" :width="20" :height="20" />
                  <span class="font-semibold">Maintenance & Criticality</span>
                </div>
              </template>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Criticality Ranking -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.criticality_ranking') }}</label>
                  <div class="detail-value">
                    <el-tag 
                      v-if="equipment.criticality_ranking" 
                      :type="getCriticalityTagType(equipment.criticality_ranking)"
                      size="large"
                    >
                      {{ getCriticalityLabel(equipment.criticality_ranking) }}
                    </el-tag>
                    <span v-else class="text-gray-400">-</span>
                  </div>
                </div>

                <!-- Has Spare -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.has_spare') }}</label>
                  <div class="detail-value">
                    <el-tag :type="equipment.has_spare ? 'success' : 'info'" size="large">
                      {{ equipment.has_spare ? 'Available' : 'Not Available' }}
                    </el-tag>
                  </div>
                </div>

                <!-- Maintenance Interval -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.maintenance_interval_hours') }}</label>
                  <div class="detail-value">
                    {{ equipment.maintenance_interval_hours ? `${formatNumber(equipment.maintenance_interval_hours)} Hours` : '-' }}
                  </div>
                </div>

                <!-- Last Maintenance -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.last_maintenance_date') }}</label>
                  <div class="detail-value">{{ formatDate(equipment.last_maintenance_date) || '-' }}</div>
                </div>

                <!-- Next Maintenance -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.next_maintenance_date') }}</label>
                  <div class="detail-value">
                    <span :class="getMaintenanceStatusColor(equipment.next_maintenance_date)">
                      {{ formatDate(equipment.next_maintenance_date) || '-' }}
                    </span>
                  </div>
                </div>
              </div>
            </el-card>

            <!-- References & Documentation -->
            <el-card class="detail-card" shadow="hover">
              <template #header>
                <div class="flex items-center">
                  <Icon icon="mdi:file-document" class="text-indigo-600 mr-2" :width="20" :height="20" />
                  <span class="font-semibold">References & Documentation</span>
                </div>
              </template>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- P&ID Reference -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.pid_reference') }}</label>
                  <div class="detail-value font-mono">{{ equipment.pid_reference || '-' }}</div>
                </div>

                <!-- Isometric Reference -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.isometric_reference') }}</label>
                  <div class="detail-value font-mono">{{ equipment.isometric_reference || '-' }}</div>
                </div>

                <!-- Manual Location -->
                <div class="detail-item">
                  <label class="detail-label">{{ $t('master.equipment.fields.manual_location') }}</label>
                  <div class="detail-value">{{ equipment.manual_location || '-' }}</div>
                </div>
              </div>
            </el-card>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6">
              <el-button type="default" @click="handleBack" size="large">
                <template #icon>
                  <Icon icon="mdi:arrow-left" :width="16" :height="16" />
                </template>
                Back to List
              </el-button>

              <div class="flex items-center space-x-3">
                <el-button type="warning" plain @click="handlePrint" size="large">
                  <template #icon>
                    <Icon icon="mdi:printer" :width="16" :height="16" />
                  </template>
                  Print Details
                </el-button>
                <el-button 
                  v-if="can('update', 'equipment')"
                  type="primary" 
                  @click="handleEdit"
                  size="large"
                >
                  <template #icon>
                    <Icon icon="mdi:pencil" :width="16" :height="16" />
                  </template>
                  Edit Equipment
                </el-button>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-12">
            <Icon icon="mdi:alert" class="text-gray-400" :width="48" :height="48" />
            <p class="text-gray-500 mt-4">Equipment not found</p>
          </div>
        </template>
      </el-skeleton>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { Icon } from '@iconify/vue'
import { ElMessage } from 'element-plus'
import { useAbility } from '@casl/vue'
import { useFormatter } from '@/composables/common/useFormatter'
import { Warning, CircleCheckFilled, CircleClose, Minus, Setting } from '@element-plus/icons-vue'

const { formatDate, formatNumber } = useFormatter()
const { t } = useI18n()
const { can } = useAbility()
const router = useRouter()
const route = useRoute()

// Reactive data
const loading = ref(false)
const equipment = ref(null)

// Computed properties
const capacityUtilization = computed(() => {
  if (!equipment.value?.design_capacity || !equipment.value?.operating_capacity) return 0
  return Math.round((equipment.value.operating_capacity / equipment.value.design_capacity) * 100)
})

// Methods
const handleBack = () => {
  router.push({ name: 'master.equipment.index' })
}

const handleEdit = () => {
  router.push({ name: 'master.equipment.edit', params: { id: route.params.id } })
}

const handlePrint = () => {
  window.print()
}

// Equipment Type Tag Types
const getEquipmentTypeTagType = (type) => {
  const typeMap = {
    'gas_turbine': 'danger',
    'compressor': 'primary', 
    'pump': 'success',
    'separator': 'warning',
    'heat_exchanger': 'info',
    'valve': '',
    'instrument': 'primary',
    'electrical': 'warning',
    'structural': 'info',
    'piping': ''
  }
  return typeMap[type] || ''
}

// Equipment Type Labels
const getEquipmentTypeLabel = (type) => {
  const equipmentTypes = {
    'gas_turbine': 'Gas Turbine',
    'compressor': 'Compressor',
    'pump': 'Pump',
    'separator': 'Separator',
    'heat_exchanger': 'Heat Exchanger',
    'valve': 'Valve',
    'instrument': 'Instrument',
    'electrical': 'Electrical',
    'structural': 'Structural',
    'piping': 'Piping'
  }
  return equipmentTypes[type] || type || '-'
}

// Equipment Status Tag Types
const getEquipmentStatusTagType = (status) => {
  const statusMap = {
    'running': 'success',
    'stopped': 'danger',
    'standby': 'warning',
    'maintenance': 'info',
    'out_of_service': 'danger'
  }
  return statusMap[status] || ''
}

// Equipment Status Labels
const getEquipmentStatusLabel = (status) => {
  const equipmentStatuses = {
    'running': 'Running',
    'stopped': 'Stopped',
    'standby': 'Standby',
    'maintenance': 'Maintenance',
    'out_of_service': 'Out of Service'
  }
  return equipmentStatuses[status] || status || '-'
}

// Status Icons
const getEquipmentStatusIcon = (status) => {
  const iconMap = {
    'running': CircleCheckFilled,
    'stopped': CircleClose,
    'standby': CircleCheckFilled,
    'maintenance': Setting,
    'out_of_service': Minus
  }
  return iconMap[status] || CircleCheckFilled
}

// Status Icon Classes
const getEquipmentStatusIconClass = (status) => {
  const classMap = {
    'running': 'text-green-500',
    'stopped': 'text-red-500',
    'standby': 'text-orange-500',
    'maintenance': 'text-blue-500',
    'out_of_service': 'text-gray-500'
  }
  return classMap[status] || 'text-gray-400'
}

// Criticality Methods
const getCriticalityTagType = (ranking) => {
  const rankingMap = {
    'a': 'danger',
    'b': 'warning',
    'c': 'info',
    'd': ''
  }
  return rankingMap[ranking] || ''
}

const getCriticalityLabel = (ranking) => {
  const rankings = {
    'a': t('master.equipment.criticality_rankings.a'),
    'b': t('master.equipment.criticality_rankings.b'),
    'c': t('master.equipment.criticality_rankings.c'),
    'd': t('master.equipment.criticality_rankings.d')
  }
  return rankings[ranking] || ranking || '-'
}

// Utility Methods
const getUtilizationColor = (percentage) => {
  if (percentage >= 90) return '#ef4444' // red
  if (percentage >= 75) return '#f59e0b' // amber
  if (percentage >= 50) return '#10b981' // emerald
  return '#6b7280' // gray
}

const getWarrantyStatusColor = (date) => {
  if (!date) return 'text-gray-400'
  const warrantyDate = new Date(date)
  const today = new Date()
  const diffDays = Math.ceil((warrantyDate - today) / (1000 * 60 * 60 * 24))
  
  if (diffDays < 0) return 'text-red-600 font-medium' // Expired
  if (diffDays <= 90) return 'text-orange-600 font-medium' // Expiring soon
  return 'text-green-600' // Valid
}

const getMaintenanceStatusColor = (date) => {
  if (!date) return 'text-gray-400'
  const maintenanceDate = new Date(date)
  const today = new Date()
  const diffDays = Math.ceil((maintenanceDate - today) / (1000 * 60 * 60 * 24))
  
  if (diffDays < 0) return 'text-red-600 font-medium' // Overdue
  if (diffDays <= 30) return 'text-orange-600 font-medium' // Due soon
  return 'text-green-600' // Scheduled
}

const loadEquipmentData = async () => {
  loading.value = true
  
  try {
    // Simulate API call - Replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 800))
    
    // Sample equipment data - matching database structure
    const sampleEquipment = {
      id: route.params.id,
      vessel_id: 1,
      vessel: {
        id: 1,
        name: 'FPU Trunojoyo-01',
        code: 'FPU-TJ01'
      },
      code: 'EQ-001',
      tag: 'GT-6640A',
      name: 'Gas Turbine Generator A',
      type: 'gas_turbine',
      category: 'Production',
      sub_category: 'Power Generation',
      manufacturer: 'Solar Turbines',
      model: 'Centaur 40',
      serial_number: 'SN123456789',
      design_capacity: 6.6,
      operating_capacity: 6.0,
      capacity_unit: 'MW',
      power_rating_kw: 6600,
      purchase_date: '2021-03-15',
      installation_date: '2021-08-20',
      commissioning_date: '2021-09-15',
      warranty_expiry_date: '2024-09-15',
      design_pressure_psi: 450,
      operating_pressure_psi: 400,
      design_temperature_f: 1200,
      operating_temperature_f: 1100,
      maintenance_interval_hours: 8760,
      last_maintenance_date: '2024-01-15',
      next_maintenance_date: '2025-01-15',
      is_critical: true,
      criticality_ranking: 'a',
      has_spare: false,
      pid_reference: 'PID-GT-001-Rev3',
      isometric_reference: 'ISO-GT-6640A',
      manual_location: 'Library Shelf A-1',
      status: 'running',
      created_at: '2024-01-01T00:00:00Z',
      updated_at: '2024-01-15T10:30:00Z'
    }
    
    equipment.value = sampleEquipment
  } catch (error) {
    ElMessage.error('Failed to load equipment details')
    console.error('Error loading equipment:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadEquipmentData()
})
</script>

<style scoped>
.equipment-detail {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.detail-card {
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}

:deep(.detail-card .el-card__header) {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border-bottom: 1px solid #e5e7eb;
  padding: 16px 20px;
}

:deep(.detail-card .el-card__body) {
  padding: 24px;
}

.detail-item {
  @apply space-y-2;
}

.detail-label {
  @apply text-sm font-semibold text-gray-600 uppercase tracking-wide;
}

.detail-value {
  @apply text-base text-gray-900 font-medium;
}

/* Print Styles */
@media print {
  .equipment-detail {
    background: white !important;
  }
  
  .detail-card {
    box-shadow: none !important;
    border: 1px solid #e5e7eb !important;
    break-inside: avoid;
    margin-bottom: 20px;
  }
  
  /* Hide action buttons when printing */
  .flex.items-center.justify-between.pt-6,
  .bg-white.border-b.border-gray-200.px-6.py-4 .flex.items-center.space-x-3 {
    display: none !important;
  }
}

/* Enhanced spacing for detail items */
.detail-item + .detail-item {
  @apply mt-4;
}

/* Custom tag styling */
:deep(.el-tag) {
  @apply font-medium;
}

/* Progress bar styling */
:deep(.el-progress-bar__outer) {
  background-color: #f3f4f6;
  border-radius: 4px;
}

:deep(.el-progress-bar__inner) {
  border-radius: 4px;
}
</style>