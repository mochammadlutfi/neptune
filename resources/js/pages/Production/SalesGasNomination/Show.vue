<template>
  <div class="content">
    <PageHeader :title="$t('production.sales_gas_nomination.detail')" :show-back="true" @back="onBack">
      <template #actions>
        <el-dropdown popper-class="dropdown-action" placement="bottom-end" trigger="click">
            <el-button circle class="!p-0 !m-0">
                <Icon icon="mingcute:more-2-fill" />
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item @click="onEdit">
                        <Icon icon="mingcute:edit-line" class="me-2" />
                        {{ $t('common.actions.edit') }}
                    </el-dropdown-item>
                    <el-dropdown-item class="text-red-600" @click="onDelete">
                        <Icon icon="mingcute:delete-2-line" class="me-2" />
                        {{ $t('common.actions.delete') }}
                    </el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
      </template>
    </PageHeader>

    <!-- Single Card (consistent with other pages) -->
    <el-card class="!shadow-lg" v-loading="isLoading" body-class="!p-0">
      <!-- Card Header -->
      <div class="flex items-center justify-between px-6 pt-5 pb-3 border-b">
        <div>
          <h3 class="text-base font-semibold">
            {{ data.name ?? $t('production.sales_gas_nomination.detail') }}
          </h3>
          <p class="text-xs text-gray-500">
            {{ $t('common.fields.last_updated_at') }}:
            <span>{{ formatDate(data.updated_at || data.recorded_date) }}</span>
          </p>
        </div>
        <div>
          <div class="text-right">
            <el-tag :type="getStatusTagType(data.status)">{{ $t(`production.sales_gas_nomination.status.${data.status}`) ?? '-' }}</el-tag>
          </div>
          <p class="text-xs text-gray-500">
            {{ $t('common.fields.created_by') }}:
            <span>{{ data.created_by?.name ?? '-' }}</span>
          </p>
        </div>
      </div>

      <!-- Card Body -->
      <div class="p-6 space-y-8">
        <!-- Section: Well & Vessel -->
        <section>
          <el-row :gutter="24">
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas_nomination.fields.vessel_id')">
                <div class="flex items-center space-x-2" v-if="data.vessel">
                    <el-icon class="text-blue-600">
                      <Icon icon="fluent:vehicle-ship-24-filled"/>
                    </el-icon>
                    <router-link :to="`/master/vessels/${data.vessel_id}`"
                        class="font-medium text-blue-600 hover:underline">
                        {{ data.vessel?.name || '-' }}
                    </router-link>
                </div>
                <span v-else>-</span>
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas_nomination.fields.date')">
                {{ formatDate(data.date) }}
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas_nomination.fields.total_nomination')">
                {{ formatNumber(data.total_nomination, 2) ?? '-' }} MMSCF
              </field-detail>
            </el-col>
            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
              <field-detail :label="$t('production.sales_gas_nomination.fields.total_confirmed')">
                {{ formatNumber(data.total_confirmed, 2) ?? '-' }} MMSCF
              </field-detail>
            </el-col>
          </el-row>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Recording Info -->
        <section>
            <div class="overflow-x-auto">
              <el-table class="base-table" :data="data.lines || []">
                <el-table-column
                  :label="$t('production.sales_gas_nomination.fields.gas_buyer')"
                  prop="buyer.name"
                />
                <el-table-column
                  :label="$t('production.sales_gas_nomination.fields.nomination')">
                  <template #default="scope">
                    <span class="font-mono">{{ formatNumber(scope.row.nomination,2) }} MMSCF</span>
                  </template>
                </el-table-column>
                <el-table-column
                  :label="$t('production.sales_gas_nomination.fields.confirmed')">
                  <template #default="scope">
                    <span class="font-mono">{{ formatNumber(scope.row.confirmed,2) }} MMSCF</span>
                  </template>
                </el-table-column>
              </el-table>
            </div>
        </section>

        <el-divider class="!my-0" />

        <!-- Section: Remarks -->
        <section>
          <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-4">
            {{ $t('production.sales_gas_nomination.fields.remarks') }}
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

const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const { formatDate, formatNumber } = useFormatter();
// State
const isLoading = ref(false)
const data = ref({})

// Load well data
const loadWell = async () => {
  try {
    isLoading.value = true
    const response = await axios.get(`/production/sales-gas-nomination/${route.params.id}`)
    data.value = response.data.data
  } catch (error) {
    console.error('Failed to load well:', error)
    ElMessage.error(t('common.messages.load_error'))
    onBack()
  } finally {
    isLoading.value = false
  }
}


const getStatusTagType = (status) => {
  const statusMap = {
    'draft': 'warning',
    'confirmed': 'success',
    'cancel' : "danger"
  }
  return statusMap[status] || 'info'
}
// Actions
const onBack = () => {
  router.push({ name: 'production.sales_gas_nomination.index' })
}

const onEdit = () => {
  router.push({ name: 'production.sales_gas_nomination.edit', params: { id: route.params.id } })
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