<template>
    <div class="content">
        <PageHeader :title="$t('master.equipment.detail')" :show-back="true" @back="onBack">
            <template #actions>
                <el-button type="primary" @click="onEdit" :icon="Edit">
                    {{ $t('common.actions.edit') }}
                </el-button>
                <el-button type="danger" @click="onDelete" :icon="Delete">
                    {{ $t('common.actions.delete') }}
                </el-button>
            </template>
        </PageHeader>
        
        <el-card class="!shadow-lg" v-loading="isLoading">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.vessel_id')">
                <div class="flex items-center space-x-2" v-if="data.vessel">
                    <el-icon class="text-blue-600">
                        <Icon icon="fluent:vehicle-ship-24-filled"/>
                    </el-icon>
                    <router-link :to="`/master/vessels/${data.vessel_id}`"
                        class="font-medium text-blue-600 hover:underline">
                        {{ data.vessel?.name ?? '-' }}
                    </router-link>
                </div>
                <span v-else>-</span>
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.code')">
                {{ data.code }}
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.tag')">
                {{ data.tag ?? '-' }}
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.name')">
                {{ data.name }}
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.category')">
                {{ data.category ?? '-' }}
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.type')">
                <el-tag :type="getWellTypeTagType(data.type)" size="small" v-if="data.type">
                    {{ data.type }}
                </el-tag>
                <span v-else>-</span>
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.manufacturer')">
                {{ data.manufacturer ?? '-' }}
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.model')">
                {{ data.model ?? '-' }}
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.serial_number')">
                {{ data.serial_number ?? '-' }}
              </field-detail>
            </div>
            <div class="mb-4">
              <field-detail :label="$t('master.equipment.fields.installation_date')">
                {{ data.installation_date ? formatDate(data.installation_date) : '-' }}
              </field-detail>
            </div>
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
import { useFormatter } from '@/composables/common/useFormatter';

import { equipmentApi } from '@/api/master/equipment'

const { formatDate, formatNumber } = useFormatter();
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

// State
const isLoading = ref(false)
const data = ref({})

// Load well data
const loadWell = async () => {
  try {
    isLoading.value = true
    const response = await equipmentApi.getById(route.params.id)
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
    'runnig': 'success',
    'standby': 'warning',
    'maintenance': 'info',
    'abandoned': 'danger',
    'drilling': 'primary'
  }
  return statusMap[status] || 'info'
}
// Actions
const onBack = () => {
  router.push({ name: 'master.equipment.index' })
}

const onEdit = () => {
  router.push({ name: 'master.equipment.edit', params: { id: route.params.id } })
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