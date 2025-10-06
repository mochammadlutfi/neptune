<template>
  <div class="content">
    <!-- Modern Page Header -->
    <PageHeader
        :title="$t('master.vessels.detail')"
        :secondary-actions="[
            {
                key: 'edit',
                label: $t('common.actions.edit'),
                icon: 'mingcute:edit-line',
                click: onEdit,
                permission: 'update'
            },
            {
                key: 'back',
                label: $t('common.actions.back'),
                icon: 'mingcute:arrow-left-line',
                click: goBack
            }
        ]"
    />

    <!-- Content Area -->
    <el-card class="!shadow-lg" v-loading="isLoading">
      <el-row :gutter="24">
        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
          <field-detail :label="$t('master.vessels.fields.code')">
            {{ data.code }}
          </field-detail>
        </el-col>
        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
          <field-detail :label="$t('master.vessels.fields.name')">
            {{ data.name }}
          </field-detail>
        </el-col>
        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
          <field-detail :label="$t('master.vessels.fields.type')">
            {{ data.type }}
          </field-detail>
        </el-col>
        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
          <field-detail :label="$t('master.vessels.fields.operator')">
            {{ data.operator }}
          </field-detail>
        </el-col>
        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
          <field-detail :label="$t('master.vessels.fields.field_name')">
            {{ data.field_name ?? '-' }}
          </field-detail>
        </el-col>
        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
          <field-detail :label="$t('master.vessels.fields.status')">
            <el-tag :type="getStatusTagType(data.status)" size="small">
                {{ data.status }}
            </el-tag>
          </field-detail>
        </el-col>
      </el-row>
    </el-card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import { useQuery } from '@tanstack/vue-query'
import { useFormatter } from '@/composables/common/useFormatter'
import { useAbility } from '@casl/vue'

// Import components
import PageHeader from '@/components/PageHeader.vue'
import FieldDetail from '@/components/FieldDetail.vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const { formatDate, formatNumber } = useFormatter()
const { can } = useAbility()

// Vessel types options
const vesselTypes = {
    'fpu': t('master.vessels.types.fpu'),
    'fpso': t('master.vessels.types.fpso'),
    'fso': t('master.vessels.types.fso'),
    'drill_ship': t('master.vessels.types.drill_ship'),
    'jack_up': t('master.vessels.types.jack_up'),
    'semi_sub': t('master.vessels.types.semi_sub'),
    'platform': t('master.vessels.types.platform')
}

const getStatusTagType = (status) => {
  const statusMap = {
    'Active': 'success',
    'Standby': 'warning',
    'Maintenance': 'info',
    'Decommissioned': 'danger'
  };
  return statusMap[status] || '';
};

// Data fetching
const fetchVessel = async () => {
  const response = await axios.get(`/master/vessels/${route.params.id}`)
  return response.data.data
}

const { data, isLoading, isError, error } = useQuery({
  queryKey: ['vessel', route.params.id],
  queryFn: fetchVessel,
  onError: (error) => {
    console.error('Error fetching vessel:', error)
    ElMessage.error(t('common.errors.operation_failed'))
    goBack()
  }
})

// Helper methods
const getTypeTagType = (type) => {
  const typeMap = {
    'FPU': 'primary',
    'FPSO': 'success',
    'FSO': 'warning',
    'Drill Ship': 'info',
    'Jack Up': 'danger',
    'Semi Sub': ''
  }
  return typeMap[type] || ''
}

// Event handlers
const onEdit = () => {
  router.push({ name: 'master.vessels.edit', params: { id: route.params.id } })
}

const goBack = () => {
  router.push({ name: 'master.vessels.index' })
}
</script>

<style scoped>
/* Add any specific styles for this page here */
</style>