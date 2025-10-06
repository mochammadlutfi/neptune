<template>
  <div class="content">
    <PageHeader :title="$t('production.wells.detail')" :show-back="true" @back="onBack">
      <template #actions>
        <el-button type="primary" @click="onEdit" :icon="Edit">
          {{ $t('common.actions.edit') }}
        </el-button>
        <el-button type="danger" @click="onDelete" :icon="Delete">
          {{ $t('common.actions.delete') }}
        </el-button>
      </template>
    </PageHeader>

    <!-- Single Card (consistent with other pages) -->
    <el-card class="!shadow-lg" v-loading="isLoading" body-class="!p-0">
      <!-- Card Header -->
      <div class="flex items-center justify-between px-6 pt-5 pb-3 border-b">
        <div>
          <h3 class="text-base font-semibold">
            {{ data.well_name ?? $t('production.wells.detail') }}
          </h3>
          <p class="text-xs text-gray-500">
            {{ $t('common.labels.last_updated') }}:
            <span>{{ formatDate(data.updated_at || data.recorded_date) }}</span>
          </p>
        </div>
        <div class="flex items-center gap-2">
          <el-tag :type="getStatusTagType(data.status)" size="small">{{ data.status ?? '-' }}</el-tag>
          <el-tag :type="getWellTypeTagType(data.well_type)" size="small" effect="plain">
            {{ data.well_type ?? '-' }}
          </el-tag>
        </div>
      </div>

      <!-- Card Body -->
      <div class="p-6 space-y-8">
        <!-- Section: Well & Vessel -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.wells.sections.well_vessel_information') || 'Well & Vessel' }}
          </h4>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.vessel_name')">
                {{ data.vessel_name ?? '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.well_name')">
                {{ data.well_name ?? '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.well_code')">
                {{ data.well_code ?? '-' }}
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Recording Info -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.wells.sections.recording_information') || 'Recording Information' }}
          </h4>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.recorded_date')">
                {{ formatDate(data.recorded_date) }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.recorded_time')">
                {{ data.recorded_time ?? '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.shift')">
                <el-tag :type="data.shift === 'Day' ? 'warning' : 'info'" size="small">
                  {{ data.shift ?? '-' }}
                </el-tag>
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.recorded_by')">
                {{ data.recorded_by ?? '-' }}
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Production Rates -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.wells.sections.rates') || 'Production Rates' }}
          </h4>
          <el-row :gutter="24">
            <el-col :lg="8" :md="8" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.oil_rate_bph')">
                <span class="text-lg font-semibold text-green-600">
                  {{ data.oil_rate_bph ? `${data.oil_rate_bph} BPH` : '-' }}
                </span>
              </field-detail>
            </el-col>
            <el-col :lg="8" :md="8" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.gas_rate_mscfh')">
                <span class="text-lg font-semibold text-blue-600">
                  {{ data.gas_rate_mscfh ? `${data.gas_rate_mscfh} MSCFH` : '-' }}
                </span>
              </field-detail>
            </el-col>
            <el-col :lg="8" :md="8" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.water_rate_bph')">
                <span class="text-lg font-semibold text-cyan-600">
                  {{ data.water_rate_bph ? `${data.water_rate_bph} BPH` : '-' }}
                </span>
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Well Parameters -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.wells.sections.parameters') || 'Well Parameters' }}
          </h4>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.wellhead_pressure_psi')">
                {{ data.wellhead_pressure_psi ? `${data.wellhead_pressure_psi} PSI` : '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.wellhead_temperature_f')">
                {{ data.wellhead_temperature_f ? `${data.wellhead_temperature_f} °F` : '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.choke_size_64th')">
                {{ data.choke_size_64th ? `${data.choke_size_64th}/64"` : '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.flow_hours')">
                {{ data.flow_hours ? `${data.flow_hours} Hours` : '-' }}
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Fluid Properties -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.wells.sections.fluid_properties') || 'Fluid Properties' }}
          </h4>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.api_gravity')">
                {{ data.api_gravity ? `${data.api_gravity} °API` : '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.wells.fields.bs_w_percent')">
                {{ data.bs_w_percent ? `${data.bs_w_percent}%` : '-' }}
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Remarks -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.wells.fields.remarks') }}
          </h4>
          <div class="rounded-lg border p-4 bg-gray-50 min-h-[56px]">
            <div class="whitespace-pre-wrap text-sm">
              {{ data.remarks ?? '-' }}
            </div>
          </div>
        </section>
      </div>
    </el-card>
  </div>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Edit, Delete } from '@element-plus/icons-vue'

import PageHeader from '@/components/PageHeader.vue'
import FieldDetail from '@/components/FieldDetail.vue'

// import 
import { useFormatter } from '@/composables/common/useFormatter'
import { wellsApi } from '@/api/production/wells'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const { formatDate } = useFormatter();
// State
const isLoading = ref(false)
const data = ref({})

// Load well data
const loadWell = async () => {
  try {
    isLoading.value = true
    const response = await wellsApi.getById(route.params.id)
    data.value = response.data.data
  } catch (error) {
    console.error('Failed to load well:', error)
    ElMessage.error(t('common.messages.load_error'))
    onBack()
  } finally {
    isLoading.value = false
  }
}

// Helper methods untuk Wells
const getWellTypeTagType = (type) => {
  const typeMap = {
    'production': 'success',
    'injection': 'primary',
    'gas_lift' : 'danger',
    'water_injection' : 'info',
    'observation' : 'secondary',
    'exploration': 'warning',
    'appraisal': 'info',
    'other': 'default'
  }
  return typeMap[type] || 'info'
}


const getStatusTagType = (status) => {
  const statusMap = {
    'active': 'success',
    'shut-in': 'warning',
    'suspended': 'info',
    'abandoned': 'danger',
    'drilling': 'primary'
  }
  return statusMap[status] || 'info'
}
// Actions
const onBack = () => {
  router.push({ name: 'production.wells.index' })
}

const onEdit = () => {
  router.push({ name: 'production.wells.edit', params: { id: route.params.id } })
}

const onDelete = async () => {
  try {
    await ElMessageBox.confirm(
      t('common.messages.delete_confirm'),
      t('common.actions.delete'),
      {
        confirmButtonText: t('common.actions.delete'),
        cancelButtonText: t('common.actions.cancel'),
        type: 'warning',
        confirmButtonClass: 'el-button--danger'
      }
    )
    
    await wellsApi.delete(route.params.id)
    ElMessage.success(t('common.messages.delete_success'))
    onBack()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Failed to delete well:', error)
      ElMessage.error(t('common.messages.delete_error'))
    }
  }
}

// Initialize
onMounted(() => {
  loadWell()
})
</script>