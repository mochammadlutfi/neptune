<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('master.vessels.title')" :primary-action="{
              label: $t('master.vessels.create'),
              icon: 'mingcute:add-line',
              click: onCreate
          }" />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <TableControls v-model:search="params.q" :search-placeholder="$t('common.actions.search')"
                :loading="isLoading" :selected-rows="selectedRows" @refresh="refetch" @bulk-export="bulkExport"
                @bulk-delete="bulkDelete" />

            <!-- Content Area -->
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <SkeletonTable />
                </template>
                <template #default>
                    <div class="overflow-x-auto">
                        <el-table class="base-table" :data="data?.data || []" @sort-change="sortChange"
                            @selection-change="handleSelectionChange" row-key="id" stripe>
                            <el-table-column type="selection" width="50" align="center" />
                            <el-table-column prop="code" :label="$t('master.vessels.fields.code')" width="120" sortable
                                fixed="left">
                                <template #default="scope">
                                    <el-tag type="info" size="small">{{ scope.row.code }}</el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="name" :label="$t('master.vessels.fields.name')" min-width="180"
                                sortable show-overflow-tooltip>
                                <template #default="scope">
                                    <div class="flex items-center space-x-2">
                                        <el-icon class="text-blue-600">
                                            <Icon icon="fluent:vehicle-ship-24-filled"/>
                                        </el-icon>
                                        <router-link :to="`/master/vessels/${scope.row.id}`"
                                            class="font-medium text-blue-600 hover:underline">
                                            {{ scope.row.name }}
                                        </router-link>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="type" :label="$t('master.vessels.fields.type')" width="120" sortable>
                                <template #default="{ row }">
                                    <el-tag :type="getTypeTagType(row.type)" size="small">
                                        {{ row.type }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="field_name" :label="$t('master.vessels.fields.field_name')"
                                min-width="160" show-overflow-tooltip>
                              <template #default="scope">
                                <div class="text-sm">
                                  <span v-if="scope.row.field_name">
                                    {{ scope.row.field_name }}
                                  </span>
                                  <span v-else class="text-gray-400">-</span>
                                </div>
                              </template>
                            </el-table-column>

                            <el-table-column prop="status" :label="$t('master.vessels.fields.status')" width="120"
                                sortable>
                                <template #default="scope">
                                    <el-tag :type="getStatusTagType(scope.row.status)" size="small">
                                        {{ scope.row.status }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column :label="$t('common.actions.action', 2)" align="center" width="120"
                                fixed="right">
                                <template #default="scope">
                                    <el-dropdown popper-class="dropdown-action" placement="bottom-end" trigger="click">
                                        <el-button circle class="!p-0 !m-0">
                                            <Icon icon="mingcute:more-2-fill" />
                                        </el-button>
                                        <template #dropdown>
                                            <el-dropdown-menu>
                                                <el-dropdown-item @click="onView(scope.row)">
                                                    <Icon icon="mingcute:eye-line" class="me-2" />
                                                    {{ $t('common.actions.view') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item @click="onEdit(scope.row)"
                                                    v-if="can('update', 'vessel')">
                                                    <Icon icon="mingcute:edit-line" class="me-2" />
                                                    {{ $t('common.actions.edit') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item divided class="text-red-600"
                                                    @click="onDelete(scope.row)" v-if="can('delete', 'vessel')">
                                                    <Icon icon="mingcute:delete-2-line" class="me-2" />
                                                    {{ $t('common.actions.delete') }}
                                                </el-dropdown-item>
                                            </el-dropdown-menu>
                                        </template>
                                    </el-dropdown>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>

                    <!-- Pagination -->
                    <Pagination :current-page="data?.meta?.current_page || 1" :per-page="data?.meta?.per_page || 25"
                        :total="data?.meta?.total || 0" :last-page="data?.meta?.last_page || 1"
                        @page-change="handlePageChange" @per-page-change="handlePerPageChange" />
                </template>
            </el-skeleton>
        </el-card>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import _ from 'lodash';
import { useQuery } from '@tanstack/vue-query';
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/common/useFormatter';
import { useRouter } from 'vue-router';
import { useAbility } from '@casl/vue';
import { Ship } from '@element-plus/icons-vue';
// Import our reusable components
import PageHeader from '@/components/PageHeader.vue';
import TableControls from '@/components/TableControls.vue';
import Pagination from '@/components/Pagination.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';

const { formatDate, formatNumber } = useFormatter();
const { t } = useI18n();
const { can } = useAbility();
const router = useRouter();

// Page stats
const viewMode = ref('table');
const selectedRows = ref([]);
const isLoadingStats = ref(false);
const stats = ref({});

const vesselTypes = {
  fpu: t('master.vessels.types.fpu'),
  fpso: t('master.vessels.types.fpso'),
  fso: t('master.vessels.types.fso'),
  drill_ship: t('master.vessels.types.drill_ship'),
  jack_up: t('master.vessels.types.jack_up'),
  semi_sub: t('master.vessels.types.semi_sub'),
  platform: t('master.vessels.types.platform')
};

// Filter state
const params = ref({
  limit: 25,
  page: 1,
  q: "",
  sort: 'name',
  sortDir: 'asc',
  name: null,
  code: null,
  type: null,
  status: null,
  operator: null,
  field_name: null
});

const advancedFilters = ref({
  type: null,
  status: null,
  operator: null,
  field_name: null
});

// Helper methods
const getStatusType = (status) => {
  switch (status) {
      case 'Active': return 'success';
      case 'Standby': return 'warning';
      case 'Maintenance': return 'info';
      case 'Decommissioned': return 'danger';
      default: return '';
  }
};

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/master/vessels", {
    params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['MasterIndexVessels', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'master.vessels.create' });
};

const doSearch = _.debounce(() => {
  params.value.page = 1;
  refetch();
}, 1000);

const sortChange = (sortObj) => {
  params.value.sort = sortObj.prop;
  params.value.sortDir = sortObj.order === 'ascending' ? 'asc' : 'desc';
  refetch();
};

const handlePageChange = (page) => {
  params.value.page = page;
  refetch();
};

const handlePerPageChange = (perPage) => {
  params.value.limit = perPage;
  params.value.page = 1;
  refetch();
};

const handleSelectionChange = (selection) => {
  selectedRows.value = selection;
};

const getTypeTagType = (type) => {
  const typeMap = {
    'FPU': 'primary',
    'FPSO': 'success',
    'FSO': 'warning',
    'Drill Ship': 'info',
    'Jack Up': 'danger',
    'Semi Sub': ''
  };
  return typeMap[type] || '';
};

const getStatusTagType = (status) => {
  const statusMap = {
    'Active': 'success',
    'Standby': 'warning',
    'Maintenance': 'info',
    'Decommissioned': 'danger'
  };
  return statusMap[status] || '';
};

const bulkExport = (rows) => {
  ElMessage.info(t('common.messages.feature_unavailable'));
};

const bulkDelete = (rows) => {
  ElMessageBox.confirm(
      t('common.confirmations.confirm_delete_multiple', { count: rows.length }),
      t('common.confirmations.are_you_sure'),
      {
          confirmButtonText: t("common.actions.confirm"),
          cancelButtonText: t("common.actions.cancel"),
          type: 'warning',
      }
  ).then(() => {
      ElMessage.info(t('common.messages.feature_unavailable'));
  });
};

const onView = (row) => {
  router.push({ name: 'master.vessels.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'master.vessels.edit', params: { id: row.id } });
};

const onDelete = async (row) => {
  try {
      await ElMessageBox.confirm(
          t('common.confirmations.confirm_delete'),
          t('common.confirmations.are_you_sure'),
          {
              confirmButtonText: t('common.actions.delete'),
              cancelButtonText: t('common.actions.cancel'),
              type: 'warning',
          }
      );

      await axios.delete(`/master/vessels/${row.id}`);
      ElMessage.success(t('common.success.item_deleted'));
      refetch();
  } catch (error) {
      if (error !== 'cancel') {
          ElMessage.error(t('common.errors.delete_failed'));
      }
  }
};

// Watchers
watch(() => params.value.q, () => {
  doSearch();
});

// Lifecycle
onMounted(() => {
  refetch();
});
</script>

<style scoped>
/* Add any specific styles for this page here */
</style>