<template>
  <div class="content">
    <PageHeader :title="$t('production.sales_gas.detail')" :show-back="true" @back="onBack">
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
            {{ data.vessel_name ?? $t('production.sales_gas.detail') }}
          </h3>
          <p class="text-xs text-gray-500">
            {{ $t('common.labels.last_updated') }}:
            <span>{{ formatDate(data.updated_at || data.reading_time) }}</span>
          </p>
        </div>
        <div class="flex items-center gap-2">
          <el-tag type="success" size="small">Active</el-tag>
          <el-tag type="info" size="small" effect="plain">Sales Gas</el-tag>
        </div>
      </div>

      <!-- Card Body -->
      <div class="p-6 space-y-8">
        <!-- Section: Vessel Information -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.sales_gas.sections.vessel_information') || 'Vessel Information' }}
          </h4>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.vessel_name')">
                {{ data.vessel_name ?? '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.buyer_name')">
                {{ data.buyer_name ?? '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.reading_time')">
                {{ formatDate(data.reading_time) }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.recorded_by')">
                {{ data.recorded_by ?? '-' }}
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Volume & Flow -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.sales_gas.sections.volume_flow') || 'Volume & Flow' }}
          </h4>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.total_volume_mmscf')">
                <span class="text-lg font-semibold text-blue-600">
                  {{ data.total_volume_mmscf ? `${data.total_volume_mmscf} MMSCF` : '-' }}
                </span>
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.flowrate_mmscfd')">
                <span class="text-lg font-semibold text-green-600">
                  {{ data.flowrate_mmscfd ? `${data.flowrate_mmscfd} MMSCFD` : '-' }}
                </span>
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.nomination_mmscf')">
                <span class="text-lg font-semibold text-orange-600">
                  {{ data.nomination_mmscf ? `${data.nomination_mmscf} MMSCF` : '-' }}
                </span>
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.actual_delivery_mmscf')">
                <span class="text-lg font-semibold text-purple-600">
                  {{ data.actual_delivery_mmscf ? `${data.actual_delivery_mmscf} MMSCF` : '-' }}
                </span>
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Export Parameters -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.sales_gas.sections.export_parameters') || 'Export Parameters' }}
          </h4>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.export_pressure_psi')">
                {{ data.export_pressure_psi ? `${data.export_pressure_psi} PSI` : '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.export_temp_f')">
                {{ data.export_temp_f ? `${data.export_temp_f} °F` : '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.variance_percent')">
                <span :class="data.variance_percent > 0 ? 'text-red-600' : data.variance_percent < 0 ? 'text-green-600' : 'text-gray-600'">
                  {{ data.variance_percent ? `${data.variance_percent}%` : '-' }}
                </span>
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Gas Properties -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.sales_gas.sections.gas_properties') || 'Gas Properties' }}
          </h4>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.heating_value_btu_scf')">
                {{ data.heating_value_btu_scf ? `${data.heating_value_btu_scf} BTU/SCF` : '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.specific_gravity')">
                {{ data.specific_gravity ?? '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.co2_content_percent')">
                {{ data.co2_content_percent ? `${data.co2_content_percent}%` : '-' }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas.fields.h2s_content_ppm')">
                {{ data.h2s_content_ppm ? `${data.h2s_content_ppm} ppm` : '-' }}
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Remarks -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.sales_gas.fields.remarks') }}
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
import { salesGasApi } from '@/api/production/sales_gas'

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const { formatDate } = useFormatter();
// State
const isLoading = ref(false)
const data = ref({})

// Load sales gas data
const loadSalesGas = async () => {
  try {
    isLoading.value = true
    const response = await salesGasApi.getById(route.params.id)
    data.value = response.data.data
  } catch (error) {
    console.error('Failed to load sales gas:', error)
    ElMessage.error(t('common.messages.load_error'))
    onBack()
  } finally {
    isLoading.value = false
  }
}

// Actions
const onBack = () => {
  router.push({ name: 'production.sales_gas.index' })
}

const onEdit = () => {
  router.push({ name: 'production.sales_gas.edit', params: { id: route.params.id } })
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
    
    await salesGasApi.delete(route.params.id)
    ElMessage.success(t('common.messages.delete_success'))
    onBack()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('Failed to delete sales gas:', error)
      ElMessage.error(t('common.messages.delete_error'))
    }
  }
}

// Initialize
onMounted(() => {
  loadSalesGas()
})
</script>