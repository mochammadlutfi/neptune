<template>
    <div class="content">
        <PageHeader :title="$t('master.wells.detail')" :show-back="true" @back="onBack">
            <template #actions>
                <el-button type="primary" @click="onEdit" :icon="Edit">
                    {{ $t('common.actions.edit') }}
                </el-button>
                <el-button type="danger" @click="onDelete" :icon="Delete">
                    {{ $t('common.actions.delete') }}
                </el-button>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 2xl:grid-cols-10 gap-6">
            <el-card class="!shadow-lg 2xl:col-span-7" v-loading="isLoading">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <div class="mb-4">
                        <field-detail :label="$t('master.wells.fields.vessel_id')">
                            <div class="flex items-center space-x-2" v-if="data.vessel">
                                <el-icon class="text-blue-600">
                                    <Icon icon="fluent:vehicle-ship-24-filled" />
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
                        <field-detail :label="$t('master.wells.fields.code')">
                            {{ data.code }}
                        </field-detail>
                    </div>
                    <div class="mb-4">
                        <field-detail :label="$t('master.wells.fields.name')">
                            {{ data.name }}
                        </field-detail>
                    </div>
                    <div class="mb-4">
                        <field-detail :label="$t('master.wells.fields.type')">
                            <el-tag :type="getWellTypeTagType(data.type)" size="small" v-if="data.type">
                                {{ data.type }}
                            </el-tag>
                            <span v-else>-</span>
                        </field-detail>
                    </div>
                    <div class="mb-4">
                        <field-detail :label="$t('master.wells.fields.max_oil_rate')">
                            <span v-if="data.max_oil_rate">{{ data.max_oil_rate }} BPH</span>
                            <span v-else>-</span>
                        </field-detail>
                    </div>
                    <div class="mb-4">
                        <field-detail :label="$t('master.wells.fields.max_gas_rate')">
                            <span v-if="data.max_gas_rate">{{ data.max_gas_rate }} MSCFH</span>
                            <span v-else>-</span>
                        </field-detail>
                    </div>
                    <div class="mb-4">
                        <field-detail :label="$t('master.wells.fields.max_water_rate')">
                            <span v-if="data.max_water_rate">{{ data.max_water_rate }} BPH</span>
                            <span v-else>-</span>
                        </field-detail>
                    </div>
                    <div class="mb-4">
                        <field-detail :label="$t('master.wells.fields.status')">
                            <el-tag :type="getStatusTagType(data.status)" size="small" v-if="data.status">
                                {{ data.status }}
                            </el-tag>
                            <span v-else>-</span>
                        </field-detail>
                    </div>
                </div>
            </el-card>
            <el-card class="!shadow-lg 2xl:col-span-3">
              Timeline
            </el-card>
        </div>
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

import { wellsApi } from '@/api/master/wells'

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
    'Production': 'success',
    'Injection': 'primary',
    'Gas Lift' : 'danger',
    'Water Injection' : 'info',
    'Observation' : 'secondary',
    'Exploration': 'warning',
    'Appraisal': 'info',
    'Other': 'default'
  }
  return typeMap[type] || 'info'
}


const getStatusTagType = (status) => {
  const statusMap = {
    'Active': 'success',
    'Shut in': 'warning',
    'Suspended': 'info',
    'Abandoned': 'danger',
    'Drilling': 'primary'
  }
  return statusMap[status] || 'info'
}
// Actions
const onBack = () => {
  router.push({ name: 'master.wells.index' })
}

const onEdit = () => {
  router.push({ name: 'master.wells.edit', params: { id: route.params.id } })
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